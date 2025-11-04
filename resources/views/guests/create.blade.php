<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Guest to ') . $event->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('guests.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="event_id" value="{{ $event->id }}">
                        <div id="guests-container">
                            <div class="guest-row mb-6 p-4 border rounded">
                                <h3 class="text-lg font-semibold mb-4">Guest 1</h3>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Name</label>
                                    <input type="text" name="guests[0][name]" class="mt-1 block w-full" required>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Email</label>
                                    <input type="email" name="guests[0][email]" class="mt-1 block w-full" required>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Phone</label>
                                    <input type="text" name="guests[0][phone]" class="mt-1 block w-full">
                                </div>
                                <button type="button" class="remove-guest bg-red-500 text-white px-3 py-1 rounded" style="display: none;">Remove</button>
                            </div>
                        </div>
                        <button type="button" id="add-guest" class="bg-blue-500 text-white px-4 py-2 rounded font-bold mr-2">Add Another Guest</button>
                        <button type="submit" class="bg-pink-500 text-white px-4 py-2 rounded font-bold" style="background-color: #ff1493;">Add Guests</button>
                    </form>

                    <script>
                        let guestIndex = 1;
                        document.getElementById('add-guest').addEventListener('click', function() {
                            const container = document.getElementById('guests-container');
                            const newRow = document.createElement('div');
                            newRow.className = 'guest-row mb-6 p-4 border rounded';
                            newRow.innerHTML = `
                                <h3 class="text-lg font-semibold mb-4">Guest ${guestIndex + 1}</h3>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Name</label>
                                    <input type="text" name="guests[${guestIndex}][name]" class="mt-1 block w-full" required>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Email</label>
                                    <input type="email" name="guests[${guestIndex}][email]" class="mt-1 block w-full" required>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Phone</label>
                                    <input type="text" name="guests[${guestIndex}][phone]" class="mt-1 block w-full">
                                </div>
                                <button type="button" class="remove-guest bg-red-500 text-white px-3 py-1 rounded">Remove</button>
                            `;
                            container.appendChild(newRow);
                            guestIndex++;
                            updateRemoveButtons();
                        });

                        document.addEventListener('click', function(e) {
                            if (e.target.classList.contains('remove-guest')) {
                                e.target.closest('.guest-row').remove();
                                updateRemoveButtons();
                                renumberGuests();
                            }
                        });

                        function updateRemoveButtons() {
                            const rows = document.querySelectorAll('.guest-row');
                            rows.forEach((row, index) => {
                                const removeBtn = row.querySelector('.remove-guest');
                                if (rows.length > 1) {
                                    removeBtn.style.display = 'inline-block';
                                } else {
                                    removeBtn.style.display = 'none';
                                }
                            });
                        }

                        function renumberGuests() {
                            const rows = document.querySelectorAll('.guest-row');
                            rows.forEach((row, index) => {
                                const h3 = row.querySelector('h3');
                                h3.textContent = `Guest ${index + 1}`;
                                const inputs = row.querySelectorAll('input');
                                inputs.forEach(input => {
                                    const name = input.name.replace(/\[\d+\]/, `[${index}]`);
                                    input.name = name;
                                });
                            });
                            guestIndex = rows.length;
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>