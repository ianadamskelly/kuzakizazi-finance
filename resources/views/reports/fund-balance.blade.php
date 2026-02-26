<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-black text-slate-800 mb-6">Fund Balance Report</h1>

        <!-- Filter Form -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-8">
            <form action="{{ route('reports.fund-balance') }}" method="GET" class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Start
                        Date</label>
                    <input type="date" name="start_date" value="{{ $startDate }}"
                        class="rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">End Date</label>
                    <input type="date" name="end_date" value="{{ $endDate }}"
                        class="rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 shadow-sm">
                        Generate Report
                    </button>
                </div>
            </form>
        </div>

        <!-- Report Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            @foreach($fundBalances as $key => $data)
                <div class="bg-white rounded-xl shadow-lg border border-slate-100 overflow-hidden">
                    <div class="p-5 border-b border-slate-100 bg-slate-50">
                        <h3 class="text-lg font-bold text-slate-800">{{ $data['label'] }}</h3>
                        <p class="text-xs text-slate-500 uppercase tracking-wide font-semibold mt-1">Fund Overview</p>
                    </div>
                    <div class="p-5 space-y-4">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-600">Total Collected</span>
                            <span class="font-bold text-green-600">+{{ number_format($data['income'], 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-600">Total Spent</span>
                            <span class="font-bold text-red-600">-{{ number_format($data['expense'], 2) }}</span>
                        </div>
                        <div class="pt-4 border-t border-slate-100 flex justify-between items-center">
                            <span class="font-bold text-slate-800">Available Balance</span>
                            <span class="text-lg font-black {{ $data['balance'] >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                                {{ number_format($data['balance'], 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h4 class="font-bold text-blue-800 mb-2">Note:</h4>
            <ul class="list-disc list-inside text-sm text-blue-700 space-y-1">
                <li><strong>Collections</strong> are calculated from recorded Student Payments.</li>
                <li><strong>Expenses</strong> include both recorded Expenses and Paid Salaries.</li>
                <li>Salaries are deducted from the <strong>Tuition Fees</strong> fund by default.</li>
            </ul>
        </div>
    </div>
</x-app-layout>