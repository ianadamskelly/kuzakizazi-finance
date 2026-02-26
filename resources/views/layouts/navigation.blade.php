<header class="bg-slate-50 border-b border-slate-200 p-4 flex justify-between items-center">
    <!-- Campus Switcher -->
    @can('view all campuses')
        <form action="{{ route('dashboard.switch-campus') }}" method="POST" class="relative">
            @csrf
            <select name="campus_id" onchange="this.form.submit()" class="pl-10 pr-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="all" @if($selectedCampusId == 'all') selected @endif>All Campuses</option>
                @foreach($campuses as $campus)
                    <option value="{{ $campus->id }}" @if($selectedCampusId == $campus->id) selected @endif>{{ $campus->name }}</option>
                @endforeach
            </select>
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m-1 4h1m4-8h1m-1 4h1m-1 4h1M9 3a2 2 0 012-2h2a2 2 0 012 2v1m-6 0V3m6 0V3"></path></svg>
            </div>
        </form>
    @else
        <div></div> <!-- Placeholder to keep alignment -->
    @endcan

    <!-- Right side controls -->
    <div class="flex items-center space-x-4">
        <!-- Search Form -->
        <form action="{{ route('search') }}" method="GET" class="relative">
            <svg class="w-5 h-5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input type="text" name="query" placeholder="Search..." value="{{ request('query') }}" class="bg-white border border-slate-300 rounded-lg pl-10 pr-4 py-2 w-64 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </form>

        <button class="p-2 rounded-full hover:bg-slate-200">
            <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
        </button>
        
        <!-- User Dropdown -->
        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button class="flex items-center space-x-2">
                    <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=7F9CF5&background=EBF4FF" alt="">
                    <div class="font-medium text-sm text-slate-700 hidden sm:block">{{ Auth::user()->name }}</div>
                    <svg class="h-4 w-4 fill-current text-slate-500 hidden sm:block" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                </button>
            </x-slot>
            <x-slot name="content">
                <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>
    </div>
</header>