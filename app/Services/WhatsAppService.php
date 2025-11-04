<?php

namespace App\Services;

use App\Models\Guest;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Storage;

class WhatsAppService
{
    /**
     * Generate WhatsApp URL for sharing guest QR code
     */
    public function generateShareUrl(Guest $guest): string
    {
        $message = $this->createInviteMessage($guest);

        // Format phone number for WhatsApp (remove any spaces, ensure +country code)
        $phone = $this->formatPhoneNumber($guest->phone);

        return "https://wa.me/{$phone}?text=" . urlencode($message);
    }

    /**
     * Generate WhatsApp URL with QR code image link (fallback method)
     */
    public function generateShareUrlWithQr(Guest $guest): string
    {
        $qrCodeUrl = asset('storage/qr_codes/' . $guest->qr_code . '.png');
        $message = $this->createInviteMessageWithQr($guest, $qrCodeUrl);

        // Format phone number for WhatsApp (remove any spaces, ensure +country code)
        $phone = $this->formatPhoneNumber($guest->phone);

        return "https://wa.me/{$phone}?text=" . urlencode($message);
    }

    /**
     * Send WhatsApp message with QR code image via Twilio API (preferred method)
     */
    public function sendQrCodeViaWhatsApp(Guest $guest): bool
    {
        try {
            $twilio = new Client(
                config('services.twilio.sid'),
                config('services.twilio.token')
            );

            $message = $this->createInviteMessage($guest);
            $phone = $this->formatPhoneNumber($guest->phone);

            // Send message with QR code as media
            $twilio->messages->create(
                'whatsapp:' . $phone,
                [
                    'from' => 'whatsapp:' . config('services.twilio.from'),
                    'body' => $message,
                    'mediaUrl' => [asset('storage/qr_codes/' . $guest->qr_code . '.png')]
                ]
            );

            return true;
        } catch (\Exception $e) {
            // Log error and fall back to URL generation
            \Log::error('Twilio WhatsApp send failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Create invite message for WhatsApp
     */
    private function createInviteMessage(Guest $guest): string
    {
        $event = $guest->event;

        $message = "🎉 *EVENT INVITATION*\n\n";
        $message .= "Hello *{$guest->name}*!\n\n";
        $message .= "You're invited to:\n";
        $message .= "*{$event->name}*\n\n";
        $message .= "📅 *Date:* {$event->event_date->format('l, F j, Y')}\n";
        $message .= "🕐 *Time:* {$event->event_date->format('g:i A')}\n";
        $message .= "📍 *Venue:* {$event->location}\n\n";

        if ($event->description) {
            $message .= "📝 *Details:* {$event->description}\n\n";
        }

        $message .= "📱 *Your QR Code:*\n";
        $message .= "Scan this QR code at the event for check-in:\n";
        $message .= "[QR Code Image Attached]\n\n";
        $message .= "💬 *Contact:* For questions, WhatsApp only: 08057516152\n\n";
        $message .= "See you there! 🎊";

        return $message;
    }

    /**
     * Create invite message with QR code link for WhatsApp
     */
    private function createInviteMessageWithQr(Guest $guest, string $qrCodeUrl): string
    {
        $event = $guest->event;

        $message = "🎉 *EVENT INVITATION*\n\n";
        $message .= "Hello *{$guest->name}*!\n\n";
        $message .= "You're invited to:\n";
        $message .= "*{$event->name}*\n\n";
        $message .= "📅 *Date:* {$event->event_date->format('l, F j, Y')}\n";
        $message .= "🕐 *Time:* {$event->event_date->format('g:i A')}\n";
        $message .= "📍 *Venue:* {$event->location}\n\n";

        if ($event->description) {
            $message .= "📝 *Details:* {$event->description}\n\n";
        }

        $message .= "📱 *Your QR Code:*\n";
        $message .= "👉 *VIEW QR CODE:* {$qrCodeUrl}\n\n";
        $message .= "📷 *Scan this QR code at the event for check-in*\n\n";
        $message .= "💡 *Tip:* Save this QR code to your phone for easy access!\n\n";
        $message .= "💬 *Contact:* For questions, WhatsApp only: 08057516152\n\n";
        $message .= "See you there! 🎊";

        return $message;
    }

    /**
     * Format phone number for WhatsApp
     */
    private function formatPhoneNumber(?string $phone): string
    {
        if (!$phone) {
            return '';
        }

        // Remove all non-numeric characters
        $phone = preg_replace('/\D/', '', $phone);

        // If it starts with 0, replace with +234 (Nigeria country code)
        if (str_starts_with($phone, '0')) {
            $phone = '234' . substr($phone, 1);
        }

        // If it doesn't start with country code, assume Nigeria
        if (!str_starts_with($phone, '234') && !str_starts_with($phone, '+')) {
            $phone = '234' . $phone;
        }

        // Remove + if present
        $phone = ltrim($phone, '+');

        return $phone;
    }

    /**
     * Send WhatsApp message with QR code image via Twilio API
     */
    public function sendMessage(Guest $guest): bool
    {
        try {
            $twilio = new Client(
                config('services.twilio.sid'),
                config('services.twilio.token')
            );

            $message = $this->createInviteMessage($guest);
            $phone = $this->formatPhoneNumber($guest->phone);

            // Get QR code file path
            $qrCodePath = storage_path('app/public/qr_codes/' . $guest->qr_code . '.png');

            // Send message with media
            $twilio->messages->create(
                'whatsapp:' . $phone,
                [
                    'from' => 'whatsapp:' . config('services.twilio.from'),
                    'body' => $message,
                    'mediaUrl' => [asset('storage/qr_codes/' . $guest->qr_code . '.png')]
                ]
            );

            return true;
        } catch (\Exception $e) {
            // Log error and fall back to URL generation
            \Log::error('Twilio WhatsApp send failed: ' . $e->getMessage());
            return false;
        }
    }
}