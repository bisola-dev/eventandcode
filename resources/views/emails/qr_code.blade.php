<!DOCTYPE html>
<html>
<head>
    <title>Your QR Code</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
        .qr-info { background: white; padding: 20px; border-radius: 8px; border-left: 4px solid #667eea; margin: 20px 0; }
        .event-details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .highlight { background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Event Invitation</h1>
            <p>Your personalized QR code is attached</p>
        </div>

        <div class="content">
            <h2>Hello {{ $guest->name }}!</h2>

            <p>You have been invited to <strong>{{ $guest->event->name }}</strong>.</p>

            <div class="qr-info">
                <h3>📱 Your QR Code</h3>
                <p>Your personalized QR code is attached to this email as an image file. The QR code features a custom design with your event's branding.</p>
                <p><strong>File name:</strong> qr_code_{{ $guest->name }}.png</p>
                <div style="text-align: center; margin: 20px 0;">
                    <img src="{{ asset('storage/' . $qrCodePath ?? 'qr_codes/' . $guest->qr_code . '.png') }}" alt="QR Code" style="width: 150px; height: 150px; border: 2px solid #667eea; border-radius: 8px;">
                </div>
            </div>

            <div class="highlight">
                <h4>📍 How to Use Your QR Code:</h4>
                <ol>
                    <li>Save the attached QR code image to your phone</li>
                    <li>Show it at the venue entrance for check-in</li>
                    <li>The QR code contains all your event information</li>
                </ol>
            </div>

            <div class="event-details">
                <h3>🎪 Event Details</h3>
                <ul>
                    <li><strong>Event:</strong> {{ $guest->event->name }}</li>
                    <li><strong>Date:</strong> {{ $guest->event->event_date->format('l, F j, Y \a\t g:i A') }}</li>
                    <li><strong>Location:</strong> {{ $guest->event->location }}</li>
                    @if($guest->event->client_name)
                    <li><strong>Client:</strong> {{ $guest->event->client_name }}</li>
                    @endif
                    @if($guest->event->description)
                    <li><strong>Description:</strong> {{ $guest->event->description }}</li>
                    @endif
                </ul>
            </div>

            <div class="qr-info">
                <h4>📊 QR Code Information</h4>
                <p>When scanned, your QR code reveals:</p>
                <ul>
                    <li>Your personal details (name, email, phone)</li>
                    <li>Complete event information</li>
                    <li>Check-in status (whether you've checked in or not)</li>
                    <li>Check-in timestamp (when you checked in, if applicable)</li>
                    <li>Direct link to your guest profile for verification</li>
                </ul>
            </div>

            <p>If you have any questions, please contact the event manager on <strong>08057516152</strong> (WhatsApp only).</p>

            <div class="footer">
                <p>This QR code is unique to you and cannot be duplicated.</p>
                <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
            </div>
        </div>
    </div>
</body>
</html>