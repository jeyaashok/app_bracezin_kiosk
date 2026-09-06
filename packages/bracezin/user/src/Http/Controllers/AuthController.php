<?php

namespace User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Route;
use Arr;
use DB;
use ErrorResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Notification;
use Otp;
use Person;
use Str;
use Tji;
use UserFcd;
use Validation;

class AuthController extends Controller
{
    public function authMe(Request $request)
    {
        $userId = @auth('admin')->user()->id;
        $userFormatted = ($userId) ? UserFcd::GetFormattedData($userId) : null;

        return Tji::showResponse($request, ['data' => $userFormatted]);
    }

    public function register(Request $request)
    {
        $input = $request->all();
        $hasEmail = Arr::has($input, 'email') && ! empty($input['email']);
        $hasUsername = Arr::has($input, 'username') && ! empty($input['username']);
        $hasMobile = Arr::has($input, 'mobile') && ! empty($input['mobile']);

        if (! $hasEmail && ! $hasUsername && ! $hasMobile) {
            throw new ErrorResponse('Any one of Email, User Name or Mobile Number is required', 405, 'info');
        }
        if ($hasEmail) {
            $this->validateEmailPassword($request);
        }
        if ($hasUsername) {
            $this->validateUsernamePassword($request);
        }
        if ($hasMobile) {
            $this->validateMobilePassword($request);
        }

        try {
            $type = Arr::has($input, 'type') ? Str::slug($input['type'], '-') : Str::slug(config('userConfig.default_user_type'), '-');
            $input['password'] = @$input['password'] ?: Config('userConfig.default_user_password', 'secret');
            $input['name'] = @$input['name'] ?: @$input['username'] ?: @$input['email'];
            if ($type && ($type === 'customer' || $type === 'company' || $type === 'agent')) {
                $input['type'] = $type;
                $user = User::create($input);
                if ($user && $user->id) {
                    $userFormatted = ($user) ? UserFcd::GetFormattedData($user->id) : null;
                    $userFormatted['token'] = auth()->guard('admin')->tokenById($user->id);

                    return Tji::successResponse($request, $userFormatted);
                }
            }
            throw new ErrorResponse('You May not have a Permission to Register on this Portal, Please Contact Admin', 405, 'info');
        } catch (Exception $e) {
            return Tji::errorResponse($request, $e);
        }
    }

    public function login(Request $request)
    {
        $input = $request->all();
        $withToken = true;
        try {
            $apiRequest = $request->is('api/*');
            $user = $this->loginWithCredentials($request);
            if ($user && $user->id && $apiRequest) {
                $userFormatted = ($user) ? UserFcd::GetFormattedData($user->id) : null;
                if ($withToken) {
                    $userFormatted['token'] = auth()->guard('admin')->tokenById($user->id);
                }

                return Tji::successResponse($request, $userFormatted);
            } elseif ($user && ! $apiRequest) {
                $userToken = ($user && $withToken) ? auth()->guard('admin')->tokenById($user->id) : null;
                $token = ['token' => $userToken];

                return Tji::successResponse($request, $token);
            } else {
                throw new ErrorResponse('You May not have a Permission to Access this Portal, Please Contact Admin', 405, 'info');
            }
        } catch (Exception $e) {
            return Tji::errorResponse($request, $e);
        }
    }

    public function loginWithCredentials(Request $request)
    {
        $input = $request->all();
        $isAuthenticable = $this->loginWithEmailPassword($request);
        if (! $isAuthenticable) {
            $isAuthenticable = $this->loginWithUsernamePassword($request);
        }
        if (! $isAuthenticable) {
            $isAuthenticable = $this->loginWithMobilePassword($request);
        }

        $user = ($isAuthenticable) ? auth('admin')->user() : null;
        $this->clearOldTokens($user);
        if (! $this->checkActive($user, $request)) {
            throw new ErrorResponse('Sorry Your Account has been Deactivated, Please Contact Support', 405, 'info');
        }

        return $user;
    }

    public function forgetPassword(Request $request)
    {
        $input = $request->all();
        try {
            $apiRequest = $request->is('api/*');
            $user = User::where('email', '=', $input['email'])->first();
            if ($user && $user->id) {
                $user->update(['do_change_password' => 1, 'do_reset_password' => 1]);
                $mail = Notification::sendLoginOtpMail($user);
                $user = UserFcd::GetFormattedData($user->id);
            }

            if ($user && $user->id && $apiRequest) {
                return Tji::successResponse($request, $user);
            } elseif ($user && ! $apiRequest) {
                return Tji::successResponse($request, $user);
            } else {
                throw new ErrorResponse('You May not have a Permission to Access this Portal, Please Contact Admin', 405, 'info');
            }
        } catch (Exception $e) {

            return Tji::errorResponse($request, $e);
        }
    }

    public function resetPassword(Request $request)
    {
        $input = $request->all();
        $email = @$input['email'] ?: null;
        $mobile = @$input['mobile'] ?: null;
        $username = @$input['username'] ?: null;
        $otp = @$input['otp'] ?: null;
        $apiRequest = $request->is('api/*');
        try {
            $user = ($email) ? User::where('email', '=', $email)->first() : null;
            $user = ($mobile && ! ($user && $user->id)) ? User::where('mobile', '=', $mobile)->first() : $user;
            $user = ($username && ! ($user && $user->id)) ? User::where('username', '=', $username)->first() : $user;
            if ($user && $user->id) {
                if (is_null($otp)) {
                    throw new ErrorResponse('OTP Required, Enter Valid OTP.', 405, 'info');
                }
                if (! ($user->otp && $user->otp === $otp)) {
                    throw new ErrorResponse('Please Enter Valid OTP.', 405, 'info');
                }
                User::where('id', '=', $user->id)->update(['do_change_password' => 1, 'do_reset_password' => 0]);
                Otp::removeOtp($user);
                $this->clearOldTokens($user);
                if ($apiRequest) {
                    $userFormatted = ($user) ? UserFcd::GetFormattedData($user->id) : null;
                    $userFormatted['token'] = auth()->guard('admin')->tokenById($user->id);

                    return Tji::successResponse($request, $userFormatted);
                } elseif (! $apiRequest) {
                    $userToken = ($user) ? auth()->guard('admin')->tokenById($user->id) : null;
                    $token = ['token' => $userToken];

                    return Tji::successResponse($request, $token);
                } else {
                    throw new ErrorResponse('You May not have a Permission to Access this Portal, Please Contact Admin', 405, 'info');
                }
            } else {
                throw new ErrorResponse('You May not have a Permission to Access this Portal, Please Contact Admin', 405, 'info');
            }
        } catch (Exception $e) {
            return Tji::errorResponse($request, $e);
        }
    }

    public function validateEmailPassword(Request $request)
    {
        $input = $request->all();
        if (! Arr::has($input, 'email') || empty($input['email'])) {
            return;
        }
        $validator = Validation::checkOn($input, ['email' => 'required|string|email|min:4|max:50|unique:users,email']);
    }

    public function loginWithEmailPassword(Request $request)
    {
        $isAuthenticable = false;
        $input = $request->all();
        $credentials = $request->only('email', 'password');
        if (Arr::has($input, 'email') && Arr::has($input, 'password')) {
            if (User::where('email', $input['email'])->count() == 0) {
                throw new ErrorResponse('Invalid Email', 405, 'info');
            }
            if (! $isAuthenticable = auth('admin')->attempt($credentials)) {
                throw new ErrorResponse('Password Incorrect', 405, 'info');
            }
        }

        return $isAuthenticable;
    }

    public function loginWithMobile(Request $request)
    {
        $isAuthenticable = false;
        $input = $request->all();
        $credentials = $request->only('mobile');
        if (Arr::has($input, 'mobile')) {
            if (User::where('mobile', $input['mobile'])->count() == 0) {
                throw new ErrorResponse('Invalid Mobile', 405, 'info');
            }
        }

        return $isAuthenticable;
    }

    public function validateUsernamePassword(Request $request)
    {
        $input = $request->all();
        if (! Arr::has($input, 'username') || empty($input['username'])) {
            return;
        }
        $validator = Validation::checkOn($input, ['username' => 'required|string|min:4|max:50|unique:users,username']);
    }

    public function loginWithUsernamePassword(Request $request)
    {
        $isAuthenticable = false;
        $input = $request->all();
        $credentials = $request->only('username', 'password');
        if (Arr::has($input, 'username') && Arr::has($input, 'password')) {
            if (User::where('username', $input['username'])->count() == 0) {
                throw new ErrorResponse('Invalid User Name', 405, 'info');
            }
            if (! $isAuthenticable = auth('admin')->attempt($credentials)) {
                throw new ErrorResponse('Password Incorrect', 405, 'info');
            }
        }

        return $isAuthenticable;
    }

    public function validateMobilePassword(Request $request)
    {
        $input = $request->all();
        if (! Arr::has($input, 'mobile') || empty($input['mobile'])) {
            return;
        }
        $validator = Validation::checkOn($input, ['mobile' => 'required|string|min:8|max:12|unique:users,mobile']);
    }

    public function loginWithMobilePassword(Request $request)
    {
        $isAuthenticable = false;
        $input = $request->all();
        $credentials = $request->only('mobile', 'password');
        if (Arr::has($input, 'mobile') && Arr::has($input, 'password')) {
            if (User::where('mobile', $input['mobile'])->count() == 0) {
                throw new ErrorResponse('Invalid Mobile', 405, 'info');
            }
            if (! $isAuthenticable = auth('admin')->attempt($credentials)) {
                throw new ErrorResponse('Password Incorrect', 405, 'info');
            }
        }

        return $isAuthenticable;
    }

    public function validateMobile(Request $request)
    {
        $input = $request->all();
        $credentials = $request->only('mobile');
        if (Arr::has($input, 'mobile')) {
            $validator = Validation::checkOn($input, ['mobile' => 'required|string|min:8|max:12|unique:users,mobile']);
        }
    }

    public function validateMobileAndOtp(Request $request)
    {
        $input = $request->all();
        $credentials = $request->only('mobile', 'otp');
        $validator = Validation::checkOn($input, ['mobile' => 'required|string|min:8|max:12|unique:users,mobile', 'otp' => null]);
    }

    public function loginWithMobileAndOtpForCustomer(Request $request)
    {
        $isAuthenticable = false;
        $input = $request->all();
        $credentials = $request->only('mobile', 'otp');
        $credentials['type'] = 'customer';
        $credentials['password'] = @$input['otp'];
        if (Arr::has($input, 'mobile') && Arr::has($input, 'otp')) {
            if (User::where('mobile', $input['mobile'])
                ->where('type', 'customer')->count() == 0) {
                throw new ErrorResponse('Invalid Mobile Number', 405, 'info');
            }
            if (! $isAuthenticable = auth('admin')->attempt($credentials)) {
                throw new ErrorResponse('OTP not Valid', 405, 'info');
            }
        }
        $customer = Person::getCustomerByColumn('mobile', @$input['mobile']);
        if ($customer && $customer->id) {
            $customer->update(['otp' => null]);
            $this->changeUserStatusMobile($customer, true);
            // have to remove below 1 line after email verification process done
            $this->changeUserStatusEmail($customer, true);
        }

        return $isAuthenticable;
    }

    public function checkActive($user, Request $request)
    {
        if ($user && $user->is_active && $user->is_active > 0) {
            return true;
        } else {
            auth('admin')->logout();

            // Used only for Sanctum Driver
            // $user->tokens()->delete();
            return false;
        }
    }

    public function checkOtpVerifyActive($user)
    {
        if ($user && $user->is_mobile_verified && $user->is_mobile_verified > 0) {
            return true;
        } else {
            auth('admin')->logout();

            // $user->tokens()->delete();
            return false;
        }
    }

    public function clearOldTokens($user)
    {
        if ($user && $user->id) {
            // Used only for Sanctum Driver
            // $user->tokens()->delete();
        }

    }

    public function logout()
    {
        if (auth('admin')->check()) {
            $user = auth('admin')->user();
            auth('admin')->logout();

            return Tji::successMessage('Logout Successfully');
        }
        throw new ErrorResponse('Something went Wrong', 405, 'info');
    }

    public function deleteMyAccount()
    {
        DB::beginTransaction();
        try {
            if (auth('admin')->check()) {
                $userId = auth('admin')->user()->id;
                User::find($userId)->fill(['is_active' => 0])->save();
                auth('admin')->logout();
            } else {
                throw new Exception('Error: Please Login with to account and then try delete your account.');
            }
            DB::commit();

            return response()->json(['message' => 'success'], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            $error = $e->getMessage();

            return response()->json(compact('error'), 405);
        }
    }

    public function unauthorized()
    {
        throw new ErrorResponse('UnAuthorized Access, Token Not Valid', 401, 'info');
    }

    public function validateAuth()
    {
        if (! auth('admin')->check()) {
            throw new ErrorResponse('UnAuthorized Access, Token Not Valid', 401, 'info');
        }
    }

    public function forceLogout($userId)
    {
        DB::beginTransaction();
        try {
            $this->validateAuth();
            $user = User::find($userId);
            if ($user && $user->id) {
                // Used only for JWT_Token
                $user->force_logout_at = now();
                $user->save();
            } else {
                throw new ErrorResponse('No User Found', 405, 'info');
            }
            DB::commit();

            return Tji::successMessage('Force Logout Successfully');
        } catch (Exception $e) {
            DB::rollBack();

            return Tji::errorResponse(request(), $e);
        }
    }

    public function forceLogoutAllUser()
    {
        DB::beginTransaction();
        try {
            $this->validateAuth();
            $users = User::all();
            if ($users && $users->count() > 0) {
                // Used only for JWT_Token
                User::whereNotNull('id')->update(['force_logout_at' => now()]);
            }
            DB::commit();

            return Tji::successMessage('Logout Successfully Completed');
        } catch (Exception $e) {
            DB::rollBack();

            return Tji::errorResponse(request(), $e);
        }
    }

    public function changePassword(Request $request)
    {
        $input = $request->all();
        $password = Arr::has($input, 'password') ? $input['password'] : null;
        $confirmPassword = Arr::has($input, 'confirm_password') ? $input['confirm_password'] : null;
        if ($password && $confirmPassword && $password === $confirmPassword) {
            $authUser = auth('admin')->user();
            if ($authUser && $authUser->id) {
                $updateData = ['password' => $password, 'do_change_password' => 0, 'do_reset_password' => 0];
                $authUser->fill($updateData)->save();
            } else {
                throw new ErrorResponse('UnAuthorized Access, Token Not Available', 405, 'info');
            }
        } else {
            throw new ErrorResponse('Password and Confirm Password does not Matched.', 405, 'info');
        }

        return Tji::successMessage('Password Changed Successfully');
    }

    public function updatePassword(Request $request)
    {
        $input = $request->all();
        $password = Arr::has($input, 'password') ? $input['password'] : null;
        $userId = Arr::has($input, 'user_id') ? $input['user_id'] : null;
        if ($password && $userId) {
            $user = User::find($userId);
            if ($user && $user->id) {
                $updateData = ['password' => $password];
                $user->fill($updateData)->save();
            } else {
                throw new ErrorResponse('UnAuthorized Access, Token Not Available', 405, 'info');
            }
        } else {
            throw new ErrorResponse('Password and Confirm Password does not Matched.', 405, 'info');
        }

        return Tji::successMessage('Password Changed Successfully');
    }

    // To change user Active and Inactivate status
    public function changeUserActiveStatus($user, $status = true)
    {
        $authUser = auth('admin')->user();
        if ($authUser && $authUser->id && $user->id) {
            if ($user->is_active) {
                $updateData = ['is_active' => $status];
            } else {
                $updateData = ['is_active' => $status];
            }
            $user->fill($updateData)->save();
        } else {
            throw new ErrorResponse('UnAuthorized Access, Token Not Available', 405, 'info');
        }

        return Tji::successMessage('Status Changed Successfully');
    }
    // End of To change user Active and Inactivate status

    // To change user email verify active and inactive status
    public function changeUserStatusEmail($user, $status = true)
    {
        if ($user->id) {
            if ($user->is_email_verified) {
                $updateData = ['is_email_verified' => $status];
            } else {
                $updateData = ['is_email_verified' => $status];
            }
            $user->fill($updateData)->save();
        } else {
            throw new ErrorResponse('UnAuthorized Access, Token Not Available', 405, 'info');
        }

        return Tji::successMessage('Status Changed Successfully');
    }

    public function changeUserStatusMobile($user, $status = true)
    {
        if ($user->id) {
            if ($user->is_mobile_verified) {
                $updateData = ['is_mobile_verified' => $status, 'is_active' => true];
            } else {
                $updateData = ['is_mobile_verified' => $status, 'is_active' => true];
            }
            $user->fill($updateData)->save();
        } else {
            throw new ErrorResponse('UnAuthorized Access, Token Not Available', 405, 'info');
        }

        return Tji::successMessage('Status Changed Successfully');
    }

    // End of To change user mobile verify active and inactive status

    // public function mailVerifyUpdate(Request $request){
    //     $user = User::find($request->route('id'));

    //     $user->is_email_verified = 1;
    //     $user->save();

    //     $url = "https://ai-octopus.com/email-verify-success";
    //     return Redirect::intended($url);

    //     // return redirect(env('BASE_URL') . '/email/verify/success');
    // }

}
