<?php

namespace App\Http\Controllers;

use App\Mail\SendQrCode;
use App\Models\Event;
use App\Models\Guest;
use App\Services\QrCodeService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
// use Maatwebsite\Excel\Facades\Excel;

class GuestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $eventId = request('event');

        // Admins can access all events, regular users only their own
        if (auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin') {
            $event = Event::findOrFail($eventId);
        } else {
            $event = auth()->user()->events()->findOrFail($eventId);
        }

        $search = request('search');
        $guests = $event->guests();

        if ($search) {
            $guests = $guests->where(function($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%')
                      ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        $guests = $guests->orderBy('name', 'asc')->get();

        return view('guests.index', compact('guests', 'event', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $eventId = request('event');
        $event = auth()->user()->events()->findOrFail($eventId);
        return view('guests.create', compact('event'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $eventId = $request->event_id;
        auth()->user()->events()->findOrFail($eventId);

        if ($request->has('guests')) {
            // Multiple guests
            $request->validate([
                'guests' => 'required|array|min:1',
                'guests.*.name' => 'required|string',
                'guests.*.email' => 'required|email|unique:guests,email',
                'guests.*.phone' => 'nullable|string',
            ]);

            foreach ($request->guests as $guestData) {
                $qrCode = Str::uuid();
                $phone = $guestData['phone'] ?? null;
                // Add leading 0 for 10-digit phone numbers
                if ($phone && is_numeric($phone) && strlen($phone) == 10) {
                    $phone = '0' . $phone;
                }

                $guest = Guest::create([
                    'name' => $guestData['name'],
                    'email' => $guestData['email'],
                    'phone' => $phone,
                    'qr_code' => $qrCode,
                    'event_id' => $eventId,
                ]);

                // Send enhanced QR code email
                Mail::to($guest->email)->send(new SendQrCode($guest));
            }
        } else {
            // Single guest (backward compatibility)
            $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:guests,email',
                'phone' => 'nullable',
            ]);

            $qrCode = Str::uuid();

            $phone = $request->phone;
            // Add leading 0 for 10-digit phone numbers
            if ($phone && is_numeric($phone) && strlen($phone) == 10) {
                $phone = '0' . $phone;
            }

            $guest = Guest::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $phone,
                'qr_code' => $qrCode,
                'event_id' => $eventId,
            ]);

            // Send enhanced QR code email
            Mail::to($guest->email)->send(new SendQrCode($guest));
        }

        return redirect()->route('guests.index', ['event' => $eventId])->with('success', 'Guests added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $guest = Guest::findOrFail($id);

        // Admins can access guests from all events, regular users only their own
        if (!(auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin')) {
            auth()->user()->events()->findOrFail($guest->event_id);
        }

        return view('guests.show', compact('guest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $guest = Guest::findOrFail($id);

        // Admins can access guests from all events, regular users only their own
        if (!(auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin')) {
            auth()->user()->events()->findOrFail($guest->event_id);
        }

        return view('guests.edit', compact('guest'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $guest = Guest::findOrFail($id);

        // Admins can access guests from all events, regular users only their own
        if (!(auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin')) {
            auth()->user()->events()->findOrFail($guest->event_id);
        }

        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'nullable',
        ]);

        $guest->update($request->only(['name', 'email', 'phone']));

        return redirect()->route('guests.index', ['event' => $guest->event_id])->with('success', 'Guest updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $guest = Guest::findOrFail($id);
        $eventId = $guest->event_id;

        // Admins can access guests from all events, regular users only their own
        if (!(auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin')) {
            auth()->user()->events()->findOrFail($eventId);
        }

        $guest->delete();
        return redirect()->route('guests.index', ['event' => $eventId]);
    }

    public function bulk()
    {
        $events = auth()->user()->events->map(function($event) {
            $event->name = @mb_convert_encoding($event->name, 'UTF-8', 'UTF-8') ?: @iconv('CP1252', 'UTF-8//IGNORE', $event->name) ?: $event->name;
            return $event;
        });
        return view('guests.bulk', compact('events'));
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'csv' => 'required|file|mimes:csv,txt,xlsx|max:2048',
            'event_id' => 'required|exists:events,id',
        ]);

        // Check for duplicate emails in the uploaded file
        $file = $request->file('csv');
        $extension = $file->getClientOriginalExtension();

        if ($extension === 'xlsx') {
            $data = $this->parseExcelFile($file);
        } else {
            $data = array_map('str_getcsv', file($file->getRealPath()));
            $header = array_shift($data);
        }

        // Check for duplicates in existing guests for this event
        $existingEmails = Guest::where('event_id', $request->event_id)
                              ->pluck('email')
                              ->toArray();

        $duplicateEmails = [];
        foreach ($data as $row) {
            if (count($row) >= 2 && !empty($row[1])) {
                $email = trim($row[1]);
                if (in_array($email, $existingEmails)) {
                    $duplicateEmails[] = $email;
                }
            }
        }

        if (!empty($duplicateEmails)) {
            return redirect()->back()
                           ->withErrors(['duplicates' => 'The following emails already exist for this event: ' . implode(', ', array_unique($duplicateEmails))])
                           ->withInput();
        }

        $eventId = $request->event_id;
        auth()->user()->events()->findOrFail($eventId);

        $file = $request->file('csv');
        $extension = $file->getClientOriginalExtension();

        if ($extension === 'xlsx') {
            // Handle Excel file
            $data = $this->parseExcelFile($file);
        } else {
            // Handle CSV
            $data = array_map('str_getcsv', file($file->getRealPath()));
            $header = array_shift($data); // Remove header
        }

        $errors = [];
        $firstGuestId = null;
        foreach ($data as $index => $row) {
            try {
                if (count($row) >= 2 && !empty($row[0]) && !empty($row[1])) {
                    $name = trim($row[0]);
                    $phone = isset($row[2]) ? trim($row[2]) : null;

                    // Handle encoding issues
                    $name = @mb_convert_encoding($name, 'UTF-8', 'UTF-8') ?: @iconv('CP1252', 'UTF-8//IGNORE', $name) ?: $name;
                    if ($phone) {
                        $phone = @mb_convert_encoding($phone, 'UTF-8', 'UTF-8') ?: @iconv('CP1252', 'UTF-8//IGNORE', $phone) ?: $phone;
                        // Add leading 0 for 10-digit phone numbers
                        if (is_numeric($phone) && strlen($phone) == 10) {
                            $phone = '0' . $phone;
                        }
                    }

                    $guest = Guest::create([
                        'name' => $name,
                        'email' => trim($row[1]),
                        'phone' => $phone,
                        'qr_code' => Str::uuid(),
                        'event_id' => $eventId,
                    ]);

                    if (!$firstGuestId) {
                        $firstGuestId = $guest->id;
                    }

                    // Send enhanced QR code email
                    Mail::to($guest->email)->send(new SendQrCode($guest));
                }
            } catch (\Exception $e) {
                $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        if (!empty($errors)) {
            return redirect()->back()->withErrors($errors);
        }

        // Redirect to the guests index page with success message
        return redirect()->route('guests.index', ['event' => $eventId])->with('success', 'Bulk upload completed! ' . count($data) . ' guests added successfully. QR codes have been generated.');
    }

    private function parseExcelFile($file)
    {
        $data = [];
        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Remove header row
            array_shift($rows);

            // Filter out empty rows
            $data = array_filter($rows, function($row) {
                return !empty(array_filter($row)); // Remove completely empty rows
            });
        } catch (\Exception $e) {
            // If Excel parsing fails, fall back to CSV parsing
            $handle = fopen($file->getRealPath(), 'r');
            if ($handle !== false) {
                while (($row = fgetcsv($handle)) !== false) {
                    $data[] = $row;
                }
                fclose($handle);
                array_shift($data); // Remove header
            }
        }
        return $data;
    }

    public function validateQr($qrCode)
    {
        $guest = Guest::where('qr_code', $qrCode)->first();

        if (!$guest) {
            return response()->json(['error' => 'Invalid QR code'], 404);
        }

        // Check if already checked in
        if ($guest->checked_in) {
            return response()->json([
                'status' => 'already_checked_in',
                'message' => 'Guest already checked in',
                'guest' => [
                    'name' => $guest->name,
                    'email' => $guest->email,
                    'checked_in_at' => $guest->checked_in_at,
                ],
                'event' => [
                    'name' => $guest->event->name,
                    'date' => $guest->event->event_date,
                    'location' => $guest->event->location,
                ]
            ]);
        }

        // Mark as checked in
        $guest->update([
            'checked_in' => true,
            'checked_in_at' => now(),
        ]);

        // Regenerate QR code to reflect new check-in status for integrity
        $qrCodeService = new \App\Services\QrCodeService();
        $qrCodeService->generateEnhancedQrCode($guest);

        return response()->json([
            'status' => 'success',
            'message' => 'Guest checked in successfully',
            'guest' => [
                'name' => $guest->name,
                'email' => $guest->email,
                'phone' => $guest->phone,
                'checked_in_at' => $guest->checked_in_at,
            ],
            'event' => [
                'name' => $guest->event->name,
                'date' => $guest->event->event_date,
                'location' => $guest->event->location,
                'description' => $guest->event->description,
                'client_name' => $guest->event->client_name,
            ]
        ]);
    }

    public function displayGuest($qrCode)
    {
        $guest = Guest::where('qr_code', $qrCode)->first();

        if (!$guest) {
            abort(404, 'Guest not found');
        }

        return view('guests.display', compact('guest'));
    }

    public function shareWhatsApp(Guest $guest)
    {
        // Check if user has access to this guest's event
        if (!(auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin')) {
            auth()->user()->events()->findOrFail($guest->event_id);
        }

        $whatsAppService = new WhatsAppService();

        // Try to send via Twilio API first
        $sent = $whatsAppService->sendQrCodeViaWhatsApp($guest);

        if ($sent) {
            return response()->json([
                'success' => true,
                'message' => 'QR code sent successfully via WhatsApp'
            ]);
        } else {
            // Fall back to URL generation
            $whatsAppUrl = $whatsAppService->generateShareUrlWithQr($guest);
            return response()->json([
                'success' => true,
                'whatsAppUrl' => $whatsAppUrl,
                'message' => 'WhatsApp share URL with QR code generated (API send failed, using fallback)'
            ]);
        }
    }

    public function sendAllWhatsApp(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
        ]);

        $eventId = $request->event_id;

        // Check if user has access to this event
        if (!(auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin')) {
            auth()->user()->events()->findOrFail($eventId);
        }

        $guests = Guest::where('event_id', $eventId)
                      ->whereNotNull('phone')
                      ->where('phone', '!=', '')
                      ->get();

        $whatsAppService = new WhatsAppService();
        $sent = 0;
        $failed = 0;
        $urls = [];

        foreach ($guests as $guest) {
            // Try to send via Twilio API first
            $result = $whatsAppService->sendQrCodeViaWhatsApp($guest);

            if ($result) {
                $sent++;
            } else {
                // Fall back to URL generation
                $url = $whatsAppService->generateShareUrlWithQr($guest);
                $urls[] = [
                    'name' => $guest->name,
                    'phone' => $guest->phone,
                    'url' => $url
                ];
                $failed++;
            }
        }

        return response()->json([
            'success' => true,
            'sent' => $sent,
            'failed' => $failed,
            'urls' => $urls,
            'message' => "Sent QR codes to {$sent} guests via WhatsApp. {$failed} failed and generated URLs as fallback."
        ]);
    }

    public function sendQrEmail(Guest $guest)
    {
        // Check if user has access to this guest's event
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        if (!($user->role === 'admin' || $user->role === 'superadmin')) {
            $user->events()->findOrFail($guest->event_id);
        }

        try {
            // Send the QR code email
            Mail::to($guest->email)->send(new SendQrCode($guest));

            return response()->json([
                'success' => true,
                'message' => 'QR code email sent successfully to ' . $guest->email
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ], 500);
        }
    }

    public function testEmail(Guest $guest)
    {
        // Check if user has access to this guest's event
        if (!(auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin')) {
            auth()->user()->events()->findOrFail($guest->event_id);
        }

        // Generate the QR code
        $qrCodeService = new QrCodeService();
        $qrCodePath = $qrCodeService->generateEnhancedQrCode($guest);

        // Return the email content for testing
        return view('emails.qr_code', [
            'guest' => $guest,
            'qrCodePath' => $qrCodePath,
        ]);
    }

    public function downloadSample()
    {
        // Create sample Excel file
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'Name');
        $sheet->setCellValue('B1', 'Email');
        $sheet->setCellValue('C1', 'Phone');

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E3F2FD']
            ]
        ];
        $sheet->getStyle('A1:C1')->applyFromArray($headerStyle);

        // Add sample data
        $sheet->setCellValue('A2', 'John Doe');
        $sheet->setCellValue('B2', 'john.doe@example.com');
        $sheet->setCellValue('C2', '08012345678');

        $sheet->setCellValue('A3', 'Jane Smith');
        $sheet->setCellValue('B3', 'jane.smith@example.com');
        $sheet->setCellValue('C3', '08123456789');

        $sheet->setCellValue('A4', 'Bob Johnson');
        $sheet->setCellValue('B4', 'bob.johnson@example.com');
        $sheet->setCellValue('C4', '09087654321');

        // Auto-size columns
        foreach (range('A', 'C') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Create writer and output to browser
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        // Set headers for download
        $filename = 'guest_import_template.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
