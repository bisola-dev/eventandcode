<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Guest Details: ') . $guest->name }}
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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Guest Information</h3>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Name</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $guest->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $guest->email }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Phone</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $guest->phone ?: 'Not provided' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">QR Code</label>
                                    <p class="mt-1 text-sm text-gray-900 font-mono">{{ $guest->qr_code }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Checked In</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        <span class="px-2 py-1 rounded text-xs font-medium {{ $guest->checked_in ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $guest->checked_in ? 'Yes' : 'No' }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold mb-4">Event Information</h3>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Event Name</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $guest->event->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Event Date</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $guest->event->event_date ? \Carbon\Carbon::parse($guest->event->event_date)->format('M j, Y') : 'Not set' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Location</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $guest->event->location ?: 'Not set' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Client</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $guest->event->user->name }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex space-x-4">
                        <a href="{{ route('guests.index', ['event' => $guest->event_id]) }}" class="bg-gray-500 text-white px-4 py-2 rounded font-bold">Back to Guests</a>
                        <a href="{{ route('guests.edit', $guest) }}" class="bg-blue-500 text-white px-4 py-2 rounded font-bold">Edit Guest</a>
                        <form action="{{ route('guests.destroy', $guest) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this guest?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded font-bold">Delete Guest</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>