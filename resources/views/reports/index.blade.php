<x-app-layout>
    <div>
        <h1 class="text-2xl md:text-3xl text-slate-800 font-bold mb-8">Financial Reports</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Fee Defaulters Report Card -->
            <div class="bg-white shadow-lg rounded-2xl border border-slate-200 p-6">
                <h2 class="text-lg font-semibold text-slate-800">Fee Defaulters</h2>
                <p class="text-sm text-slate-500 mt-1">View a list of all students with outstanding balances.</p>
                <div class="mt-4"><a href="{{ route('reports.fee-defaulters') }}"
                        class="text-sm font-medium text-blue-600 hover:text-blue-800">Generate Report &rarr;</a></div>
            </div>
            <!-- Expense Report -->
            <a href="{{ route('reports.expense-report') }}"
                class="group block bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md hover:border-blue-300 transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-red-100 p-3 rounded-lg group-hover:bg-red-200 transition-colors">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-semibold uppercase text-slate-400">Expense</span>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2 group-hover:text-blue-600 transition-colors">Expense
                    Report</h3>
                <p class="text-sm text-slate-500">Breakdown of expenses by category.</p>
            </a>

            <!-- Fund Balance Report (New) -->
            <a href="{{ route('reports.fund-balance') }}"
                class="group block bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md hover:border-blue-300 transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-purple-100 p-3 rounded-lg group-hover:bg-purple-200 transition-colors">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                    <span class="text-xs font-semibold uppercase text-slate-400">Fund Tracking</span>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2 group-hover:text-blue-600 transition-colors">Fund
                    Balance</h3>
                <p class="text-sm text-slate-500">Track collections vs expenses per fund.</p>
            </a>
        </div>
    </div>
</x-app-layout>