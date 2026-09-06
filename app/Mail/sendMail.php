<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Throwable;

class sendMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject; // Add a public property to hold the subject
    public $viewName; // Add a public property to hold the view file name
    public $otp;

    /**
     * Create a new message instance.
     *
     * @param string $subject
     * @param string $viewName
     * @return void
     */
    public function __construct($subject, $viewName, $otp)
    {
        $this->subject = $subject; // Set the subject when creating an instance
        $this->viewName = $viewName; // Set the view file name when creating an instance
        $this->otp = $otp;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
        ->replyTo('support@citymobile.com')
        ->subject($this->subject) // Set the email subject dynamically
        ->view($this->viewName); // Set the email view file name dynamically
    }
}


