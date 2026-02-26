<x-app-layout>
    <div x-data="{ selectedInvoices: [] }">
        <!-- Page header -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Fee Management</h1>
            <div class="flex items-center space-x-2">
                <form x-show="selectedInvoices.length > 0" action="{{ route('invoices.bulk-destroy') }}" method="POST"
                    onsubmit="return confirm('Are you sure? This will reverse all selected charges and adjust student balances.');">
                    @csrf
                    @method('DELETE')
                    <template x-for="id in selectedInvoices" :key="id">
                        <input type="hidden" name="invoice_ids[]" :value="id">
                    </template>
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700">
                        Delete Selected (<span x-text="selectedInvoices.length"></span>)
                    </button>
                </form>
                <a href="{{ route('invoices.create') }}"
                    class="inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">
                    <span>Generate Invoices</span>
                </a>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white shadow-lg rounded-2xl border border-slate-200 p-4 mb-8">
            <form action="{{ route('invoices.index') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <input type="text" name="search" placeholder="Search by name or admission no..."
                        value="{{ $filters['search'] ?? '' }}" class="rounded-md border-slate-300">
                    <select name="grade_id" class="rounded-md border-slate-300">
                        <option value="">All Grades</option>
                        @foreach($grades as $grade)
                            <option value="{{ $grade->id }}" {{ ($filters['grade_id'] ?? '') == $grade->id ? 'selected' : '' }}>{{ $grade->name }}</option>
                        @endforeach
                    </select>
                    <select name="status" class="rounded-md border-slate-300">
                        <option value="">All Statuses</option>
                        <option value="unpaid" {{ ($filters['status'] ?? '') == 'unpaid' ? 'selected' : '' }}>Unpaid
                        </option>
                        <option value="partially_paid" {{ ($filters['status'] ?? '') == 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                        <option value="paid" {{ ($filters['status'] ?? '') == 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                    <div class="flex items-center space-x-2">
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">Filter</button>
                        <a href="{{ route('invoices.index') }}"
                            class="w-full text-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white shadow-lg rounded-2xl border border-slate-200">
            <div class="overflow-x-auto">
                <table class="table-auto w-full">
                    <thead class="text-xs font-semibold uppercase text-slate-500 bg-slate-50">
                        <tr>
                            <th class="p-4 w-12"><input type="checkbox"
                                    @click="$el.checked ? selectedInvoices = {{ $invoices->pluck('id') }} : selectedInvoices = []"
                                    class="rounded"></th>
                            <th class="p-4">
                                <div class="font-semibold text-left">Invoice #</div>
                            </th>
                            <th class="p-4">
                                <div class="font-semibold text-left">Student</div>
                            </th>
                            <th class="p-4">
                                <div class="font-semibold text-left">Grade</div>
                            </th>
                            <th class="p-4">
                                <div class="font-semibold text-left">Campus</div>
                            </th>
                            <th class="p-4">
                                <div class="font-semibold text-right">Amount</div>
                            </th>
                            <th class="p-4">
                                <div class="font-semibold text-right">Balance Due</div>
                            </th>
                            <th class="p-4">
                                <div class="font-semibold text-center">Status</div>
                            </th>
                            <th class="p-4">
                                <div class="font-semibold text-center">Due Date</div>
                            </th>
                            <th class="p-4"></th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-200">
                        @forelse ($invoices as $invoice)
                            <tr>
                                <td class="p-4 w-12"><input type="checkbox" :value="{{ $invoice->id }}"
                                        x-model="selectedInvoices" class="rounded"></td>
                                <td class="p-4">
                                    <div class="font-medium text-slate-800">
                                        INV-{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }}</div>
                                </td>
                                <td class="p-4">
                                    <div>{{ $invoice->student->first_name }} {{ $invoice->student->last_name }}</div>
                                </td>
                                <td class="p-4">
                                    <div>{{ $invoice->student->grade->name ?? 'N/A' }}</div>
                                </td>
                                <td class="p-4">
                                    <div>{{ $invoice->student->campus->name }}</div>
                                </td>
                                <td class="p-4">
                                    <div class="text-right font-medium text-slate-800">
                                        ${{ number_format($invoice->total_amount, 2) }}</div>
                                </td>
                                <td class="p-4">
                                    <div class="text-right font-medium text-red-500">
                                        ${{ number_format($invoice->balance_due, 2) }}</div>
                                </td>
                                <td class="p-4 text-center">
                                    <span @class(['px-2 py-1 text-xs font-semibold rounded-full', 'bg-green-100 text-green-800' => $invoice->status === 'paid', 'bg-yellow-100 text-yellow-800' => $invoice->status === 'partially_paid', 'bg-red-100 text-red-800' => $invoice->status === 'unpaid'])>{{ ucfirst(str_replace('_', ' ', $invoice->status)) }}</span>
                                </td>
                                <td class="p-4">
                                    <div class="text-center">
                                        {{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</div>
                                </td>
                                <td class="p-4"><a href="{{ route('invoices.show', $invoice) }}"
                                        class="font-medium text-blue-600 hover:text-blue-800">View</a></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="p-4 text-center text-slate-500">No invoices found matching your
                                    criteria.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-8">{{ $invoices->links() }}</div>
    </div>
</x-app-layout>