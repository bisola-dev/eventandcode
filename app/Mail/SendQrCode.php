<?php

namespace App\Mail;

use App\Models\Guest;
use App\Services\QrCodeService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class SendQrCode extends Mailable
{
    use Queueable, SerializesModels;

    public $guest;
    public $qrCodePath;

    public function __construct(Guest $guest)
    {
        $this->guest = $guest;

        // Generate enhanced QR code
        $qrCodeService = new QrCodeService();
        $this->qrCodePath = $qrCodeService->generateEnhancedQrCode($guest);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your QR Code for ' . $this->guest->event->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.qr_code',
            with: [
                'guest' => $this->guest,
                'qrCodePath' => $this->qrCodePath,
            ],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath(Storage::disk('public')->path($this->qrCodePath))
                ->as('qr_code_' . $this->guest->name . '.png')
                ->withMime('image/png'),
        ];
    }
}
