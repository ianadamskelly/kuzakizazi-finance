<x-app-layout>
    <div>
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Employees</h1>
            <a href="{{ route('employees.create') }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">
                <svg class="w-4 h-4 fill-current -ml-1 mr-2" viewBox="0 0 16 16"><path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"></path></svg>
                <span>Add Employee</span>
            </a>
        </div>
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert"><p>{{ session('success') }}</p></div>
        @endif
        <div class="bg-white shadow-lg rounded-2xl border border-slate-200">
            <div class="overflow-x-auto">
                <table class="table-auto w-full">
                    <thead class="text-xs font-semibold uppercase text-slate-500 bg-slate-50">
                        <tr>
                            <th class="p-4"><div class="font-semibold text-left">Name</div></th>
                            <th class="p-4"><div class="font-semibold text-left">Job Title / Category</div></th>
                            <th class="p-4"><div class="font-semibold text-left">Campus</div></th>
                            <th class="p-4"><div class="font-semibold text-left">System Access</div></th>
                            <th class="p-4"><div class="font-semibold text-center">Actions</div></th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-200">
                        @forelse ($employees as $employee)
                            <tr>
                                <td class="p-4 align-top">
                                    <div class="font-medium text-slate-800">{{ $employee->first_name }} {{ $employee->last_name }}</div>
                                    @if($employee->user)
                                        <div class="text-xs text-slate-500">{{ $employee->user->email }}</div>
                                    @endif
                                </td>
                                <td class="p-4 align-top">
                                    <div>{{ $employee->job_title }}</div>
                                    <div class="text-xs text-slate-500">{{ $employee->employeeCategory?->name }}</div>
                                </td>
                                <td class="p-4 align-top"><div>{{ $employee->campus->name ?? 'All Campuses' }}</div></td>
                                <td class="p-4 align-top">
                                    @if($employee->user && $employee->user->roles->isNotEmpty())
                                        <div class="flex flex-col items-start">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">{{ $employee->user->roles->first()->name }}</span>
                                            @if($employee->user->permissions->isNotEmpty())
                                                <div class="text-xs text-slate-500 mt-1">
                                                    + {{ $employee->user->permissions->pluck('name')->implode(', ') }}
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-slate-400">N/A</span>
                                    @endif
                                </td>
                                <td class="p-4 align-top">
                                    <div class="flex justify-center items-center space-x-4">
                                        <a href="{{ route('employees.edit', $employee) }}" class="font-medium text-blue-600 hover:text-blue-800">Edit</a>
                                        <form action="{{ route('employees.destroy', $employee) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="font-medium text-red-500 hover:text-red-700">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-4 text-center text-slate-500">No employees found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-8">{{ $employees->links() }}</div>
    </div>
</x-app-layout>