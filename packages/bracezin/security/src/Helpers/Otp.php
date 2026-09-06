<?php

namespace Security\Helpers;

use Carbon\Carbon;
use ErrorResponse;
use Tji\Helpers\MainHelper;

class Otp extends MainHelper
{
    public function generateOtp($model)
    {
        $otp = null;

        $otpValue = null;

        if ($model && $model->id) {
            $recentOtp = $model->recentOtp();
            if ($recentOtp && $recentOtp->id) {
                if ($recentOtp->expired_at <= Carbon::now()) {
                    $this->otpRepository->forceDestroy($recentOtp->id);
                } else {
                    $otp = $recentOtp;
                }
            }
            if (! ($otp && $otp->id)) {
                // Temprory set Time (5)
                $storeData = [
                    'resource_id' => $model->id,
                    'resource_type' => $model->tableName,
                    'expired_at' => Carbon::now()->addMinutes(2000),
                    'value' => @$otpValue ?: sprintf('%04d', rand(0, 9999)),
                ];
                $otp = $this->otpRepository->store($storeData);
            }
        }

        return $otp;
    }

    public function reGenerateOtp($model)
    {
        $otpValue = null;
        if ($model && $model->id) {
            $do = $this->removeOtp($model);
            $storeData = [
                'resource_id' => $model->id,
                'resource_type' => $model->tableName,
                'expired_at' => Carbon::now()->addMinutes(5),
                'value' => @$otpValue ?: sprintf('%04d', rand(0, 9999)),
            ];
            $otp = $this->otpRepository->store($storeData);
            $otpValue = $otp->value;
        }

        return $otpValue;
    }

    public function validateOtp($model, $otpValue = null)
    {
        if (! $otpValue) {
            throw new ErrorResponse('OTP Required.', 405, 'info');
        }
        if ($model && $model->id) {
            $recentOtp = $model->recentOtp();
            if ($recentOtp && $recentOtp->id) {
                if ($recentOtp->expired_at <= Carbon::now()) {
                    $recentOtp->forceDelete();
                    throw new ErrorResponse('OTP Expired Time Out.', 405, 'info');
                } elseif ($recentOtp && $recentOtp->value === $otpValue) {
                    return true;
                } else {
                    throw new ErrorResponse('OTP Not Matched.', 405, 'info');
                }
            } else {
                throw new ErrorResponse('OTP Not Valid, Please try after sometimes.', 405, 'info');
            }
        }

        return false;
    }

    public function removeOtp($model)
    {
        if ($model && $model->id) {
            $searchData = [
                'resource_id' => $model->id,
                'resource_type' => $model->tableName,
            ];
            $otps = $this->otpRepository->index($searchData)->get();
            if ($otps && $otps->count() > 0) {
                foreach ($otps as $otp) {
                    $otp->forceDelete();
                }
            }
        }

        return true;
    }
}
