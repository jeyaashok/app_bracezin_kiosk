<?php

namespace Tji\Http\Middleware;

use Arr;
use Closure;
use ErrorResponse;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Sys;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthUserMiddleware
{
    private const DEVICE_COMPUTER = 'computer';

    private const DEVICE_ANDROID_MOBILE = 'android_mobile';

    private const DEVICE_IOS_MOBILE = 'ios_mobile';

    private const DEVICE_DISPLAY_PANEL = 'display_panel';

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        Sys::setAuthUser();
        $authUser = Sys::authUser();
        if (is_null($authUser)) {
            $authUser = (auth('admin')->check()) ? auth('admin')->user() : null;
        }

        $sysAdmin = ($authUser && $authUser->is_sysAdmin) ? true : false;

        if (is_null($authUser)) {
            return $next($request);
        }

        if ($this->isTokenForcedToLogout($request, $authUser)) {
            auth('admin')->logout();
            throw new ErrorResponse('UnAuthorized Access, Token Not Valid', 401, 'info');
        }

        if ($sysAdmin) {
            return $next($request);
        } else {
            $userType = ($authUser && $authUser->type) ? $authUser->type : null;

            if (! $userType) {
                return $next($request);
            }

            $deviceType = $this->resolveDeviceType($request);
            $authConfig = $this->resolveAuthenticatableConfig($request, $deviceType);

            if (Arr::has($authConfig, $userType) && $authConfig[$userType]) {
                return $next($request);
            }

            if (! $this->expectsApiResponse($request)) {
                if (Route::has('activated')) {
                    return redirect()->route('activated');
                }

                return redirect('/');
            }
            auth('admin')->logout();
            throw new ErrorResponse('UnAuthorized Access, Token Not Valid', 401, 'info');
        }
    }

    private function resolveDeviceType(Request $request): string
    {
        $requestedDevice = (string) $request->header('X-Device-Type', $request->input('device_type', ''));
        $normalizedRequestedDevice = $this->normalizeDeviceAlias($requestedDevice);

        if ($normalizedRequestedDevice) {
            return $normalizedRequestedDevice;
        }

        $userAgent = strtolower((string) $request->userAgent());

        if (str_contains($userAgent, 'android')) {
            return self::DEVICE_ANDROID_MOBILE;
        }

        if (
            str_contains($userAgent, 'iphone') ||
            str_contains($userAgent, 'ipad') ||
            str_contains($userAgent, 'ipod') ||
            str_contains($userAgent, 'ios')
        ) {
            return self::DEVICE_IOS_MOBILE;
        }

        if (
            str_contains($userAgent, 'smart-tv') ||
            str_contains($userAgent, 'smarttv') ||
            str_contains($userAgent, 'hbbtv') ||
            str_contains($userAgent, 'tizen') ||
            str_contains($userAgent, 'webos') ||
            str_contains($userAgent, 'signage') ||
            str_contains($userAgent, 'display') ||
            str_contains($userAgent, 'kiosk')
        ) {
            return self::DEVICE_DISPLAY_PANEL;
        }

        return self::DEVICE_COMPUTER;
    }

    private function normalizeDeviceAlias(string $deviceType): ?string
    {
        $normalized = strtolower(trim($deviceType));

        return match ($normalized) {
            'computer', 'desktop', 'laptop', 'web' => self::DEVICE_COMPUTER,
            'android', 'android_mobile', 'android-mobile' => self::DEVICE_ANDROID_MOBILE,
            'ios', 'iphone', 'ipad', 'ios_mobile', 'ios-mobile' => self::DEVICE_IOS_MOBILE,
            'display', 'display_panel', 'display-panel', 'panel', 'kiosk' => self::DEVICE_DISPLAY_PANEL,
            default => null,
        };
    }

    private function resolveAuthenticatableConfig(Request $request, string $deviceType): array
    {
        $deviceConfig = config('userConfig.device_authenticatable', []);

        if (Arr::has($deviceConfig, $deviceType) && is_array($deviceConfig[$deviceType])) {
            return $deviceConfig[$deviceType];
        }

        return match ($deviceType) {
            self::DEVICE_ANDROID_MOBILE, self::DEVICE_IOS_MOBILE => config('userConfig.mobile_authenticatable', []),
            self::DEVICE_DISPLAY_PANEL => config('userConfig.display_authenticatable', []),
            default => $request->is('api/*')
                ? config('userConfig.api_authenticatable', config('userConfig.web_authenticatable', []))
                : config('userConfig.web_authenticatable', []),
        };
    }

    private function expectsApiResponse(Request $request): bool
    {
        return $request->is('api/*') || $request->expectsJson();
    }

    private function isTokenForcedToLogout(Request $request, $authUser): bool
    {
        if (! $request->is('api/*') && ! $request->expectsJson()) {
            return false;
        }

        $forcedLogoutAt = $authUser->force_logout_at ?? null;
        if (is_null($forcedLogoutAt)) {
            return false;
        }

        try {
            $payload = JWTAuth::parseToken()->getPayload();
            $issuedAt = $payload->get('iat');

            if (! $issuedAt) {
                return false;
            }

            return Carbon::createFromTimestamp((int) $issuedAt)->lessThanOrEqualTo(Carbon::parse($forcedLogoutAt));
        } catch (JWTException $e) {
            return false;
        }
    }
}
