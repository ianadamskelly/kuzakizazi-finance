<x-app-layout>
    <div class="max-w-4xl mx-auto">
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md" role="alert"><p>{{ session('success') }}</p></div>
        @endif
        <div class="bg-white shadow-lg rounded-2xl border border-slate-200">
            <!-- Header -->
            <div class="px-6 py-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-800">Payslip for {{ $payslip->employee->first_name }} {{ $payslip->employee->last_name }}</h2>
                        <p class="text-sm text-slate-500">Period: {{ date('F Y', mktime(0, 0, 0, $payslip->month, 1, $payslip->year)) }}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('payroll.pdf', $payslip) }}" class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">Download PDF</a>
                        <span @class(['px-3 py-1 text-sm font-semibold rounded-full', 'bg-green-100 text-green-800' => $payslip->status === 'paid', 'bg-red-100 text-red-800' => $payslip->status === 'unpaid'])>{{ ucfirst($payslip->status) }}</span>
                    </div>
                </div>
            </div>
            <!-- Body -->
            <div class="p-6 border-t border-slate-200 grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Earnings -->
                <div>
                    <h3 class="text-lg font-semibold text-slate-800 mb-2">Earnings</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm"><span class="text-slate-600">Base Salary</span><span>${{ number_format($payslip->base_salary, 2) }}</span></div>
                        @foreach($payslip->items->where('type', 'earning') as $item)
                            <div class="flex justify-between text-sm"><span class="text-slate-600">{{ $item->description }}</span><span>${{ number_format($item->amount, 2) }}</span></div>
                        @endforeach
                    </div>
                    <div class="flex justify-between text-sm font-bold border-t mt-2 pt-2"><span class="text-slate-800">Total Earnings</span><span>${{ number_format($payslip->total_earnings, 2) }}</span></div>
                </div>
                <!-- Deductions -->
                <div>
                    <h3 class="text-lg font-semibold text-slate-800 mb-2">Deductions</h3>
                    <div class="space-y-2">
                         @forelse($payslip->items->where('type', 'deduction') as $item)
                            <div class="flex justify-between text-sm"><span class="text-slate-600">{{ $item->description }}</span><span>-${{ number_format($item->amount, 2) }}</span></div>
                        @empty
                            <p class="text-sm text-slate-400">No deductions.</p>
                        @endforelse
                    </div>
                    <div class="flex justify-between text-sm font-bold border-t mt-2 pt-2"><span class="text-slate-800">Total Deductions</span><span>-${{ number_format($payslip->total_deductions, 2) }}</span></div>
                </div>
            </div>
            <!-- Net Pay Footer -->
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-between items-center">
                <span class="text-lg font-bold text-slate-800">Net Pay</span>
                <span class="text-lg font-bold text-green-600">${{ number_format($payslip->net_pay, 2) }}</span>
            </div>
            <!-- Actions -->
            @if($payslip->status == 'unpaid')
            <div class="p-6 border-t border-slate-200">
                <div class="flex justify-between items-start">
                    <!-- Add Item Form -->
                    <form action="{{ route('payroll.items.store', $payslip) }}" method="POST" class="flex-grow">
                        @csrf
                        <h4 class="text-slate-600 font-semibold mb-2">Add Earning / Deduction</h4>
                        <div class="flex items-start space-x-2">
                            <input type="text" name="description" placeholder="Description" class="block w-full rounded-md border-slate-300 text-sm" required>
                            <input type="number" name="amount" step="0.01" placeholder="Amount" class="block w-32 rounded-md border-slate-300 text-sm" required>
                            <select name="type" class="block w-40 rounded-md border-slate-300 text-sm" required>
                                <option value="earning">Earning</option>
                                <option value="deduction">Deduction</option>
                            </select>
                            <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-slate-600 hover:bg-slate-700">Add</button>
                        </div>
                    </form>
                    <!-- Mark as Paid Button -->
                    <form action="{{ route('payroll.pay', $payslip) }}" method="POST" class="ml-4">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="inline-flex items-center justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700">Mark as Paid</button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>