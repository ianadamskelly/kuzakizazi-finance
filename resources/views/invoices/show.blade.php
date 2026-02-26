<x-app-layout>
    <div class="max-w-4xl mx-auto">
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif
        <div class="bg-white shadow-lg rounded-2xl border border-slate-200">
            <!-- Header -->
            <div class="px-6 py-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-800">Invoice
                            INV-{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }}</h2>
                        <p class="text-sm text-slate-500">Due:
                            {{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}
                        </p>
                    </div>
                    <div class="text-right">
                        <span @class(['px-3 py-1 text-sm font-semibold rounded-full', 'bg-green-100 text-green-800' => $invoice->status === 'paid', 'bg-yellow-100 text-yellow-800' => $invoice->status === 'partially_paid', 'bg-red-100 text-red-800' => $invoice->status === 'unpaid'])>{{ ucfirst(str_replace('_', ' ', $invoice->status)) }}</span>
                    </div>
                </div>
            </div>
            <!-- Body -->
            <div class="p-6 border-t border-slate-200">
                <div class="mb-6">
                    <h4 class="text-slate-600 font-semibold">Billed To:</h4>
                    <p class="text-slate-800">{{ $invoice->student->first_name }} {{ $invoice->student->last_name }}</p>
                    <p class="text-sm text-slate-500">{{ $invoice->student->campus->name }}</p>
                </div>
                <table class="table-auto w-full mb-6">
                    <thead class="text-xs uppercase text-slate-500 bg-slate-50 rounded-sm">
                        <tr>
                            <th class="p-2 font-semibold text-left">Description</th>
                            <th class="p-2 font-semibold text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-200">
                        @foreach($invoice->items as $item)
                            <tr>
                                <td class="p-2">{{ $item->description }}</td>
                                <td class="p-2 text-right">${{ number_format($item->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="text-sm font-semibold text-slate-700">
                        <tr>
                            <td class="p-2 text-right border-t-2 border-slate-200">Total:</td>
                            <td class="p-2 text-right border-t-2 border-slate-200">
                                ${{ number_format($invoice->items->sum('amount'), 2) }}</td>
                        </tr>
                        <tr>
                            <td class="p-2 text-right">Balance Due:</td>
                            <td class="p-2 text-right text-red-500">${{ number_format($invoice->balance_due, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
                <h4 class="text-slate-600 font-semibold mb-2">Payments</h4>
                @if($invoice->payments->isNotEmpty())
                    <ul class="divide-y divide-slate-200 border border-slate-200 rounded-md">
                        @foreach($invoice->payments as $payment)
                            <li class="p-3 flex justify-between items-center text-sm">
                                <span>Paid on {{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }} via
                                    {{ $payment->payment_method }} for <span
                                        class="font-bold text-slate-700">{{ ucfirst($payment->category) }}</span></span>
                                <span class="font-semibold">${{ number_format($payment->amount_paid, 2) }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-slate-500">No payments recorded for this specific invoice.</p>
                @endif
            </div>
            <!-- Record Payment Form -->
            @if($invoice->balance_due > 0)
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
                    <form action="{{ route('payments.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="student_id" value="{{ $invoice->student_id }}">
                        <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">

                        <h4 class="text-slate-600 font-semibold mb-4">Record a New Payment for this Invoice</h4>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label for="category" class="block text-sm font-medium text-slate-700">Category
                                    (Fund)</label>
                                <select name="category" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm"
                                    required>
                                    <option value="fees">Tuition Fees</option>
                                    <option value="food">Canteen</option>
                                    <option value="transport">Transport</option>
                                    <option value="others">Others</option>
                                </select>
                            </div>
                            <div>
                                <label for="amount" class="block text-sm font-medium text-slate-700">Amount</label>
                                <input type="number" name="amount" step="0.01"
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm" required>
                            </div>
                            <div>
                                <label for="payment_date" class="block text-sm font-medium text-slate-700">Payment
                                    Date</label>
                                <input type="date" name="payment_date" value="{{ date('Y-m-d') }}"
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm" required>
                            </div>
                            <div>
                                <label for="payment_method" class="block text-sm font-medium text-slate-700">Method</label>
                                <select name="payment_method"
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm" required>
                                    <option>Bank Transfer</option>
                                    <option>Cash</option>
                                    <option>Online</option>
                                    <option>M-Pesa</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex justify-between items-center mt-4">
                            <a href="{{ route('students.statement', $invoice->student_id) }}" target="_blank"
                                class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                    </path>
                                </svg>
                                Print Statement
                            </a>
                            <button type="submit"
                                class="inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">Record
                                Payment</button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>