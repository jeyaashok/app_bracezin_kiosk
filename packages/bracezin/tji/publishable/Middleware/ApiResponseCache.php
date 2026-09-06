<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class ApiResponseCache
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->canInvalidateOnWriteRequest($request)) {
            /** @var Response $response */
            $response = $next($request);

            if ($this->shouldInvalidateAfterResponse($response)) {
                $invalidatedCount = $this->clearTrackedCacheKeysForWriteRequest($request);

                if ($invalidatedCount > 0) {
                    $response->headers->set('X-Api-Cache-Invalidated', 'true');
                    $response->headers->set('X-Api-Cache-Invalidated-Count', (string) $invalidatedCount);
                }
            }

            return $response;
        }

        if (! $this->canCacheRequest($request)) {
            return $next($request);
        }

        $ttlSeconds = max((int) config('api_cache.ttl_seconds', 30), 1);
        $cacheKey = $this->cacheKey($request);
        $cached = Cache::get($cacheKey);

        if (is_array($cached)) {
            return $this->buildCachedResponse($cached, $ttlSeconds);
        }

        /** @var Response $response */
        $response = $next($request);

        if (! $this->canCacheResponse($response)) {
            return $response;
        }

        $cachePayload = [
            'status' => $response->getStatusCode(),
            'content' => $response->getContent(),
            'headers' => $this->cacheableHeaders($response),
        ];

        Cache::put($cacheKey, $cachePayload, now()->addSeconds($ttlSeconds));
        $this->trackCacheKey($cacheKey, $this->resourceScope($request));

        $response->headers->set('X-Api-Cache', 'MISS');
        $response->headers->set('Cache-Control', "private, max-age={$ttlSeconds}");

        return $response;
    }

    private function canCacheRequest(Request $request): bool
    {
        if (! (bool) config('api_cache.enabled', true)) {
            return false;
        }

        if (! $request->isMethod('GET')) {
            return false;
        }

        if ((bool) $request->boolean('no_cache') || $request->headers->has('X-Skip-Cache')) {
            return false;
        }

        if (! (bool) config('api_cache.include_authenticated', true) && $request->user() !== null) {
            return false;
        }

        foreach ((array) config('api_cache.ignored_paths', []) as $pathPattern) {
            if ($request->is($pathPattern)) {
                return false;
            }
        }

        return true;
    }

    private function canInvalidateOnWriteRequest(Request $request): bool
    {
        if (! (bool) config('api_cache.enabled', true)) {
            return false;
        }

        if (! (bool) config('api_cache.invalidate_on_write', true)) {
            return false;
        }

        if (! in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return false;
        }

        foreach ((array) config('api_cache.invalidation_ignored_paths', []) as $pathPattern) {
            if ($request->is($pathPattern)) {
                return false;
            }
        }

        return $request->is('api/*');
    }

    private function shouldInvalidateAfterResponse(Response $response): bool
    {
        if (! (bool) config('api_cache.invalidate_only_on_success', true)) {
            return true;
        }

        return $response->getStatusCode() < 400;
    }

    private function canCacheResponse(Response $response): bool
    {
        if ($response->getStatusCode() !== 200) {
            return false;
        }

        $contentType = (string) $response->headers->get('Content-Type', '');

        return str_contains(strtolower($contentType), 'application/json');
    }

    private function cacheKey(Request $request): string
    {
        $userId = $request->user()?->getAuthIdentifier() ?? 'guest';

        $keyParts = [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'user' => (string) $userId,
            'accept' => (string) $request->header('Accept', ''),
            'language' => (string) $request->header('Accept-Language', ''),
        ];

        return (string) config('api_cache.key_prefix', 'api-response-cache:').sha1(json_encode($keyParts));
    }

    private function cacheableHeaders(Response $response): array
    {
        $headers = [];

        foreach ($response->headers->all() as $name => $values) {
            if (in_array(strtolower($name), ['set-cookie', 'cache-control', 'pragma', 'expires'], true)) {
                continue;
            }

            $headers[$name] = $values;
        }

        return $headers;
    }

    private function buildCachedResponse(array $cached, int $ttlSeconds): Response
    {
        $response = response(
            $cached['content'] ?? null,
            (int) ($cached['status'] ?? 200)
        );

        foreach ((array) ($cached['headers'] ?? []) as $name => $values) {
            $response->headers->set($name, $values);
        }

        $response->headers->set('X-Api-Cache', 'HIT');
        $response->headers->set('Cache-Control', "private, max-age={$ttlSeconds}");

        return $response;
    }

    private function cacheIndexKey(): string
    {
        return (string) config('api_cache.key_prefix', 'api-response-cache:').'tracked-keys';
    }

    private function scopeIndexKey(string $scope): string
    {
        return (string) config('api_cache.key_prefix', 'api-response-cache:').'tracked-keys:scope:'.$scope;
    }

    private function scopeListIndexKey(): string
    {
        return (string) config('api_cache.key_prefix', 'api-response-cache:').'tracked-scopes';
    }

    private function trackCacheKey(string $cacheKey, string $scope): void
    {
        $indexKey = $this->cacheIndexKey();
        $keys = (array) Cache::get($indexKey, []);
        $scopeIndexKey = $this->scopeIndexKey($scope);
        $scopeKeys = (array) Cache::get($scopeIndexKey, []);
        $scopeListIndexKey = $this->scopeListIndexKey();
        $scopeList = (array) Cache::get($scopeListIndexKey, []);

        if (! in_array($cacheKey, $keys, true)) {
            $keys[] = $cacheKey;
            Cache::forever($indexKey, array_values(array_unique($keys)));
        }

        if (! in_array($cacheKey, $scopeKeys, true)) {
            $scopeKeys[] = $cacheKey;
            Cache::forever($scopeIndexKey, array_values(array_unique($scopeKeys)));
        }

        if (! in_array($scope, $scopeList, true)) {
            $scopeList[] = $scope;
            Cache::forever($scopeListIndexKey, array_values(array_unique($scopeList)));
        }
    }

    private function clearTrackedCacheKeys(): void
    {
        $indexKey = $this->cacheIndexKey();
        $keys = (array) Cache::get($indexKey, []);
        $scopeListIndexKey = $this->scopeListIndexKey();
        $scopes = (array) Cache::get($scopeListIndexKey, []);

        foreach ($keys as $key) {
            Cache::forget((string) $key);
        }

        foreach ($scopes as $scope) {
            Cache::forget($this->scopeIndexKey((string) $scope));
        }

        Cache::forget($indexKey);
        Cache::forget($scopeListIndexKey);
    }

    private function clearTrackedCacheKeysForWriteRequest(Request $request): int
    {
        if (! (bool) config('api_cache.granular_invalidation', true)) {
            $keys = (array) Cache::get($this->cacheIndexKey(), []);
            $this->clearTrackedCacheKeys();

            return count($keys);
        }

        $scopes = $this->scopesForWriteRequest($request);
        $invalidatedCount = 0;

        foreach ($scopes as $scope) {
            $invalidatedCount += $this->clearScopeKeys($scope);
        }

        if ($invalidatedCount > 0) {
            return $invalidatedCount;
        }

        if ((bool) config('api_cache.invalidate_fallback_to_all', true)) {
            $allKeys = (array) Cache::get($this->cacheIndexKey(), []);
            $this->clearTrackedCacheKeys();

            return count($allKeys);
        }

        return 0;
    }

    private function clearScopeKeys(string $scope): int
    {
        $scopeIndexKey = $this->scopeIndexKey($scope);
        $scopeKeys = (array) Cache::get($scopeIndexKey, []);

        if (count($scopeKeys) === 0) {
            return 0;
        }

        foreach ($scopeKeys as $key) {
            Cache::forget((string) $key);
        }

        $this->removeFromGlobalIndex($scopeKeys);
        $this->removeFromScopeList($scope);
        Cache::forget($scopeIndexKey);

        return count($scopeKeys);
    }

    private function removeFromGlobalIndex(array $keysToRemove): void
    {
        $indexKey = $this->cacheIndexKey();
        $globalKeys = (array) Cache::get($indexKey, []);

        if (count($globalKeys) === 0) {
            return;
        }

        $filtered = array_values(array_diff($globalKeys, $keysToRemove));

        if (count($filtered) === 0) {
            Cache::forget($indexKey);

            return;
        }

        Cache::forever($indexKey, $filtered);
    }

    private function removeFromScopeList(string $scope): void
    {
        $scopeListIndexKey = $this->scopeListIndexKey();
        $scopes = (array) Cache::get($scopeListIndexKey, []);

        if (count($scopes) === 0) {
            return;
        }

        $filtered = array_values(array_filter($scopes, fn ($item) => (string) $item !== $scope));

        if (count($filtered) === 0) {
            Cache::forget($scopeListIndexKey);

            return;
        }

        Cache::forever($scopeListIndexKey, $filtered);
    }

    private function scopesForWriteRequest(Request $request): array
    {
        $scopes = [$this->resourceScope($request)];

        foreach ((array) config('api_cache.invalidation_scope_map', []) as $pathPattern => $mappedScopes) {
            if (! is_string($pathPattern) || ! $request->is($pathPattern)) {
                continue;
            }

            foreach ((array) $mappedScopes as $mappedScope) {
                if (! is_string($mappedScope) || trim($mappedScope) === '') {
                    continue;
                }

                $scopes[] = strtolower(trim($mappedScope));
            }
        }

        return array_values(array_unique($scopes));
    }

    private function resourceScope(Request $request): string
    {
        $path = trim($request->path(), '/');

        if ($path === '') {
            return 'root';
        }

        $segments = explode('/', $path);

        if (($segments[0] ?? '') === 'api') {
            array_shift($segments);
        }

        $version = null;
        if (isset($segments[0]) && preg_match('/^v\d+$/i', $segments[0])) {
            $version = strtolower((string) array_shift($segments));
        }

        $resource = strtolower((string) ($segments[0] ?? 'root'));

        if ($version !== null) {
            return $version.'/'.$resource;
        }

        return $resource;
    }
}