<?php

namespace App\Mail;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewCustomerMail extends Mailable
{
    use Queueable, SerializesModels;

    public $customer;
    public $password;

    public function __construct(Customer $customer, $password)
    {
        $this->customer = $customer;
        $this->password = $password;
    }

    public function build()
    {
        return $this->subject('Votre compte client')
                    ->view('emails.new-customer')
                    ->with([
                        'first_name' => $this->customer->first_name,
                        'last_name' => $this->customer->last_name,
                        'email' => $this->customer->email,
                        'password' => $this->password
                    ]);
    }
}