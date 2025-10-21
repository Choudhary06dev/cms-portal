<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Edit Complaint</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('admin.complaints.update', $complaint) }}" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-sm mb-1">Type</label>
                            <input type="text" name="type" value="{{ old('type', $complaint->type) }}" class="w-full border-gray-300 rounded" />
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Description</label>
                            <textarea name="description" class="w-full border-gray-300 rounded" rows="4">{{ old('description', $complaint->description) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Location</label>
                            <input type="text" name="location" value="{{ old('location', $complaint->location) }}" class="w-full border-gray-300 rounded" />
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Assign Employee</label>
                            <select name="assigned_employee_id" class="w-full border-gray-300 rounded">
                                <option value="">— None —</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" @selected($complaint->assigned_employee_id == $emp->id)>#{{ $emp->id }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Status</label>
                            <select name="status" class="w-full border-gray-300 rounded">
                                @foreach(['NEW','IN_PROGRESS','RESOLVED','CLOSED','REOPENED'] as $st)
                                    <option value="{{ $st }}" @selected($complaint->status === $st)>{{ $st }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <button class="px-4 py-2 bg-gray-800 text-white rounded">Update</button>
                            <a href="{{ route('admin.complaints.index') }}" class="ml-2 text-gray-500">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


