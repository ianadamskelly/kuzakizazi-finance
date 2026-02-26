<x-app-layout>
    <div>
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Expense Report</h1>
            <a href="{{ route('reports.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">&larr; Back to Reports</a>
        </div>

        <!-- Filter Form -->
        <div class="bg-white shadow-lg rounded-2xl border border-slate-200 p-6 mb-8">
            <form action="{{ route('reports.expense-report') }}" method="GET" class="flex flex-col md:flex-row md:items-end md:space-x-4 space-y-4 md:space-y-0">
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

        <!-- Report Table -->
        <div class="bg-white shadow-lg rounded-2xl border border-slate-200">
            <div class="overflow-x-auto">
                <table class="table-auto w-full">
                    <thead class="text-xs font-semibold uppercase text-slate-500 bg-slate-50">
                        <tr>
                            <th class="p-4"><div class="font-semibold text-left">Date</div></th>
                            <th class="p-4"><div class="font-semibold text-left">Category</div></th>
                            <th class="p-4"><div class="font-semibold text-left">Vendor</div></th>
                            <th class="p-4"><div class="font-semibold text-left">Campus</div></th>
                            <th class="p-4"><div class="font-semibold text-right">Amount</div></th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-200">
                        @forelse ($expenses as $expense)
                            <tr>
                                <td class="p-4"><div>{{ \Carbon\Carbon::parse($expense->expense_date)->format('M d, Y') }}</div></td>
                                <td class="p-4"><div class="font-medium text-slate-800">{{ $expense->category }}</div></td>
                                <td class="p-4"><div>{{ $expense->vendor ?? 'N/A' }}</div></td>
                                <td class="p-4"><div>{{ $expense->campus->name }}</div></td>
                                <td class="p-4"><div class="text-right font-medium text-red-600">${{ number_format($expense->amount, 2) }}</div></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-4 text-center text-slate-500">No expenses found in this period.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot class="text-sm font-semibold text-slate-700">
                        <tr>
                            <td colspan="4" class="p-4 text-right">Total Expenses:</td>
                            <td class="p-4 text-right">${{ number_format($totalExpenses, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>