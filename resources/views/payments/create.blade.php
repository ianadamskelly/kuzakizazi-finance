<x-app-layout>
    @if(!$student)
        <div class="max-w-xl mx-auto py-12 px-4">
            <div class="bg-white rounded-xl shadow-lg border border-slate-200 p-8 text-center">
                <h2 class="text-2xl font-bold text-slate-800 mb-4">Select a Student</h2>
                <p class="text-slate-500 mb-6">Please select a student to receive payment for.</p>
                <form action="{{ route('payments.create') }}" method="GET">
                    <div class="mb-4">
                        <select name="student_id"
                            class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            onchange="this.form.submit()">
                            <option value="">-- Choose Student --</option>
                            @foreach($students as $s)
                                <option value="{{ $s->id }}">{{ $s->first_name }} {{ $s->last_name }}
                                    ({{ $s->admission_number }})</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>
    @else
        <div class="max-w-5xl mx-auto py-8 px-4">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('students.show', $student) }}"
                    class="inline-flex items-center text-sm font-medium text-slate-600 hover:text-slate-900">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Student Profile
                </a>
            </div>

            <div class="bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden">
                <!-- Header -->
                <div class="bg-slate-900 px-8 py-6 text-white flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold">Receive Payment</h2>
                        <p class="text-slate-400">Apply credits to student sub-accounts</p>
                    </div>
                    <div class="text-right hidden md:block">
                        <div class="text-xs text-slate-400 uppercase tracking-wider">Total Due</div>
                        <div class="text-2xl font-bold">${{ number_format($student->balance, 2) }}</div>
                    </div>
                </div>

                <form action="{{ route('payments.store') }}" method="POST" class="p-8">
                    @csrf
                    <input type="hidden" name="student_id" value="{{ $student->id }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                        <!-- Left Column: Student Info & Balances -->
                        <div class="space-y-6">
                            <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                                <label class="text-xs font-bold text-slate-500 uppercase">Student Name</label>
                                <p class="text-lg font-semibold text-slate-800">{{ $student->first_name }}
                                    {{ $student->last_name }}</p>
                                <p class="text-sm text-slate-500">ID: {{ $student->admission_number }} | Grade:
                                    {{ $student->grade }}</p>
                            </div>

                            <div class="space-y-3">
                                <label class="text-sm font-bold text-slate-700">Current Balances</label>

                                <div
                                    class="flex justify-between p-3 bg-blue-50 text-blue-800 rounded-md border border-blue-100">
                                    <span>Tuition & Fees</span>
                                    <span class="font-bold">${{ number_format($student->fees_balance, 2) }}</span>
                                </div>

                                <div
                                    class="flex justify-between p-3 bg-orange-50 text-orange-800 rounded-md border border-orange-100">
                                    <span>Food & Canteen</span>
                                    <span class="font-bold">${{ number_format($student->food_balance, 2) }}</span>
                                </div>

                                <div
                                    class="flex justify-between p-3 bg-purple-50 text-purple-800 rounded-md border border-purple-100">
                                    <span>Transport</span>
                                    <span class="font-bold">${{ number_format($student->transport_balance, 2) }}</span>
                                </div>

                                <div
                                    class="flex justify-between p-3 bg-slate-50 text-slate-700 rounded-md border border-slate-200">
                                    <span>Others</span>
                                    <span class="font-bold">${{ number_format($student->others_balance ?? 0, 2) }}</span>
                                </div>
                            </div>

                            <!-- Change Student optional link -->
                            <div class="text-center pt-2">
                                <a href="{{ route('payments.create') }}"
                                    class="text-sm text-blue-600 hover:underline">Change Student</a>
                            </div>
                        </div>

                        <!-- Right Column: Form Inputs -->
                        <div class="space-y-5">
                            <!-- Amount Input -->
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Amount to Pay ($)</label>
                                <input type="number" name="amount" step="0.01" required
                                    class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-xl font-bold"
                                    placeholder="0.00">
                            </div>

                            <!-- Category Selection -->
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Apply Payment To</label>
                                <select name="category" required
                                    class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 outline-none">
                                    <option value="fees">Tuition & School Fees</option>
                                    <option value="food">Food & Canteen Fund</option>
                                    <option value="transport">Transport / Bus Fees</option>
                                    <option value="others">Other Administrative Fees</option>
                                </select>
                                <p class="mt-1 text-xs text-slate-500 italic">This payment will only reduce the selected
                                    balance.</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <!-- Method -->
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Method</label>
                                    <select name="payment_method"
                                        class="w-full px-4 py-2 rounded-lg border border-slate-300 outline-none">
                                        <option value="Cash">Cash</option>
                                        <option value="M-Pesa">M-Pesa</option>
                                        <option value="Bank">Bank Transfer</option>
                                        <option value="Check">Check</option>
                                    </select>
                                </div>
                                <!-- Date -->
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Date</label>
                                    <input type="date" name="payment_date" value="{{ date('Y-m-d') }}"
                                        class="w-full px-4 py-2 rounded-lg border border-slate-300 outline-none">
                                </div>
                            </div>

                            <!-- Reference -->
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Reference / Receipt
                                    No.</label>
                                <input type="text" name="reference_no"
                                    class="w-full px-4 py-2 rounded-lg border border-slate-300 outline-none"
                                    placeholder="TXN-123456">
                            </div>

                            <button type="submit"
                                class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg shadow-lg shadow-emerald-200 transition-all flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Confirm & Post Payment
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    @endif
</x-app-layout>