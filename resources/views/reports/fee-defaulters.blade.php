<x-app-layout>
    <div>
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Fee Defaulters Report</h1>
                <a href="{{ route('reports.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">&larr; Back to Reports</a>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <form action="{{ route('reports.fee-defaulters') }}" method="GET" class="flex items-center gap-2">
                    <select name="grade_id" onchange="this.form.submit()" class="text-sm border-slate-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Grades</option>
                        @foreach($grades as $grade)
                            <option value="{{ $grade->id }}" {{ $gradeId == $grade->id ? 'selected' : '' }}>
                                {{ $grade->name }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <a href="{{ route('reports.fee-defaulters', ['export' => 'pdf', 'grade_id' => $gradeId]) }}" class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">
                    <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export PDF
                </a>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-2xl border border-slate-200">
            <div class="overflow-x-auto">
                <table class="table-auto w-full">
                    <thead class="text-xs font-semibold uppercase text-slate-500 bg-slate-50">
                        <tr>
                            <th class="p-4"><div class="font-semibold text-left">Student Name</div></th>
                            <th class="p-4"><div class="font-semibold text-left">Admission No.</div></th>
                            <th class="p-4"><div class="font-semibold text-left">Grade</div></th>
                            <th class="p-4"><div class="font-semibold text-left">Campus</div></th>
                            <th class="p-4"><div class="font-semibold text-center">Overdue Invoices</div></th>
                            <th class="p-4"><div class="font-semibold text-right">Total Balance Due</div></th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-200">
                        @forelse ($defaulters as $defaulter)
                            <tr>
                                <td class="p-4"><div class="font-medium text-slate-800">{{ $defaulter['student']->first_name }} {{ $defaulter['student']->last_name }}</div></td>
                                <td class="p-4"><div>{{ $defaulter['student']->admission_number }}</div></td>
                                <td class="p-4"><div>{{ $defaulter['student']->grade->name ?? 'N/A' }}</div></td>
                                <td class="p-4"><div>{{ $defaulter['student']->campus->name }}</div></td>
                                <td class="p-4"><div class="text-center">{{ $defaulter['invoice_count'] }}</div></td>
                                <td class="p-4"><div class="text-right font-bold text-red-600">${{ number_format($defaulter['total_balance'], 2) }}</div></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-4 text-center text-slate-500">No fee defaulters found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>