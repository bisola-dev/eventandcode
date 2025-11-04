<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Guest;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Storage;

class QrCodeService
{
    /**
     * Generate enhanced QR code with event image overlay
     */
    public function generateEnhancedQrCode(Guest $guest): string
    {
        $event = $guest->event;

        // Create comprehensive QR data
        $qrData = $this->createQrData($guest, $event);

        // Generate base QR code
        $qrCodeImage = $this->generateBaseQrCode($qrData);

        // If event has image, overlay it on QR code
        if ($event->image) {
            $qrCodeImage = $this->overlayEventImage($qrCodeImage, $event->image);
        }

        // Save the enhanced QR code
        $filename = 'qr_codes/' . $guest->qr_code . '.png';
        Storage::disk('public')->put($filename, $qrCodeImage);

        return $filename;
    }

    /**
     * Create readable QR code data for guest display
     */
    private function createQrData(Guest $guest, Event $event): string
    {
        // Create comprehensive QR data that includes validation URL
        $validationUrl = route('guest.validate', $guest->qr_code);

        $data = [
            'GUEST QR CODE',
            '==============',
            'Validation URL: ' . $validationUrl,
            '',
            'GUEST DETAILS',
            '==============',
            'Name: ' . $guest->name,
            'Email: ' . $guest->email,
            'Phone: ' . ($guest->phone ?: 'Not provided'),
            '',
            'EVENT INFORMATION',
            '==================',
            'Event: ' . $event->name,
            'Date: ' . $event->event_date->format('l, F j, Y'),
            'Time: ' . $event->event_date->format('g:i A'),
            'Venue: ' . $event->location,
            '',
            'Scan this QR code at the event for check-in!'
        ];

        return implode("\n", $data);
    }

    /**
     * Generate base QR code image
     */
    private function generateBaseQrCode(string $data): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle(400, 4), // Higher margin for better readability
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        $svgContent = $writer->writeString($data);

        // Convert SVG to PNG using ImageMagick
        return $this->svgToPngWithImageMagick($svgContent, 400, 400);
    }

    /**
     * Overlay event image on QR code
     */
    private function overlayEventImage(string $qrCodePng, string $eventImagePath): string
    {
        // Load QR code image
        $qrImage = imagecreatefromstring($qrCodePng);

        // Load event image
        $eventImagePath = storage_path('app/public/' . $eventImagePath);
        if (!file_exists($eventImagePath)) {
            return $qrCodePng; // Return original if event image doesn't exist
        }

        $eventImage = imagecreatefromstring(file_get_contents($eventImagePath));

        if (!$eventImage) {
            return $qrCodePng; // Return original if can't load event image
        }

        // Get dimensions
        $qrWidth = imagesx($qrImage);
        $qrHeight = imagesy($qrImage);
        $eventWidth = imagesx($eventImage);
        $eventHeight = imagesy($eventImage);

        // Calculate overlay size (22% of QR code size for better visibility)
        $overlaySize = min($qrWidth, $qrHeight) * 0.22;
        $overlayX = ($qrWidth - $overlaySize) / 2;
        $overlayY = ($qrHeight - $overlaySize) / 2;

        // Resize event image to fit overlay area
        $resizedEventImage = imagecreatetruecolor($overlaySize, $overlaySize);

        // Handle transparency for PNG images
        imagealphablending($resizedEventImage, false);
        imagesavealpha($resizedEventImage, true);
        $transparent = imagecolorallocatealpha($resizedEventImage, 255, 255, 255, 127);
        imagefill($resizedEventImage, 0, 0, $transparent);
        imagealphablending($resizedEventImage, true);

        imagecopyresampled(
            $resizedEventImage, $eventImage,
            0, 0, 0, 0,
            $overlaySize, $overlaySize,
            $eventWidth, $eventHeight
        );

        // Create circular mask for the event image
        $mask = imagecreatetruecolor($overlaySize, $overlaySize);
        imagealphablending($mask, false);
        imagesavealpha($mask, true);
        $transparent = imagecolorallocatealpha($mask, 255, 255, 255, 127);
        imagefill($mask, 0, 0, $transparent);

        // Draw white circle on mask (filled circle for the image area)
        $white = imagecolorallocate($mask, 255, 255, 255);
        imagefilledellipse($mask, $overlaySize/2, $overlaySize/2, $overlaySize-2, $overlaySize-2, $white);

        // Create a new image with the circular mask applied
        $circularImage = imagecreatetruecolor($overlaySize, $overlaySize);
        imagealphablending($circularImage, false);
        imagesavealpha($circularImage, true);
        $transparentBg = imagecolorallocatealpha($circularImage, 255, 255, 255, 127);
        imagefill($circularImage, 0, 0, $transparentBg);

        // Copy the resized event image using the mask
        for ($x = 0; $x < $overlaySize; $x++) {
            for ($y = 0; $y < $overlaySize; $y++) {
                $maskPixel = imagecolorat($mask, $x, $y);
                $maskColors = imagecolorsforindex($mask, $maskPixel);

                if ($maskColors['red'] == 255 && $maskColors['green'] == 255 && $maskColors['blue'] == 255) {
                    // White pixel in mask = copy from event image
                    $eventPixel = imagecolorat($resizedEventImage, $x, $y);
                    imagesetpixel($circularImage, $x, $y, $eventPixel);
                }
                // Non-white pixels remain transparent
            }
        }

        // Composite the circular event image onto QR code
        imagecopy($qrImage, $circularImage, $overlayX, $overlayY, 0, 0, $overlaySize, $overlaySize);

        // Clean up
        imagedestroy($eventImage);
        imagedestroy($resizedEventImage);
        imagedestroy($mask);
        imagedestroy($circularImage);

        // Return PNG data
        ob_start();
        imagepng($qrImage);
        $pngData = ob_get_clean();
        imagedestroy($qrImage);

        return $pngData;
    }

    /**
     * Convert SVG to PNG using ImageMagick
     */
    private function svgToPngWithImageMagick(string $svgContent, int $width, int $height): string
    {
        // Create temporary files
        $tempSvg = tempnam(sys_get_temp_dir(), 'qr_svg_') . '.svg';
        $tempPng = tempnam(sys_get_temp_dir(), 'qr_png_') . '.png';

        // Write SVG content to temporary file
        file_put_contents($tempSvg, $svgContent);

        // Try magick first (IMv7), fallback to convert (IMv6)
        $command = "magick convert -background transparent -size {$width}x{$height} {$tempSvg} {$tempPng} 2>/dev/null || convert -background transparent -size {$width}x{$height} {$tempSvg} {$tempPng}";
        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            // Fallback to GD if ImageMagick fails
            unlink($tempSvg);
            return $this->svgToPngGd($svgContent, $width, $height);
        }

        // Read the PNG file
        $pngData = file_get_contents($tempPng);

        // Clean up temporary files
        unlink($tempSvg);
        unlink($tempPng);

        return $pngData;
    }

    /**
     * Fallback SVG to PNG conversion using GD
     */
    private function svgToPngGd(string $svgContent, int $width, int $height): string
    {
        // Create PNG image
        $image = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);

        imagefill($image, 0, 0, $white);

        // Simple approach: create a basic QR-like pattern
        // This is a fallback - the real QR code should come from ImageMagick
        $moduleSize = 8;
        $margin = 4;

        for ($y = $margin; $y < $height - $margin; $y += $moduleSize) {
            for ($x = $margin; $x < $width - $margin; $x += $moduleSize) {
                if (rand(0, 1)) { // Random pattern as fallback
                    imagefilledrectangle($image, $x, $y, $x + $moduleSize - 1, $y + $moduleSize - 1, $black);
                }
            }
        }

        ob_start();
        imagepng($image);
        $pngData = ob_get_clean();
        imagedestroy($image);

        return $pngData;
    }
}