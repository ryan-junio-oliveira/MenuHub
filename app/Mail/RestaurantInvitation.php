<?php

namespace App\Mail;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RestaurantInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Restaurant $restaurant,
        public User $admin,
        public string $setupUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Convite para gerenciar ' . ($this->restaurant->razao_social ?: $this->restaurant->name),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.restaurant-invitation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}