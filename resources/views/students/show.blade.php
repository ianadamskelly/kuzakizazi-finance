<x-app-layout>
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-start mb-8">
            <div>
                <h1 class="text-3xl font-bold text-slate-800">{{ $student->first_name }} {{ $student->last_name }}</h1>
                <div class="mt-2 text-slate-500 space-x-4">
                    <span><span class="font-semibold text-slate-700">ID:</span> {{ $student->admission_number }}</span>
                    <span>&bull;</span>
                    <span><span class="font-semibold text-slate-700">Grade:</span>
                        {{ $student->grade->name ?? 'N/A' }}</span>
                    <span>&bull;</span>
                    <span><span class="font-semibold text-slate-700">Campus:</span> {{ $student->campus->name }}</span>
                    <span>&bull;</span>
                    <span
                        class="px-2 py-0.5 rounded text-xs font-semibold {{ $student->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ ucfirst($student->status) }}
                    </span>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('students.statement', $student) }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 rounded-lg font-semibold text-slate-700 hover:bg-slate-50 shadow-sm transition-all">
                    <svg class="w-5 h-5 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Statement
                </a>
                <a href="{{ route('students.edit', $student) }}"
                    class="px-4 py-2 bg-white border border-slate-300 rounded-lg font-semibold text-slate-700 hover:bg-slate-50 shadow-sm transition-all">
                    Edit Profile
                </a>
            </div>
        </div>

        <!-- Student Financial Overview Card -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-lg font-semibold text-slate-800">Financial Overview</h3>
                <p class="text-sm text-slate-500">Real-time categorized balance breakdown</p>
            </div>

            <div class="p-6">
                <!-- Main Balance Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

                    <!-- Tuition/Fees Balance -->
                    <div class="p-4 rounded-lg bg-blue-50 border border-blue-100">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-blue-600">Tuition & Fees</span>
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="12 14l9-5-9-5-9 5 9 5z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z">
                                </path>
                            </svg>
                        </div>
                        <div class="text-2xl font-bold text-blue-900">
                            ${{ number_format($student->fees_balance, 2) }}
                        </div>
                    </div>

                    <!-- Food/Canteen Balance -->
                    <div class="p-4 rounded-lg bg-orange-50 border border-orange-100">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-orange-600">Food &
                                Canteen</span>
                            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                        <div class="text-2xl font-bold text-orange-900">
                            ${{ number_format($student->food_balance, 2) }}
                        </div>
                    </div>

                    <!-- Transport Balance -->
                    <div class="p-4 rounded-lg bg-purple-50 border border-purple-100">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-purple-600">Transport</span>
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                            </svg>
                        </div>
                        <div class="text-2xl font-bold text-purple-900">
                            ${{ number_format($student->transport_balance, 2) }}
                        </div>
                    </div>

                    <!-- Other/Admin Balance -->
                    <div class="p-4 rounded-lg bg-slate-50 border border-slate-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-600">Others</span>
                            <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z">
                                </path>
                            </svg>
                        </div>
                        <div class="text-2xl font-bold text-slate-900">
                            ${{ number_format($student->others_balance ?? ($student->assessment_balance + $student->uniform_balance), 2) }}
                        </div>
                    </div>

                </div>

                <!-- Total Outstanding Section -->
                <div
                    class="flex flex-col md:flex-row items-center justify-between p-6 bg-slate-900 rounded-xl text-white">
                    <div>
                        <p class="text-slate-400 text-sm font-medium">Total Outstanding Balance</p>
                        <p class="text-xs text-slate-500 italic">Combined sum of all sub-accounts</p>
                    </div>
                    <div class="mt-4 md:mt-0 text-3xl font-black text-white">
                        ${{ number_format($student->balance, 2) }}
                    </div>
                </div>
            </div>

            <!-- Quick Action Footer -->
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex gap-3">
                {{-- Placeholder route --}}
                <a href="#"
                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold transition-colors">
                    Receive Payment
                </a>
                <button
                    class="px-4 py-2 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 rounded-lg text-sm font-semibold transition-colors">
                    Generate Statement
                </button>
            </div>
        </div>

        <!-- Recent Activity Tabs (Invoices / Payments) could go here -->
    </div>
</x-app-layout>