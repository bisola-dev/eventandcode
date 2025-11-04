<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Guest') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Debug: Show current guest data -->
                    <div class="bg-blue-50 border border-blue-200 rounded p-4 mb-6">
                        <h3 class="text-sm font-medium text-blue-800 mb-2">Current Guest Data:</h3>
                        <p class="text-sm text-blue-700">Name: {{ $guest->name }}</p>
                        <p class="text-sm text-blue-700">Email: {{ $guest->email }}</p>
                        <p class="text-sm text-blue-700">Phone: {{ $guest->phone ?: 'Not set' }}</p>
                    </div>

                    @if($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <strong>Update Failed:</strong>
                            <ul class="list-disc list-inside mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('guests.update', $guest) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $guest->name) }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-pink-500 focus:border-pink-500 px-3 py-2" required>
                        </div>
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $guest->email) }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-pink-500 focus:border-pink-500 px-3 py-2" required>
                        </div>
                        <div class="mb-4">
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $guest->phone) }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-pink-500 focus:border-pink-500 px-3 py-2">
                        </div>
                        <button type="submit" class="bg-pink-500 text-white px-4 py-2 rounded font-bold hover:bg-pink-600 transition-colors" style="background-color: #ff1493;">Update Guest</button>
                        <a href="{{ route('guests.index', ['event' => $guest->event_id]) }}" class="bg-gray-500 text-white px-4 py-2 rounded ml-2 hover:bg-gray-600 transition-colors">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>