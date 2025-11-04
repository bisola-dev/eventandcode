<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Events') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900">
                    <a href="{{ route('events.create') }}" class="block p-4 bg-pink-500 text-white rounded hover:bg-pink-600 text-center font-bold text-lg shadow-lg mb-4 inline-block" style="background-color: #ff1493;">Create Event</a>

                    <form method="GET" class="mb-4">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search events..." class="border border-gray-300 rounded px-4 py-2 w-full md:w-1/2">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded ml-2">Search</button>
                    </form>

                    <table class="w-full table-auto border-collapse border border-gray-300">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">Image</th>
                                <th class="px-4 py-2">Event Name</th>
                                <th class="px-4 py-2">Client</th>
                                <th class="px-4 py-2">Date</th>
                                <th class="px-4 py-2">Location</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($events as $event)
                            <tr>
                                <td class="border px-4 py-2">
                                    @if($event->image)
                                        <img src="{{ asset('storage/' . $event->image) }}" alt="Event Image" class="w-16 h-16 object-cover">
                                    @else
                                        No Image
                                    @endif
                                </td>
                                <td class="border px-4 py-2">{{ $event->name }}</td>
                                <td class="border px-4 py-2">{{ $event->client_name ?: 'N/A' }}</td>
                                <td class="border px-4 py-2">{{ $event->event_date }}</td>
                                <td class="border px-4 py-2">{{ $event->location }}</td>
                                <td class="border px-4 py-2 flex space-x-2">
                                    <a href="{{ route('events.edit', $event) }}" class="text-blue-500 hover:text-blue-700" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <a href="{{ route('guests.index', ['event' => $event->id]) }}" class="text-purple-500 hover:text-purple-700" title="Manage Guests">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('events.destroy', $event) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700" title="Delete" onclick="return confirm('Are you sure you want to delete this event?')">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </a>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    @if(request('search'))
                                        No events found matching "{{ request('search') }}".
                                    @else
                                        No events found.
                                    @endif
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Edit Event Modal -->
                    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
                        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                            <div class="mt-3">
                                <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Edit Event</h3>
                                <form id="editForm" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" id="eventId" name="event_id">

                                    <div class="mb-4">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Event Name</label>
                                        <input type="text" id="editName" name="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Client Name</label>
                                        <input type="text" id="editClientName" name="client_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Event Date</label>
                                        <input type="date" id="editEventDate" name="event_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Location</label>
                                        <input type="text" id="editLocation" name="location" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                                        <textarea id="editDescription" name="description" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Event Image</label>
                                        <input type="file" id="editImage" name="image" accept="image/*" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                        <p class="text-sm text-gray-500 mt-1">Leave empty to keep current image</p>
                                    </div>

                                    <div class="flex items-center justify-between pt-4">
                                        <button type="button" onclick="closeEditModal()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                            Cancel
                                        </button>
                                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                            Update Event
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <script>
                        function openEditModal(id, name, clientName, eventDate, location, description) {
                            document.getElementById('eventId').value = id;
                            document.getElementById('editName').value = name;
                            document.getElementById('editClientName').value = clientName || '';
                            document.getElementById('editEventDate').value = eventDate;
                            document.getElementById('editLocation').value = location;
                            document.getElementById('editDescription').value = description || '';
                            document.getElementById('editForm').action = `/events/${id}`;
                            document.getElementById('editModal').classList.remove('hidden');
                        }

                        function closeEditModal() {
                            document.getElementById('editModal').classList.add('hidden');
                        }

                        // Close modal when clicking outside
                        document.getElementById('editModal').addEventListener('click', function(e) {
                            if (e.target === this) {
                                closeEditModal();
                            }
                        });
                    </script>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>