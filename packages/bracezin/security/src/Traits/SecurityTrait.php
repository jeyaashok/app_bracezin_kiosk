<?php

namespace Security\Traits;

use Otp;
use Qr;

/******** Index *********/
trait SecurityTrait
{
    public function qr()
    {
        if ($this && $this->id && $this->qr_id) {
            return $this->belongsTo('Security\Models\Qr', 'qr_id');
        }
        if ($this && $this->id && ($this->tableName === 'users')) {
            return $this->morphOne('Security\Models\Qr', 'resource');
        }
    }

    public function recentQr()
    {
        return $this->morphMany('Security\Models\Qr', 'resource')->orderBy('id', 'desc')->first();
    }

    public function qrs()
    {
        return $this->morphMany('Security\Models\Qr', 'resource');
    }

    public function otp()
    {
        return $this->morphOne('Security\Models\Otp', 'resource')->orderBy('id', 'desc')->first();
    }

    public function recentOtp()
    {
        return $this->morphMany('Security\Models\Otp', 'resource')->orderBy('id', 'desc')->first();
    }

    public function otps()
    {
        return $this->morphMany('Security\Models\Otp', 'resource');
    }

    public function getOtpAttribute()
    {
        $otp = $this->GenerateOtp();
        $otp = ($otp) ? $otp : $this->ReGenerateOtp();

        return $this->attributes['otp'] = @$otp;
    }

    public function scopeHasOtp($query)
    {
        $count = $this->otps()->count();

        return ($count && $count > 0) ? true : false;
    }

    public function scopeGenerateOtp($query)
    {
        $otp = Otp::generateOtp($this);

        return ($otp && $otp->id) ? $otp->value : null;
    }

    public function scopeReGenerateOtp($query)
    {
        $otp = Otp::reGenerateOtp($this);

        return @$otp || null;
    }

    public function scopeValidateOtp($query, $otpValue)
    {
        $isTrue = Otp::validateOtp($this, $otpValue);

        return $isTrue ? true : false;
    }

    public function scopeRemoveOtp($query)
    {
        Otp::removeOtp($this);
    }

    public function scopeGenerateDiagnoseQrCode($query, $input)
    {
        $qr = null;
        $qr = Qr::generateDiagnoseQrCode($input);

        return $qr;
    }

    public function scopeGenerateQrCode($query)
    {
        $qr = null;
        if ($this && $this->id && $this->tableName) {
            switch ($this->tableName) {
                case 'users':
                    $qr = Qr::checkAndStoreSingleRefreshableQr($this);
                    break;
                default:
                    break;
            }
        }

        return $qr;
    }
}
