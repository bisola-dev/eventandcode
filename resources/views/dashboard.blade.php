<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Eventandcode Admin Dashboard</h3>
                    <div class="space-y-4">
                        @if(auth()->user() && (auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin'))
                        <a href="{{ route('events.index') }}" class="block p-4 rounded text-center font-bold text-xl shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                            Manage Events
                        </a>

                        @if(auth()->user() && auth()->user()->role === 'superadmin')
                        <a href="{{ route('users.index') }}" class="block p-4 rounded text-center font-bold text-xl shadow-lg" style="background: linear-gradient(135deg, #F59E0B, #8B5CF6); color: white;">
                            Manage Admins
                        </a>
                        @endif
                        @else
                        <a href="{{ route('events.index') }}" class="block p-4 rounded text-center font-bold text-xl shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                            My Events
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
