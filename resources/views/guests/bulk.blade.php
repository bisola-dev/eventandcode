<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Bulk Add Guests') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <strong>Upload Failed:</strong>
                            <ul class="mt-2">
                                @foreach($errors->all() as $error)
                                    <li class="text-sm">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Sample Spreadsheet Download -->
                    <div class="bg-blue-50 border border-blue-200 rounded p-4 mb-6">
                        <h3 class="text-lg font-semibold text-blue-800 mb-2">📥 Download Sample Spreadsheet</h3>
                        <p class="text-blue-700 mb-3">Get a properly formatted Excel template with the correct column headers and sample data.</p>
                        <a href="{{ route('guests.download-sample') }}" target="_blank" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors inline-flex items-center">
                            <span class="mr-2">📊</span>
                            Download Sample Excel Template
                        </a>
                        <p class="text-xs text-blue-600 mt-2">💡 Tip: Fill this template with your guest data and upload it back</p>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-2">📋 File Format Requirements</h3>
                        <p class="mb-4">Upload a CSV or Excel file with guest information. The file should have these columns:</p>
                        <div class="bg-gray-50 p-4 rounded">
                            <strong>Required columns:</strong>
                            <ul class="list-disc list-inside mt-2 mb-3">
                                <li><strong>Name</strong> - Guest's full name</li>
                                <li><strong>Email</strong> - Guest's email address</li>
                            </ul>
                            <strong>Optional columns:</strong>
                            <ul class="list-disc list-inside mt-2">
                                <li><strong>Phone</strong> - Guest's phone number (can be left empty)</li>
                            </ul>
                        </div>

                        <div class="mt-4">
                            <p class="text-sm text-gray-600 mb-2">Example format:</p>
                            <pre class="bg-gray-100 p-3 rounded text-sm overflow-x-auto">Name,Email,Phone
John Doe,john@example.com,08012345678
Jane Smith,jane@example.com,
Bob Johnson,bob@example.com,08123456789</pre>
                        </div>
                    </div>

                    <form action="{{ route('guests.bulk.store') }}" method="POST" enctype="multipart/form-data" id="bulk-upload-form">
                        @csrf
                        <div class="mb-4">
                            <label for="event_id" class="block text-sm font-medium text-gray-700">Select Event</label>
                            <select name="event_id" id="event_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-pink-500 focus:border-pink-500 px-3 py-2" required>
                                <option value="">Choose an event</option>
                                @foreach($events as $event)
                                    <option value="{{ $event->id }}">{{ mb_convert_encoding($event->name, 'UTF-8', 'auto') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="csv" class="block text-sm font-medium text-gray-700">Select CSV/Excel File</label>
                            <input type="file" name="csv" id="csv" accept=".csv,.txt,.xlsx" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-pink-500 focus:border-pink-500 px-3 py-2" required>
                            <p class="text-sm text-gray-500 mt-1">Supported formats: .csv, .xlsx, .txt</p>
                        </div>

                        <!-- Upload Progress -->
                        <div id="upload-progress" class="hidden mb-4">
                            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded">
                                <div class="flex items-center">
                                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-700 mr-2"></div>
                                    <span>Uploading and processing guests...</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex space-x-4">
                            <button type="submit" id="upload-btn" class="bg-pink-500 text-white px-6 py-2 rounded font-bold hover:bg-pink-600 transition-colors" style="background-color: #ff1493;">
                                📤 Upload and Add Guests
                            </button>
                            <button type="button" onclick="resetForm()" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600 transition-colors" title="Clear the form to start over">
                                🔄 Reset Form
                            </button>
                        </div>
                        <p class="text-sm text-gray-600 mt-2">
                            💡 <strong>Reset Button:</strong> Clears the form if you want to start over or upload a different file
                        </p>
                    </form>

                    <div class="mt-6">
                        <a href="{{ route('events.index') }}" class="text-gray-500 hover:text-gray-700">← Back to Events</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Handle form submission with progress indicator
        document.getElementById('bulk-upload-form').addEventListener('submit', function(e) {
            const uploadBtn = document.getElementById('upload-btn');
            const progressDiv = document.getElementById('upload-progress');

            // Show progress indicator
            progressDiv.classList.remove('hidden');
            uploadBtn.disabled = true;
            uploadBtn.innerHTML = '⏳ Processing...';

            // Form will submit normally, progress indicator stays until page reloads
        });

        // Reset form function
        function resetForm() {
            document.getElementById('bulk-upload-form').reset();
            document.getElementById('upload-progress').classList.add('hidden');
            document.getElementById('upload-btn').disabled = false;
            document.getElementById('upload-btn').innerHTML = '📤 Upload and Add Guests';
        }

        // Auto-hide success message after 10 seconds
        setTimeout(function() {
            const successMsg = document.querySelector('.bg-green-100');
            if (successMsg) {
                successMsg.style.transition = 'opacity 0.5s';
                successMsg.style.opacity = '0';
                setTimeout(() => successMsg.remove(), 500);
            }
        }, 10000);
    </script>
</x-app-layout>