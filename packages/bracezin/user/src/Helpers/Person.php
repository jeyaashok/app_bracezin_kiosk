<?php

namespace User\Helpers;

use App\Models\User;
use Illuminate\Support\Arr;
use Setting;
use Tji\Helpers\MainHelper;
use Validation;

class Person extends MainHelper
{
    public function checkUserDetailFillable($input)
    {
        return $this->userDetailRepository->checkFillable($input);
    }

    public function updateOrCreateUserDetail($user, $input)
    {
        $updateData = array_filter($this->checkUserDetailFillable($input));
        if ($user && $user->id && $user->tableName === 'users' && $updateData && count($updateData) > 0) {
            $updateData['user_id'] = $user->id;
            $userDetail = $user->detail()->first();
            if ($userDetail && $userDetail->id) {
                $userDetail = $userDetail->update($updateData);
            } else {
                // $updateData['code'] = Setting::getCodeBySlug($user->type.' Prefix', $user->type.' Code');
                $updateData['code'] = Setting::getCodeWithTimestamp($user->type);
                $userDetail = $this->userDetailRepository->store($updateData);
                // Setting::incrementValue($user->type.' Code');
            }
        }
    }

    public function getCustomerByColumn($column, $value, $input = null)
    {
        $searchData = ['type' => 'customer'];
        $searchData[$column] = $value;
        $customer = @$this->allUserRepository->index($searchData)->first();
        $mobile = Arr::has($input, 'mobile') ? $input['mobile'] : null;
        $email = Arr::has($input, 'email') ? $input['email'] : null;
        $searchData['email'] = $email;
        if (! ($customer && $customer->id) && $email && $mobile) {
            $customer = $this->checkAndStoreCustomer($searchData);
        }

        return $customer;
    }

    public function checkAndStoreCustomer($input, $customerData = [])
    {
        $validation = Validation::checkOn($input, ['email' => null, 'mobile' => null]);
        $mobile = Arr::has($input, 'mobile') ? $input['mobile'] : null;
        $email = Arr::has($input, 'email') ? $input['email'] : null;
        if ($email) {
            $validation = Validation::checkOn($input, ['email' => 'required|string|email|min:4|max:50|unique:users,email']);
        }
        $input['username'] = (string) $mobile;
        $input['name'] = (string) $mobile;
        $input['mobile'] = (string) $mobile;
        $input['type'] = 'customer';
        $input['password'] = Config('userConfig.default_user_password', 'secret');
        $input = array_merge($input, $customerData);
        $customer = $this->allUserRepository->index(['mobile' => $mobile, 'type' => 'customer'])->first();
        if (! ($customer && $customer->id)) {
            $customer = $this->allUserRepository->store($input);
        } else {
            if (! $customer->is_active && $customer->email != $input['email']) {
                $customerData = $this->allUserRepository->checkFillable($input);
                $customer = User::where('id', $customer->id)
                    ->find($customer->id);
                $customer->email = @$input['email'];
                $customer->save();
            }
        }

        return $customer;
    }

    public function getNonCustomerUser($input)
    {
        $mobile = Arr::has($input, 'mobile') ? $input['mobile'] : null;
        $user = $this->allUserRepository->index(['mobile' => (string) $mobile])
            ->where('type', '!=', 'customer')
            ->first();

        return ($user && $user->id) ? $user : null;
    }

    public function getCustomerUser($input)
    {
        $mobile = Arr::has($input, 'mobile') ? $input['mobile'] : null;
        $user = $this->allUserRepository->index(['mobile' => (string) $mobile])
            ->where('type', '=', 'customer')
            ->first();

        return ($user && $user->id) ? $user : null;
    }
}
