<!DOCTYPE html>
<html>
<head>
    <title>Your QR Code</title>
</head>
<body>
    <h1>Hello {{ $guest->name }}</h1>
    <p>You have been invited to the event: {{ $guest->event->name }}</p>
    <p>Event Date: {{ $guest->event->event_date }}</p>
    <p>Location: {{ $guest->event->location }}</p>
    <p>Please scan the QR code below to check in at the venue:</p>
    <div>
        {!! QrCode::generate($guest->qr_code) !!}
    </div>
    <p>Thank you!</p>
</body>
</html>