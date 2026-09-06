<?php

namespace Notification\Helpers;

use App\Mail\LoginOtpMail;
use Illuminate\Support\Facades\Mail;
use Otp;
use Tji\Helpers\MainHelper;

class Notification extends MainHelper
{
    public function sendLoginOtpMail($model, $otp = null)
    {
        if ($model && $model->id) {
            $otp = @$otp ?: @Otp::reGenerateOtp($model);
            if ($otp) {
                Mail::to($model->email)->send(new LoginOtpMail($model, $otp));
            }
        }

        return true;
    }

    public function sendEmail($input)
    {
        $recipientEmail = @$input['email'];
        $subject = @$input['subject'] ?: 'city-Mobile';
        $viewName = $input['view'] ?: 'otpMail'; // Assuming you have a Blade view named 'sendMail.blade.php'
        $otp = $input['otp'] ?: null;
        if (filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
            $email = new sendMail($subject, $viewName, $otp);
            if (Mail::to($recipientEmail)->send($email)) {
                return true;
            } else {
                throw new ErrorResponse('Failed to send email. Please try again later.', 405, 'info');
            }
        } else {
            throw new ErrorResponse('Invalid email address. Please check the recipient email address and try again.', 405, 'info');
        }
    }
}
