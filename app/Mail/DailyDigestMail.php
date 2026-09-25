<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyDigestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly string $digest,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your morning digest — '.now()->format('l, F j'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.daily-digest',
            with: ['user' => $this->user, 'digest' => $this->digest],
        );
    }
}
