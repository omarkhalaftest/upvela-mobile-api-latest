<?php

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OTPWithdraw extends Mailable
{
    use Queueable, SerializesModels;

    public $otpData;
    public $name;

    /**
     * Create a new message instance.
     *
     * @return void  public $otpData;
     */
    public function __construct($otpData, $name)
    {
        $this->otpData = $otpData;
        $this->name = $name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("verify Email")
                    ->view("otpmail");
    }
}
