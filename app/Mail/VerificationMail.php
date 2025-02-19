<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;


class VerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $page_name; // Nowa zmienna na dodatkowy parametr

    public function __construct(User $user, $page_name)
    {
        $this->user = $user;
        $this->page_name = $page_name; // Przypisanie dodatkowego parametru
    }

    public function build()
    {
        return $this->from(env('MAIL_FROM_ADDRESS'))
        ->subject('Potwierdzenie rejestracji')
        ->view('emails.verification')
        ->with([
            'userName' => $this->user->name,
            'userEmail' => $this->user->email,
            'verificationCode' => $this->user->verification_code,
            'pageName' => $this->page_name // Przekazanie do widoku
        ]);
    }
}
