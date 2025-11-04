<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Guests for ') . $event->name }}
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

                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <a href="{{ route('guests.create', ['event' => $event->id]) }}" class="bg-pink-500 text-white px-4 py-2 rounded font-bold mr-2" style="background-color: #ff1493;">Add Guest</a>
                            <a href="{{ route('guests.bulk') }}" class="bg-pink-500 text-white px-4 py-2 rounded font-bold mr-2" style="background-color: #ff1493;">Bulk Add Guests</a>
                            <button onclick="sendAllWhatsApp({{ $event->id }})" class="bg-green-500 text-white px-4 py-2 rounded font-bold mr-2 hover:bg-green-600 transition-colors">
                                📱 Send All WhatsApp
                            </button>
                            <a href="{{ route('events.show', $event) }}" class="bg-gray-500 text-white px-4 py-2 rounded">Back to Event</a>
                        </div>
                        <div class="flex items-center">
                            <form method="GET" action="{{ route('guests.index', ['event' => $event->id]) }}" class="flex">
                                <input type="hidden" name="event" value="{{ $event->id }}">
                                <input type="text" name="search" value="{{ $search }}" placeholder="Search guests..." class="border border-gray-300 rounded-l px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" style="border-color: #ff1493;">
                                <button type="submit" class="bg-pink-500 text-white px-4 py-2 rounded-r font-bold" style="background-color: #ff1493;">Search</button>
                            </form>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-300" id="guests-table">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Phone</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">QR Code</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Checked In</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($guests as $guest)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $guest->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $guest->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $guest->phone ?: 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-500">{{ $guest->qr_code }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $guest->checked_in ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $guest->checked_in ? 'Yes' : 'No' }}
                                        </span>
                                        @if($guest->checked_in_at)
                                            <br><span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($guest->checked_in_at)->format('M j, g:i A') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('guests.edit', $guest) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                        <div class="relative">
                                            <button onclick="showShareOptions(this, {{ $guest->id }}, '{{ $guest->name }}', '{{ $guest->email }}', '{{ $guest->qr_code }}')" class="text-purple-600 hover:text-purple-900 mr-3" title="Share QR Code">
                                                📤 Share
                                            </button>
                                            <div id="share-options-{{ $guest->id }}" class="fixed hidden bg-white border border-gray-300 rounded-lg shadow-xl p-6 z-50" style="width: 450px; min-height: 280px; left: 50%; top: 50%; transform: translate(-50%, -50%);">
                                                <div class="text-center mb-6">
                                                    <h3 class="text-xl font-bold text-gray-800 mb-2">Share {{ $guest->name }}'s QR Code</h3>
                                                    <p class="text-sm text-gray-600">Choose how to share the QR code</p>
                                                </div>

                                                <div class="grid grid-cols-2 gap-4 mb-6">
                                                    <!-- Preview Email -->
                                                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-4 hover:shadow-lg transition-all duration-200 cursor-pointer" onclick="window.open('/test-email/{{ $guest->id }}', '_blank')">
                                                        <div class="flex flex-col items-center text-center">
                                                            <div class="bg-blue-500 text-white rounded-full w-12 h-12 flex items-center justify-center mb-3 shadow-md">
                                                                <span class="text-xl">👁️</span>
                                                            </div>
                                                            <span class="font-semibold text-blue-800 text-sm">Preview Email</span>
                                                            <span class="text-xs text-blue-600 mt-1">See how it looks</span>
                                                        </div>
                                                    </div>

                                                    <!-- Send Email -->
                                                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-xl p-4 hover:shadow-lg transition-all duration-200 cursor-pointer" onclick="sendQrEmail({{ $guest->id }}, '{{ $guest->name }}', '{{ $guest->email }}')">
                                                        <div class="flex flex-col items-center text-center">
                                                            <div class="bg-purple-500 text-white rounded-full w-12 h-12 flex items-center justify-center mb-3 shadow-md">
                                                                <span class="text-xl">📧</span>
                                                            </div>
                                                            <span class="font-semibold text-purple-800 text-sm">Send Email</span>
                                                            <span class="text-xs text-purple-600 mt-1">Direct to inbox</span>
                                                        </div>
                                                    </div>

                                                    <!-- WhatsApp -->
                                                    <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-xl p-4 hover:shadow-lg transition-all duration-200 cursor-pointer" onclick="sendWhatsAppInvite({{ $guest->id }}, '{{ $guest->name }}')">
                                                        <div class="flex flex-col items-center text-center">
                                                            <div class="bg-green-500 text-white rounded-full w-12 h-12 flex items-center justify-center mb-3 shadow-md">
                                                                <span class="text-xl">💬</span>
                                                            </div>
                                                            <span class="font-semibold text-green-800 text-sm">WhatsApp</span>
                                                            <span class="text-xs text-green-600 mt-1">WhatsApp only</span>
                                                        </div>
                                                    </div>

                                                    <!-- Test Link -->
                                                    <div class="bg-gradient-to-br from-cyan-50 to-cyan-100 border border-cyan-200 rounded-xl p-4 hover:shadow-lg transition-all duration-200 cursor-pointer" onclick="testWhatsApp('{{ $guest->phone }}', '{{ $guest->name }}')">
                                                        <div class="flex flex-col items-center text-center">
                                                            <div class="bg-cyan-500 text-white rounded-full w-12 h-12 flex items-center justify-center mb-3 shadow-md">
                                                                <span class="text-xl">🔗</span>
                                                            </div>
                                                            <span class="font-semibold text-cyan-800 text-sm">Test Link</span>
                                                            <span class="text-xs text-cyan-600 mt-1">Verify number</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="text-center">
                                                    <button onclick="closeShareModal()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium px-6 py-2 rounded-lg transition-colors duration-200 border border-gray-300">
                                                        Close
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <form action="{{ route('guests.destroy', $guest) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this guest?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        @if($search)
                                            No guests found matching "{{ $search }}"
                                        @else
                                            No guests added yet for this event.
                                        @endif
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($search)
                        <div class="mt-4">
                            <a href="{{ route('guests.index', ['event' => $event->id]) }}" class="text-blue-600 hover:text-blue-800">Clear search</a>
                        </div>
                    @endif

                    <script>
                        // Show share options modal
                        function showShareOptions(button, guestId, name, email, qrCode) {
                            console.log('Showing share modal for guest:', guestId, name);

                            // Close any other open modals first
                            document.querySelectorAll('[id^="share-options-"]').forEach(el => {
                                el.classList.add('hidden');
                            });

                            // Show the modal
                            const modal = document.getElementById(`share-options-${guestId}`);
                            if (modal) {
                                modal.classList.remove('hidden');
                            }
                        }

                        // Close share modal
                        function closeShareModal() {
                            document.querySelectorAll('[id^="share-options-"]').forEach(el => {
                                el.classList.add('hidden');
                            });
                        }

                        // Send QR code email function
                        window.sendQrEmail = function(guestId, name, email) {
                            fetch(`/guest/send-qr-email/${guestId}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                                },
                                body: JSON.stringify({})
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    showNotification('QR code email sent to ' + email, 'green');
                                } else {
                                    showNotification('Failed to send email: ' + data.message, 'red');
                                }
                            })
                            .catch(error => {
                                showNotification('Error sending email', 'red');
                            });
                        };

                        // Send WhatsApp invite function
                        window.sendWhatsAppInvite = function(guestId, name) {
                            console.log('Sending WhatsApp invite for guest:', guestId, name);
                            showNotification('Sending WhatsApp invite...', 'blue');

                            fetch(`/guest/share-whatsapp/${guestId}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                                },
                                body: JSON.stringify({})
                            })
                            .then(response => {
                                console.log('Response status:', response.status);
                                if (!response.ok) {
                                    throw new Error(`HTTP error! status: ${response.status}`);
                                }
                                return response.json();
                            })
                            .then(data => {
                                console.log('Response data:', data);
                                if (data.success) {
                                    if (data.whatsAppUrl) {
                                        console.log('Opening WhatsApp URL:', data.whatsAppUrl);
                                        const whatsappWindow = window.open(data.whatsAppUrl, '_blank');
                                        if (whatsappWindow) {
                                            showNotification('WhatsApp opened with invite for ' + name, 'green');
                                        } else {
                                            showNotification('Popup blocked! Please allow popups for this site.', 'orange');
                                        }
                                    } else {
                                        showNotification('QR code sent successfully via WhatsApp to ' + name, 'green');
                                    }
                                } else {
                                    showNotification('Failed to send WhatsApp invite: ' + (data.message || 'Unknown error'), 'red');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                showNotification('Error sending WhatsApp invite: ' + error.message, 'red');
                            });
                        };

                        // Test WhatsApp link function
                        window.testWhatsApp = function(phone, name) {
                            if (!phone) {
                                showNotification('No phone number available for ' + name, 'red');
                                return;
                            }

                            let formattedPhone = phone.replace(/\D/g, '');
                            if (formattedPhone.startsWith('0')) {
                                formattedPhone = '234' + formattedPhone.substring(1);
                            }

                            const whatsappUrl = `https://wa.me/${formattedPhone}?text=Hello%20${encodeURIComponent(name)}`;
                            console.log('Testing WhatsApp URL:', whatsappUrl);

                            const whatsappWindow = window.open(whatsappUrl, '_blank');
                            if (whatsappWindow) {
                                showNotification('WhatsApp test link opened for ' + name, 'green');
                            } else {
                                showNotification('Popup blocked! Please allow popups for this site.', 'orange');
                            }
                        };

                        // Show notification function
                        window.showNotification = function(message, color) {
                            const notification = document.createElement('div');
                            notification.className = `fixed top-4 right-4 bg-${color}-500 text-white px-4 py-2 rounded shadow-lg z-50`;
                            notification.textContent = message;
                            document.body.appendChild(notification);

                            setTimeout(() => {
                                notification.remove();
                            }, 3000);
                        };

                        // Show manual WhatsApp links as fallback when popups are blocked
                        window.showManualWhatsAppLinks = function(urls) {
                            const modal = document.createElement('div');
                            modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50';
                            modal.innerHTML = `
                                <div class="bg-white p-6 rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-96 overflow-y-auto">
                                    <div class="flex justify-between items-center mb-4">
                                        <h3 class="text-lg font-semibold">Manual WhatsApp Links</h3>
                                        <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-4">Popups were blocked. Click each link below to open WhatsApp manually:</p>
                                    <div class="space-y-2">
                                        ${urls.map(guestData => `
                                            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-200">
                                                <span class="font-medium text-green-800">${guestData.name}</span>
                                                <a href="${guestData.url}" target="_blank" class="bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600 transition-colors">
                                                    Open WhatsApp
                                                </a>
                                            </div>
                                        `).join('')}
                                    </div>
                                </div>
                            `;

                            document.body.appendChild(modal);
                        };

                        // Send all WhatsApp invites function
                        window.sendAllWhatsApp = function(eventId) {
                            if (!confirm('This will send QR codes via WhatsApp to all guests with phone numbers. Continue?')) {
                                return;
                            }

                            showNotification('Sending WhatsApp invites...', 'blue');

                            fetch('/guest/send-all-whatsapp', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                                },
                                body: JSON.stringify({ event_id: eventId })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    if (data.urls && data.urls.length > 0) {
                                        showNotification(`${data.sent} sent successfully, ${data.failed} failed. Opening fallback URLs...`, 'orange');

                                        let openedCount = 0;
                                        let blockedCount = 0;

                                        data.urls.forEach((guestData, index) => {
                                            setTimeout(() => {
                                                try {
                                                    const newWindow = window.open(guestData.url, `_blank_whatsapp_${index}`, 'width=800,height=600');
                                                    if (newWindow) {
                                                        openedCount++;
                                                        showNotification(`✅ ${guestData.name} - WhatsApp opened`, 'green');
                                                    } else {
                                                        blockedCount++;
                                                        showNotification(`❌ ${guestData.name} - Popup blocked`, 'red');
                                                    }
                                                } catch (error) {
                                                    blockedCount++;
                                                    showNotification(`❌ ${guestData.name} - Error opening WhatsApp`, 'red');
                                                }

                                                if (index === data.urls.length - 1) {
                                                    setTimeout(() => {
                                                        if (blockedCount > 0) {
                                                            showNotification(`${openedCount} opened, ${blockedCount} blocked. Allow popups and try again.`, 'orange');
                                                            showManualWhatsAppLinks(data.urls);
                                                        } else {
                                                            showNotification(`✅ All ${openedCount} WhatsApp windows opened successfully!`, 'green');
                                                        }
                                                    }, 1000);
                                                }
                                            }, index * 3000);
                                        });
                                    } else {
                                        showNotification(`✅ QR codes sent successfully to ${data.sent} guests via WhatsApp!`, 'green');
                                    }
                                } else {
                                    showNotification('Failed to send WhatsApp invites', 'red');
                                }
                            })
                            .catch(error => {
                                showNotification('Error sending WhatsApp invites', 'red');
                            });
                        };

                        // Real-time search functionality
                        document.addEventListener('DOMContentLoaded', function() {
                            console.log('DOM loaded');
                            const searchInput = document.querySelector('input[name="search"]');
                            let searchTimeout;

                            searchInput.addEventListener('input', function() {
                                clearTimeout(searchTimeout);
                                searchTimeout = setTimeout(function() {
                                    // Auto-submit the search form
                                    searchInput.closest('form').submit();
                                }, 300); // 300ms delay
                            });

                            // Clear search when input is empty
                            searchInput.addEventListener('keydown', function(e) {
                                if (e.key === 'Escape') {
                                    searchInput.value = '';
                                    searchInput.closest('form').submit();
                                }
                            });

                            // Auto-refresh page every 10 seconds to show real-time check-in updates
                            setInterval(function() {
                                if (!document.hidden) { // Only refresh if page is visible
                                    location.reload();
                                }
                            }, 10000); // 10 seconds
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>