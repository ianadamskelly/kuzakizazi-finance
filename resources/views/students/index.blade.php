<x-app-layout>
    <div>
        <!-- Page header -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Students</h1>
            <a href="{{ route('students.create') }}"
                class="inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">
                <svg class="w-4 h-4 fill-current -ml-1 mr-2" viewBox="0 0 16 16">
                    <path
                        d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z">
                    </path>
                </svg>
                <span>Add Student</span>
            </a>
        </div>

        <!-- Session Messages -->
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <!-- Search & Filter Form -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-6">
            <form action="{{ route('students.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                <div class="flex-grow">
                    <label for="search" class="sr-only">Search</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" name="search" id="search"
                            class="pl-10 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            placeholder="Search by name or admission number..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="w-full md:w-1/4">
                    <label for="grade_id" class="sr-only">Filter by Grade</label>
                    <select name="grade_id" id="grade_id"
                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">All Grades</option>
                        @foreach($grades as $grade)
                            <option value="{{ $grade->id }}" {{ request('grade_id') == $grade->id ? 'selected' : '' }}>
                                {{ $grade->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'grade']))
                        <a href="{{ route('students.index') }}"
                            class="inline-flex justify-center py-2 px-4 border border-slate-300 shadow-sm text-sm font-medium rounded-lg text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white shadow-lg rounded-2xl border border-slate-200">
            <div class="overflow-x-auto">
                <table class="table-auto w-full">
                    <thead class="text-xs font-semibold uppercase text-slate-500 bg-slate-50">
                        <tr>
                            <th class="p-4 whitespace-nowrap">
                                <div class="font-semibold text-left">Name</div>
                            </th>
                            <th class="p-4 whitespace-nowrap">
                                <div class="font-semibold text-left">Campus</div>
                            </th>
                            <th class="p-4 whitespace-nowrap">
                                <div class="font-semibold text-left">Category</div>
                            </th>
                            <th class="p-4 whitespace-nowrap">
                                <div class="font-semibold text-right">Account Balance</div>
                            </th>
                            <th class="p-4 whitespace-nowrap">
                                <div class="font-semibold text-center">Actions</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-200">
                        @forelse ($students as $student)
                            <tr>
                                <td class="p-4">
                                    <div class="font-medium text-slate-800">{{ $student->first_name }}
                                        {{ $student->last_name }}
                                    </div>
                                    <div class="text-xs text-slate-500">{{ $student->admission_number }}</div>
                                </td>
                                <td class="p-4">
                                    <div>{{ $student->campus?->name }}</div>
                                </td>
                                <td class="p-4">
                                    <div>{{ $student->studentCategory?->name }}</div>
                                </td>
                                <td class="p-4 text-right">
                                    <span @class(['font-bold', 'text-red-600' => $student->balance > 0, 'text-green-600' => $student->balance < 0])>
                                        ${{ number_format(abs($student->balance), 2) }}
                                        @if($student->balance < 0) (Credit) @endif
                                    </span>
                                </td>
                                <td class="p-4">
                                    <div class="flex justify-center items-center space-x-4">
                                        <a href="{{ route('students.show', $student) }}"
                                            class="font-medium text-slate-600 hover:text-slate-800">View</a>
                                        <a href="{{ route('students.edit', $student) }}"
                                            class="font-medium text-blue-600 hover:text-blue-800">Edit</a>
                                        <form action="{{ route('students.destroy', $student) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this student?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="font-medium text-red-500 hover:text-red-700">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-4 text-center text-slate-500">No students found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $students->links() }}
        </div>
    </div>
</x-app-layout>