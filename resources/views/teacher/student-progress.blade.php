<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Reading Report: {{ $student->name }}
            </h2>
            <a href="{{ route('teacher.dashboard') }}" class="text-sm text-blue-600 hover:text-blue-800 font-bold">
                &larr; Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-8">
                    <div class="flex items-center space-x-4 mb-8">
                        <div class="bg-blue-100 p-3 rounded-2xl text-blue-600 text-2xl">📊</div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Summary</h3>
                            <p class="text-sm text-gray-500">Student ID: {{ $student->student_id }} • Joined
                                {{ $student->created_at->format('M Y') }}</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-gray-400 text-xs uppercase tracking-widest border-b">
                                    <th class="py-4 font-black">Book Title</th>
                                    <th class="py-4 font-black">Progress</th>
                                    <th class="py-4 font-black">Status</th>
                                    <th class="py-4 font-black">Last Active</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($progress as $entry)
                                    <tr class="group hover:bg-gray-50 transition">
                                        <td class="py-5">
                                            <div class="flex items-center space-x-3">
                                                <div
                                                    class="w-8 h-12 bg-gray-100 rounded shadow-inner overflow-hidden flex-shrink-0">
                                                    <img src="{{ $entry->book->cover_url }}"
                                                        class="w-full h-full object-cover">
                                                </div>
                                                <span class="font-bold text-gray-800">{{ $entry->book->title }}</span>
                                            </div>
                                        </td>
                                        <td class="py-5 w-48">
                                            <div class="flex items-center space-x-2">
                                                <div class="flex-1 bg-gray-100 rounded-full h-2">
                                                    <div class="bg-blue-500 h-2 rounded-full transition-all duration-500"
                                                        style="width: {{ $entry->percentage }}%"></div>
                                                </div>
                                                <span
                                                    class="text-xs font-black text-gray-500">{{ round($entry->percentage) }}%</span>
                                            </div>
                                        </td>
                                        <td class="py-5">
                                            @if($entry->is_completed)
                                                <span
                                                    class="bg-green-100 text-green-700 text-[10px] font-black px-2 py-1 rounded-full uppercase tracking-tighter">Completed</span>
                                            @else
                                                <span
                                                    class="bg-blue-100 text-blue-700 text-[10px] font-black px-2 py-1 rounded-full uppercase tracking-tighter">In
                                                    Progress</span>
                                            @endif
                                        </td>
                                        <td class="py-5">
                                            <span
                                                class="text-sm text-gray-500">{{ $entry->updated_at->diffForHumans() }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-10 text-center text-gray-400 italic">
                                            No reading activity recorded for this student yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>