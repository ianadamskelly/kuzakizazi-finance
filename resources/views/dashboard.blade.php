<x-app-layout>
    <div class="p-6 space-y-8 bg-slate-50 min-h-screen">

        <!-- Row 1: Monthly Collection Breakdown (Revenue) -->
        <div>
            <h2 class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-4">This Month's Collections</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @php
                    $cats = [
                        'fees' => ['label' => 'Tuition', 'color' => 'blue'],
                        'food' => ['label' => 'Canteen', 'color' => 'orange'],
                        'transport' => ['label' => 'Transport', 'color' => 'purple'],
                        'others' => ['label' => 'Admin/Misc', 'color' => 'slate']
                    ];
                @endphp

                @foreach($cats as $key => $info)
                    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
                        <p class="text-xs font-medium text-{{ $info['color'] }}-600">{{ $info['label'] }} Collected</p>
                        <p class="text-2xl font-bold text-slate-800">${{ number_format($monthlyCollections[$key] ?? 0, 2) }}
                        </p>
                        <div class="mt-2 w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-{{ $info['color'] }}-500 h-full" style="width: 70%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Row: Expense Summary table (Replaces Income Trend Graph) -->
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <h3 class="font-bold text-slate-800 mb-6">Financial Summary ({{ date('F Y') }})</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 text-slate-600 text-xs uppercase font-bold">
                        <tr>
                            <th class="px-6 py-4">Metric</th>
                            <th class="px-6 py-4 text-right">Amount</th>
                            <th class="px-6 py-4">Description</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <tr>
                            <td class="px-6 py-4 font-medium text-slate-700">General Expenses</td>
                            <td class="px-6 py-4 text-right font-bold text-red-600">
                                ${{ number_format($expenseSummary->general, 2) }}</td>
                            <td class="px-6 py-4 text-slate-500 italic">Operating costs for the current month</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 font-medium text-slate-700">Salaries (Payroll)</td>
                            <td class="px-6 py-4 text-right font-bold text-red-600">
                                ${{ number_format($expenseSummary->salaries, 2) }}</td>
                            <td class="px-6 py-4 text-slate-500 italic">Total net pay for employees this month</td>
                        </tr>
                        <tr class="bg-slate-50/50">
                            <td class="px-6 py-4 font-bold text-slate-800 uppercase tracking-wider">Total Expenditure
                            </td>
                            <td class="px-6 py-4 text-right font-black text-red-700">
                                ${{ number_format($expenseSummary->total_expenditure, 2) }}</td>
                            <td class="px-6 py-4 text-slate-500 italic font-bold">Sum of all monthly costs</td>
                        </tr>
                        <tr class="bg-blue-50/30">
                            <td class="px-6 py-4 font-medium text-slate-700">Total Tuition Collected</td>
                            <td class="px-6 py-4 text-right font-bold text-green-600">
                                ${{ number_format($expenseSummary->tuition_collected, 2) }}</td>
                            <td class="px-6 py-4 text-slate-500 italic">Monthly income from fee payments</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-900 text-white">
                            <td class="px-6 py-4 rounded-bl-xl font-bold uppercase">Net Cash Balance</td>
                            <td @class([
                                'px-6 py-4 text-right font-black text-xl',
                                'text-green-400' => $expenseSummary->net_balance >= 0,
                                'text-red-400' => $expenseSummary->net_balance < 0,
                            ])>
                                ${{ number_format($expenseSummary->net_balance, 2) }}</td>
                            <td class="px-6 py-4 rounded-br-xl text-slate-400 text-xs italic">
                                Remaining funds after deducting all expenses from tuition
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Row 2: Outstanding Debt & Status -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Total Debt Breakdown Chart Placeholder -->
            <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-slate-800">Total Outstanding Debt per Category</h3>
                    <span class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded">Action Required</span>
                </div>

                <div class="space-y-4">
                    @foreach(['tuition' => $outstanding->tuition, 'food' => $outstanding->food, 'transport' => $outstanding->transport, 'others' => $outstanding->others] as $label => $amount)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="capitalize text-slate-600 font-medium">{{ $label }}</span>
                                <span class="font-bold text-slate-900">${{ number_format($amount, 2) }}</span>
                            </div>
                            <div class="w-full bg-slate-100 h-2 rounded-full">
                                <div class="bg-slate-800 h-2 rounded-full" style="width: {{ $amount > 0 ? '45%' : '0%' }}">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Quick Summary Stats -->
            <div class="bg-white rounded-xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="font-bold text-slate-800 mb-4">Overall Status</h3>
                    <p class="text-slate-500 text-sm">Grand Total Outstanding</p>
                    <h2 class="text-4xl font-black mt-2 text-slate-800">
                        ${{ number_format($outstanding->tuition + $outstanding->food + $outstanding->transport + $outstanding->others, 2) }}
                    </h2>
                </div>
                <div class="mt-8 pt-6 border-t border-slate-100">
                    <a href="{{ route('reports.fee-defaulters') }}"
                        class="flex items-center justify-between text-sm font-bold text-blue-600 hover:text-blue-800 transition-colors p-2 rounded-lg bg-blue-50 hover:bg-blue-100">
                        View Arrears Report
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>


    </div>
</x-app-layout>