<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use App\Services\QrCodeService;

class PhpMailerService
{
    protected $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);

        // Server settings
        $this->mailer->isSMTP();
        $this->mailer->Host = config('mail.host');
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = config('mail.username');
        $this->mailer->Password = config('mail.password');
        $this->mailer->SMTPSecure = config('mail.encryption') === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
        $this->mailer->Port = config('mail.port');

        // Recipients
        $this->mailer->setFrom(config('mail.from.address'), config('mail.from.name'));
    }

    public function sendMail($to, $subject, $body, $isHtml = true, $attachments = [])
    {
        try {
            // Recipients
            $this->mailer->addAddress($to);

            // Attachments
            foreach ($attachments as $attachment) {
                $this->mailer->addAttachment($attachment['path'], $attachment['name'] ?? '');
            }

            // Content
            $this->mailer->isHTML($isHtml);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;

            $this->mailer->send();
            return ['success' => true, 'message' => 'Message has been sent'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => "Message could not be sent. Mailer Error: {$this->mailer->ErrorInfo}"];
        }
    }

    public function sendQrCodeEmail($guest)
    {
        try {
            // Generate QR code if not already
            $qrCodeService = new QrCodeService();
            $qrCodePath = $qrCodeService->generateEnhancedQrCode($guest);

            // Render the email view
            $body = View::make('emails.qr_code', [
                'guest' => $guest,
                'qrCodePath' => $qrCodePath,
            ])->render();

            // Recipients
            $this->mailer->addAddress($guest->email);

            // Attachments
            $attachmentPath = Storage::disk('public')->path($qrCodePath);
            $this->mailer->addAttachment($attachmentPath, 'qr_code_' . $guest->name . '.png');

            // Content
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Your QR Code for ' . $guest->event->name;
            $this->mailer->Body = $body;

            $this->mailer->send();
            return ['success' => true, 'message' => 'QR Code email has been sent'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => "Message could not be sent. Mailer Error: {$this->mailer->ErrorInfo}"];
        }
    }
}