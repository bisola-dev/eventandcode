<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Admins') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded font-semibold">
                            ✅ {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    <h3 class="text-lg font-medium mb-4">Add New Admin</h3>
                    <form action="{{ route('users.store') }}" method="POST" class="mb-6 p-4 bg-gray-100 rounded">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                                <input type="text" name="name" id="name" class="mt-1 block w-full border-gray-300 rounded" required>
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" name="email" id="email" class="mt-1 block w-full border-gray-300 rounded" required>
                            </div>
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                                <input type="password" name="password" id="password" class="mt-1 block w-full border-gray-300 rounded" required>
                            </div>
                            <div>
                                <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                                <select name="role" id="role" class="mt-1 block w-full border-gray-300 rounded" required>
                                    <option value="admin">Admin</option>
                                    <option value="superadmin">Super Admin</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="mt-4 bg-pink-500 text-white px-4 py-2 rounded font-bold" style="background-color: #ff1493;">Add Admin</button>
                    </form>

                    <h3 class="text-lg font-medium mb-4">Existing Admins</h3>
                    <table class="w-full table-auto border-collapse border border-gray-300">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="border border-gray-300 px-4 py-2 text-left">Name</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Email</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Role</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Created At</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="border border-gray-300 px-4 py-2">{{ $user->name }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $user->email }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ ucfirst($user->role) }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $user->created_at->format('Y-m-d H:i') }}</td>
                                <td class="border border-gray-300 px-4 py-2">
                                    <div class="flex space-x-2">
                                        @if($user->id !== auth()->id())
                                        <button onclick="openEditUserModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->email }}', '{{ $user->role }}')" class="text-blue-500 hover:text-blue-700" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700" title="Delete" onclick="return confirm('Are you sure you want to delete this user?')">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                        @else
                                        <span class="text-gray-500 text-sm">Current User</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Edit User Modal -->
                    <div id="editUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center h-full w-full hidden z-50">
                        <div class="p-6 border w-full max-w-md shadow-lg rounded-lg bg-white max-h-[90vh] overflow-y-auto">
                            <div class="mt-3">
                                <h3 class="text-lg font-medium text-gray-900 mb-4" id="userModalTitle">Edit User</h3>
                                <form id="editUserForm" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" id="userId" name="user_id">

                                    <div class="mb-4">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                                        <input type="text" id="editUserName" name="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                                        <input type="email" id="editUserEmail" name="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Role</label>
                                        <select id="editUserRole" name="role" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                            <option value="admin">Admin</option>
                                            <option value="superadmin">Super Admin</option>
                                        </select>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">New Password (leave empty to keep current)</label>
                                        <input type="password" id="editUserPassword" name="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                        <p class="text-sm text-gray-500 mt-1">Only fill if you want to change the password</p>
                                    </div>

                                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                                        <button type="button" onclick="closeEditUserModal()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded focus:outline-none focus:shadow-outline border-2 border-red-600">
                                            Cancel
                                        </button>
                                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded focus:outline-none focus:shadow-outline border-2" style="background-color: #62ff14ff; border-color: #ff1493;">
                                            Update User
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <script>
                        function openEditUserModal(id, name, email, role) {
                            document.getElementById('userId').value = id;
                            document.getElementById('editUserName').value = name;
                            document.getElementById('editUserEmail').value = email;
                            document.getElementById('editUserRole').value = role;
                            document.getElementById('editUserPassword').value = '';
                            document.getElementById('editUserForm').action = `/users/${id}`;
                            const modal = document.getElementById('editUserModal');
                            modal.style.display = 'block';
                            modal.classList.remove('hidden');
                        }

                        function closeEditUserModal() {
                            const modal = document.getElementById('editUserModal');
                            modal.style.display = 'none';
                            modal.classList.add('hidden');
                        }

                        // Close modal when clicking outside
                        document.addEventListener('DOMContentLoaded', function() {
                            const modal = document.getElementById('editUserModal');
                            if (modal) {
                                modal.addEventListener('click', function(e) {
                                    if (e.target === this) {
                                        closeEditUserModal();
                                    }
                                });
                            }
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>