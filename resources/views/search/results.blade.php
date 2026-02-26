<x-app-layout>
    <div>
        <h1 class="text-2xl md:text-3xl text-slate-800 font-bold mb-6">
            Search Results for "{{ $searchTerm }}"
        </h1>

        <div class="bg-white shadow-lg rounded-2xl border border-slate-200">
            <div class="p-6">
                @if($results->isEmpty())
                    <p class="text-center text-slate-500">No results found.</p>
                @else
                    <ul class="divide-y divide-slate-200">
                        @foreach($results as $result)
                            <li class="py-4">
                                <a href="{{ $result['url'] }}" class="flex items-center space-x-4 group">
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-slate-100">
                                            @if($result['type'] === 'Student')
                                                <svg class="h-5 w-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            @elseif($result['type'] === 'Invoice')
                                                <svg class="h-5 w-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            @elseif($result['type'] === 'Expense')
                                                <svg class="h-5 w-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-slate-900 truncate group-hover:text-blue-600">
                                            {{ $result['title'] }}
                                        </p>
                                        <p class="text-sm text-slate-500 truncate">
                                            {{ $result['subtitle'] }}
                                        </p>
                                    </div>
                                    <div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $result['type'] }}
                                        </span>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>