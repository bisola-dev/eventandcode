<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Event Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium">{{ $event->name }}</h3>
                    <p><strong>Description:</strong> {{ $event->description }}</p>
                    <p><strong>Date:</strong> {{ $event->event_date }}</p>
                    <p><strong>Location:</strong> {{ $event->location }}</p>
                    <a href="{{ route('events.edit', $event) }}" class="bg-green-500 text-white px-4 py-2 rounded mt-4 inline-block">Edit</a>
                    <a href="{{ route('guests.index', ['event' => $event->id]) }}" class="bg-blue-500 text-white px-4 py-2 rounded mt-4 inline-block ml-2">Manage Guests</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>