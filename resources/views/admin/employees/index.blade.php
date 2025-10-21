<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Employees</h2>
            <a href="{{ route('admin.employees.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-white border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-200 focus:bg-gray-700 dark:focus:bg-gray-200 active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none">Add Employee</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr>
                                <th class="text-left p-2">#</th>
                                <th class="text-left p-2">User</th>
                                <th class="text-left p-2">Department</th>
                                <th class="text-left p-2">Designation</th>
                                <th class="text-left p-2">Biometric</th>
                                <th class="text-left p-2">Leave Quota</th>
                                <th class="text-left p-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employees as $employee)
                                <tr class="border-t border-gray-200 dark:border-gray-700">
                                    <td class="p-2">{{ $employee->id }}</td>
                                    <td class="p-2">{{ optional($employee->user)->name }}<div class="text-xs text-gray-400">{{ optional($employee->user)->email }}</div></td>
                                    <td class="p-2">{{ $employee->department }}</td>
                                    <td class="p-2">{{ $employee->designation }}</td>
                                    <td class="p-2">{{ $employee->biometric_id }}</td>
                                    <td class="p-2">{{ $employee->leave_quota }}</td>
                                    <td class="p-2">
                                        <a href="{{ route('admin.employees.edit', $employee) }}" class="text-blue-500">Edit</a>
                                        <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Delete employee?')" class="text-red-500 ml-2">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="p-4 text-center text-gray-400">No employees found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $employees->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


