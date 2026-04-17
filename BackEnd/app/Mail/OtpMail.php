<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp; // Tambahkan ini

    public function __construct($otp)
    {
        $this->otp = $otp; // Isi nilai OTP
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kode OTP Aktivasi Akun Spekta Academy',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp', // Pastikan folder 'emails' dan file 'otp.blade.php' ada
        );
    }
}