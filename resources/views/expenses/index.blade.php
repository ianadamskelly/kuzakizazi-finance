<x-app-layout>
    <div>
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Expenses</h1>
            <a href="{{ route('expenses.create') }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">
                <svg class="w-4 h-4 fill-current -ml-1 mr-2" viewBox="0 0 16 16"><path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"></path></svg>
                <span>Add Expense</span>
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
                            <th class="p-4"><div class="font-semibold text-left">Date</div></th>
                            <th class="p-4"><div class="font-semibold text-left">Category</div></th>
                            <th class="p-4"><div class="font-semibold text-left">Vendor</div></th>
                            <th class="p-4"><div class="font-semibold text-left">Campus</div></th>
                            <th class="p-4"><div class="font-semibold text-right">Amount</div></th>
                            <th class="p-4"><div class="font-semibold text-center">Receipt</div></th>
                            <th class="p-4"><div class="font-semibold text-center">Actions</div></th>
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
                                <td class="p-4 text-center">
                                    @if($expense->receipt_path)
                                        <a href="{{ Storage::url($expense->receipt_path) }}" target="_blank" class="font-medium text-blue-600 hover:text-blue-800">View</a>
                                    @else
                                        <span class="text-slate-400">N/A</span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <div class="flex justify-center items-center space-x-4">
                                        <a href="{{ route('expenses.edit', $expense) }}" class="font-medium text-blue-600 hover:text-blue-800">Edit</a>
                                        <form action="{{ route('expenses.destroy', $expense) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="font-medium text-red-500 hover:text-red-700">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="p-4 text-center text-slate-500">No expenses found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-8">{{ $expenses->links() }}</div>
    </div>
</x-app-layout>