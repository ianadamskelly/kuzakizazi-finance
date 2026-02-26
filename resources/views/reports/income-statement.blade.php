<x-app-layout>
    <div>
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Income Statement</h1>
            <a href="{{ route('reports.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">&larr; Back to Reports</a>
        </div>

        <!-- Filter Form -->
        <div class="bg-white shadow-lg rounded-2xl border border-slate-200 p-6 mb-8">
            <form action="{{ route('reports.income-statement') }}" method="GET" class="flex flex-col md:flex-row md:items-end md:space-x-4 space-y-4 md:space-y-0">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-slate-700">Start Date</label>
                    <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-slate-700">End Date</label>
                    <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm">
                </div>
                <div class="flex items-center space-x-2">
                    <button type="submit" class="inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">Filter</button>
                    <button type="submit" name="export" value="pdf" class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">Export PDF</button>
                </div>
            </form>
        </div>

        <!-- Report Body -->
        <div class="bg-white shadow-lg rounded-2xl border border-slate-200 p-6">
            <h2 class="text-lg font-semibold text-slate-800 mb-4">Report for {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</h2>
            
            <div class="mb-6">
                <h3 class="text-md font-semibold text-slate-700 border-b pb-2 mb-2">Revenue</h3>
                <div class="flex justify-between items-center">
                    <span>Total Payments Received</span>
                    <span class="font-bold text-green-600">${{ number_format($revenue, 2) }}</span>
                </div>
            </div>

            <div class="mb-6">
                <h3 class="text-md font-semibold text-slate-700 border-b pb-2 mb-2">Expenses</h3>
                @forelse($expenses as $expense)
                    <div class="flex justify-between items-center text-sm py-1">
                        <span>{{ $expense->category }}</span>
                        <span>${{ number_format($expense->total, 2) }}</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No expenses recorded in this period.</p>
                @endforelse
                <div class="flex justify-between items-center font-bold border-t pt-2 mt-2">
                    <span>Total Expenses</span>
                    <span class="text-red-600">${{ number_format($totalExpenses, 2) }}</span>
                </div>
            </div>

            <div class="flex justify-between items-center font-bold text-lg border-t-2 pt-4 mt-4">
                <span>Net Profit</span>
                <span @class(['', 'text-red-600' => $netProfit < 0, 'text-green-600' => $netProfit >= 0])>
                    ${{ number_format($netProfit, 2) }}
                </span>
            </div>
        </div>
    </div>
</x-app-layout>