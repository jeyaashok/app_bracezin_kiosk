<?php

namespace Tji\Helpers;

use Request;
use Arr;
use Str;
use Validator;
use ErrorResponse;

class Validation {

    public function checkOn($input, $datas = []) {
        if(!($datas && is_array($datas) && count($datas) > 0)) { return; }
        $validateDatas = [];
        $factor = [];
        foreach($datas as $key => $data) {
            switch($key) {
                case 'name':
                    $validateDatas[$key] = $data ?: 'required|string|min:4|max:30';
                    $factor['required'] = 'name';
                    break;
                case 'mobile':
                    $validateDatas[$key] = $data ?: 'required|string|min:8|max:12';
                    $factor['required'] = 'mobile';
                    break;
                case 'email':
                    $validateDatas[$key] = $data ?: 'required|string|email|min:4|max:50';
                    $factor['required'] = 'email';
                    break;
                case 'otp':
                    $validateDatas[$key] = $data ?: 'required|string|min:4|max:4';
                    $factor['required'] = 'otp';
                    break;
                case 'mac':
                    $validateDatas[$key] = $data ?: 'required|string|min:17|max:17';
                    $factor['required'] = 'mac';
                    break;
                default:
                    $validateDatas[$key] = $data ?: 'required';
                    $factor['required'] = $key;
                    break;
            }
        }
        $validator = Validator::make($input, $validateDatas);
        if ($validator->fails()) {
            $error = $validator->errors()->first();
            throw new ErrorResponse($error,405,'info', [$factor]);
        }
        return;
    }

    public function isPermittedOtp($otp) {
        $permittedOtp = env('PERMITTED_OTP', null);
        if(is_null($permittedOtp)) { return false; }
        return ($permittedOtp && $permittedOtp === $otp) ? true : false;
    }

}
