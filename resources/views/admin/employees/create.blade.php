<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Add Employee</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('admin.employees.store') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm mb-1">User</label>
                            <select name="user_id" class="w-full border-gray-300 rounded">
                                <option value="">— None —</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Department</label>
                            <input type="text" name="department" class="w-full border-gray-300 rounded" />
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Designation</label>
                            <input type="text" name="designation" class="w-full border-gray-300 rounded" />
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Biometric ID</label>
                            <input type="text" name="biometric_id" class="w-full border-gray-300 rounded" />
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Leave Quota</label>
                            <input type="number" name="leave_quota" value="12" class="w-full border-gray-300 rounded" />
                        </div>
                        <div>
                            <button class="px-4 py-2 bg-gray-800 text-white rounded">Save</button>
                            <a href="{{ route('admin.employees.index') }}" class="ml-2 text-gray-500">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


