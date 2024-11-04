<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OTPregister extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
   public $otpData;
    public $name;
    // public $desc;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($otpDataa,$name)
    {
        $this->otpData = $otpDataa;
        $this->name = $name;
        // $this->desc=$desc;
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

