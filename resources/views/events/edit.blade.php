<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Event') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('events.update', $event) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Event Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $event->name) }}" class="mt-1 block w-full" required>
                        </div>
                        <div class="mb-4">
                            <label for="client_name" class="block text-sm font-medium text-gray-700">Client Name</label>
                            <input type="text" name="client_name" id="client_name" value="{{ old('client_name', $event->client_name) }}" class="mt-1 block w-full">
                        </div>
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" id="description" class="mt-1 block w-full">{{ old('description', $event->description) }}</textarea>
                        </div>
                        <div class="mb-4">
                            <label for="event_date" class="block text-sm font-medium text-gray-700">Event Date</label>
                            <input type="datetime-local" name="event_date" id="event_date" value="{{ old('event_date', $event->event_date->format('Y-m-d\TH:i')) }}" class="mt-1 block w-full" required>
                        </div>
                        <div class="mb-4">
                            <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                            <input type="text" name="location" id="location" value="{{ old('location', $event->location) }}" class="mt-1 block w-full" required>
                        </div>
                        <div class="mb-4">
                            <label for="image" class="block text-sm font-medium text-gray-700">Event Image</label>
                            <input type="file" name="image" id="image" class="mt-1 block w-full" accept="image/*">
                            @if($event->image)
                                <img src="{{ asset('storage/' . $event->image) }}" alt="Event Image" class="mt-2 w-32 h-32 object-cover">
                            @endif
                        </div>
                        <button type="submit" class="bg-pink-500 text-white px-4 py-2 rounded font-bold" style="background-color: #ff1493;">Update Event</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>