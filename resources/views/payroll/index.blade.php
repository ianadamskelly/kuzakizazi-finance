<x-app-layout>
    <div>
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Payroll</h1>
            <a href="{{ route('payroll.create') }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">
                <span>Generate Payroll Run</span>
            </a>
        </div>
        
        <!-- Filter and Bulk Actions -->
        <div class="bg-white shadow-lg rounded-2xl border border-slate-200 p-4 mb-8">
            <div class="flex justify-between items-center">
                <form action="{{ route('payroll.index') }}" method="GET" class="flex items-end space-x-2">
                    <div>
                        <label for="month" class="block text-sm font-medium text-slate-700">Month</label>
                        <select name="month" id="month" class="mt-1 block w-full rounded-md border-slate-300">
                            @for ($m=1; $m<=12; $m++)
                                <option value="{{ $m }}" {{ $m == $selectedMonth ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m, 1)) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label for="year" class="block text-sm font-medium text-slate-700">Year</label>
                        <input type="number" name="year" id="year" value="{{ $selectedYear }}" class="mt-1 block w-full rounded-md border-slate-300">
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">Filter</button>
                </form>
                <form action="{{ route('payroll.bulk-pay') }}" method="POST" onsubmit="return confirm('Are you sure you want to mark all unpaid payslips for this period as paid?');">
                    @csrf
                    <input type="hidden" name="month" value="{{ $selectedMonth }}">
                    <input type="hidden" name="year" value="{{ $selectedYear }}">
                    <button type="submit" class="inline-flex items-center justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700">Mark All as Paid</button>
                </form>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert"><p>{{ session('success') }}</p></div>
        @endif
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert"><p>{{ session('success') }}</p></div>
        @endif
        <div class="bg-white shadow-lg rounded-2xl border border-slate-200">
            <div class="overflow-x-auto">
                <table class="table-auto w-full">
                    <thead class="text-xs font-semibold uppercase text-slate-500 bg-slate-50">
                        <tr>
                            <th class="p-4"><div class="font-semibold text-left">Period</div></th>
                            <th class="p-4"><div class="font-semibold text-left">Employee</div></th>
                            <th class="p-4"><div class="font-semibold text-right">Net Pay</div></th>
                            <th class="p-4"><div class="font-semibold text-center">Status</div></th>
                            <th class="p-4"></th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-200">
                        @forelse ($payslips as $payslip)
                            <tr>
                                <td class="p-4"><div class="font-medium text-slate-800">{{ date('F Y', mktime(0, 0, 0, $payslip->month, 1, $payslip->year)) }}</div></td>
                                <td class="p-4"><div>{{ $payslip->employee->first_name }} {{ $payslip->employee->last_name }}</div></td>
                                <td class="p-4"><div class="text-right font-semibold text-slate-800">${{ number_format($payslip->net_pay, 2) }}</div></td>
                                <td class="p-4 text-center">
                                    <span @class(['px-2 py-1 text-xs font-semibold rounded-full',
                                        'bg-green-100 text-green-800' => $payslip->status === 'paid',
                                        'bg-red-100 text-red-800' => $payslip->status === 'unpaid',
                                    ])>{{ ucfirst($payslip->status) }}</span>
                                </td>
                                <td class="p-4"><a href="{{ route('payroll.show', $payslip) }}" class="font-medium text-blue-600 hover:text-blue-800">View Details</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-4 text-center text-slate-500">No payslips generated yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-8">{{ $payslips->links() }}</div>
    </div>
</x-app-layout>