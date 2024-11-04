<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProblemReport extends Mailable
{
    use Queueable, SerializesModels;

    public $username;
    public $email;
    public $problem;
    public $phone;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($username, $email, $problem,$phone)
    {
        $this->username = $username;
        $this->email = $email;
        $this->problem = $problem;
        $this->phone = $phone;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('sasa')
                    ->subject('Problem Report')
                    ->with([
                        'username' => $this->username,
                        'email' => $this->email,
                        'problem' => $this->problem,
                        'phone' => $this->phone,
                    ]);
    }
}
