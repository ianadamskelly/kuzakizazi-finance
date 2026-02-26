<x-app-layout>
    <div class="max-w-6xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

        <!-- Header & Print -->
        @push('scripts')
            <style>
                @media print {

                    /* Hide sidebar, navigation, and anything else */
                    aside,
                    nav,
                    header,
                    footer,
                    .sidebar,
                    .navigation,
                    .no-print {
                        display: none !important;
                    }

                    /* Ensure body takes full width */
                    body,
                    main,
                    div[x-data] {
                        background: white !important;
                        margin: 0 !important;
                        padding: 0 !important;
                        width: 100% !important;
                        height: auto !important;
                        overflow: visible !important;
                        display: block !important;
                    }

                    /* Hiding the specific Layout wrapper elements we saw in app.blade.php */
                    .h-screen {
                        height: auto !important;
                        display: block !important;
                    }

                    aside {
                        display: none !important;
                    }

                    /* Hide the filter form and print button */
                    .print\:hidden {
                        display: none !important;
                    }

                    /* Enhance table readability */
                    table {
                        width: 100% !important;
                        border-collapse: collapse !important;
                    }

                    th,
                    td {
                        border: 1px solid #ddd !important;
                        padding: 8px !important;
                    }

                    /* Add a print header */
                    .print-header {
                        display: block !important;
                        margin-bottom: 20px;
                        text-align: center;
                    }

                    /* Force background colors for summary cards */
                    .bg-slate-50,
                    .bg-red-50,
                    .bg-green-50,
                    .bg-blue-50 {
                        -webkit-print-color-adjust: exact !important;
                        print-color-adjust: exact !important;
                    }
                }

                .print-header {
                    display: none;
                }
            </style>
        @endpush

        <div class="print-header">
            <h1 class="text-2xl font-bold">{{ config('app.name') }} - Statement of Account</h1>
            <p>Student: {{ $student->first_name }} {{ $student->last_name }} ({{ $student->admission_number }})</p>
        </div>

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 print:hidden">
            <div>
                <h1 class="text-3xl font-black text-slate-800">Statement of Account</h1>
                <p class="text-slate-500">
                    Student: <span class="font-bold text-slate-800">{{ $student->first_name }}
                        {{ $student->last_name }}</span>
                    ({{ $student->admission_number }})
                </p>
                <p class="text-slate-500 text-sm">{{ $student->campus->name }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('students.show', $student) }}"
                    class="inline-flex items-center px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 bg-white hover:bg-slate-50">
                    &larr; Back to Profile
                </a>
                <button onclick="window.print()"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 shadow-lg">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                        </path>
                    </svg>
                    Print Statement
                </button>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-8 print:hidden">
            <form action="{{ route('students.statement', $student) }}" method="GET"
                class="flex flex-wrap items-end gap-4">
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
                        class="px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700">Filter</button>
                </div>
                <div class="flex-grow text-right">
                    <span class="text-xs text-slate-400">Quick Filters: </span>
                    <a href="{{ request()->fullUrlWithQuery(['start_date' => now()->startOfMonth()->toDateString(), 'end_date' => now()->toDateString()]) }}"
                        class="text-xs text-blue-600 hover:underline mx-1">This Month</a> |
                    <a href="{{ request()->fullUrlWithQuery(['start_date' => now()->subMonths(3)->startOfMonth()->toDateString(), 'end_date' => now()->toDateString()]) }}"
                        class="text-xs text-blue-600 hover:underline mx-1">Last 3 Months</a> |
                    <a href="{{ request()->fullUrlWithQuery(['start_date' => now()->startOfYear()->toDateString(), 'end_date' => now()->toDateString()]) }}"
                        class="text-xs text-blue-600 hover:underline mx-1">This Year</a>
                </div>
            </form>
        </div>

        <!-- Financial Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                <p class="text-xs font-bold text-slate-500 uppercase">Opening Balance</p>
                <p class="text-xl font-bold text-slate-800">{{ number_format($openingBalance, 2) }}</p>
                <p class="text-xs text-slate-400">As of {{ $startDate }}</p>
            </div>
            <div class="bg-red-50 p-4 rounded-xl border border-red-100">
                <p class="text-xs font-bold text-red-500 uppercase">Total Billed</p>
                <p class="text-xl font-bold text-red-800">+{{ number_format($totalDebits, 2) }}</p>
                <p class="text-xs text-red-400">In selected period</p>
            </div>
            <div class="bg-green-50 p-4 rounded-xl border border-green-100">
                <p class="text-xs font-bold text-green-500 uppercase">Total Paid</p>
                <p class="text-xl font-bold text-green-800">-{{ number_format($totalCredits, 2) }}</p>
                <p class="text-xs text-green-400">In selected period</p>
            </div>
            <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                <p class="text-xs font-bold text-blue-500 uppercase">Closing Balance</p>
                <p class="text-xl font-bold text-blue-800">{{ number_format($closingBalance, 2) }}</p>
                <p class="text-xs text-blue-400">As of {{ $endDate }}</p>
            </div>
        </div>

        <!-- Statement Table -->
        <div class="bg-white shadow-lg rounded-2xl border border-slate-200 overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-bold">
                    <tr>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Description</th>
                        <th class="px-6 py-4">Category</th>
                        <th class="px-6 py-4 text-right">Debit (Billed)</th>
                        <th class="px-6 py-4 text-right">Credit (Paid)</th>
                        <th class="px-6 py-4 text-right">Balance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <!-- Opening Balance Row -->
                    <tr class="bg-slate-50 italic text-slate-500">
                        <td class="px-6 py-3">{{ $startDate }}</td>
                        <td class="px-6 py-3" colspan="4">Opening Balance Brought Forward</td>
                        <td class="px-6 py-3 text-right font-semibold">{{ number_format($openingBalance, 2) }}</td>
                    </tr>

                    @forelse($statementData as $txn)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-slate-600">{{ $txn->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="block text-slate-800 font-medium">{{ $txn->description ?? 'Transaction' }}</span>
                                @if($txn->invoice_id)
                                    <span class="text-xs text-slate-500">Ref:
                                        INV-{{ str_pad($txn->invoice_id, 4, '0', STR_PAD_LEFT) }}</span>
                                @elseif($txn->payment_id)
                                    <span class="text-xs text-slate-500">Ref: Payment #{{ $txn->payment_id }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                    {{ ucfirst($txn->category) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-slate-800">
                                @if($txn->type === 'debit')
                                    {{ number_format($txn->amount, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-green-600 font-medium">
                                @if($txn->type === 'credit')
                                    {{ number_format($txn->amount, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-slate-800">
                                {{ number_format($txn->running_balance, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-500 italic">
                                No transactions found for this period.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-8 text-center text-xs text-slate-400 print:block hidden">
            <p>Generated on {{ now()->format('M d, Y H:i A') }}</p>
        </div>

    </div>
</x-app-layout>