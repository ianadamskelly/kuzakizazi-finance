<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label for="expense_date" class="block text-sm font-medium text-slate-700">Expense Date</label>
        <input type="date" name="expense_date" id="expense_date"
            value="{{ old('expense_date', isset($expense) ? \Carbon\Carbon::parse($expense->expense_date)->format('Y-m-d') : date('Y-m-d')) }}"
            class="mt-1 block w-full rounded-md border-slate-300 shadow-sm" required>
        <x-input-error :messages="$errors->get('expense_date')" class="mt-2" />
    </div>
    <div>
        <label for="category" class="block text-sm font-medium text-slate-700">Category</label>
        <input type="text" name="category" id="category" value="{{ old('category', $expense->category ?? '') }}"
            placeholder="e.g., Utilities, Supplies" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm"
            required>
        <x-input-error :messages="$errors->get('category')" class="mt-2" />
    </div>
    <div>
        <label for="amount" class="block text-sm font-medium text-slate-700">Amount</label>
        <input type="number" name="amount" id="amount" step="0.01" value="{{ old('amount', $expense->amount ?? '') }}"
            class="mt-1 block w-full rounded-md border-slate-300 shadow-sm" required>
        <x-input-error :messages="$errors->get('amount')" class="mt-2" />
    </div>
    <div>
        <label for="vendor" class="block text-sm font-medium text-slate-700">Vendor (Optional)</label>
        <input type="text" name="vendor" id="vendor" value="{{ old('vendor', $expense->vendor ?? '') }}"
            class="mt-1 block w-full rounded-md border-slate-300 shadow-sm">
        <x-input-error :messages="$errors->get('vendor')" class="mt-2" />
    </div>
    <div class="md:col-span-2 shadow-sm rounded-md p-4 bg-slate-50 border border-slate-200">
        <label for="fund_source" class="block text-sm font-bold text-slate-700 mb-2">Source Fund (Where is this paid
            from?)</label>
        <select id="fund_source" name="fund_source"
            class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            required>
            <option value="fees" {{ (old('fund_source', $expense->fund_source ?? '') == 'fees') ? 'selected' : '' }}>
                Tuition Fees (Main Account)</option>
            <option value="food" {{ (old('fund_source', $expense->fund_source ?? '') == 'food') ? 'selected' : '' }}>
                Canteen / Food</option>
            <option value="transport" {{ (old('fund_source', $expense->fund_source ?? '') == 'transport') ? 'selected' : '' }}>Transport</option>
            <option value="others" {{ (old('fund_source', $expense->fund_source ?? '') == 'others') ? 'selected' : '' }}>
                Other Collections</option>
        </select>
        <p class="mt-1 text-xs text-slate-500">Specify which collection fund is being used to pay for this expense.
            Salaries and Bills usually come from Tuition Fees.</p>
        <x-input-error :messages="$errors->get('fund_source')" class="mt-2" />
    </div>
    <div class="md:col-span-2">
        <label for="campus_id" class="block text-sm font-medium text-slate-700">Campus</label>
        <select id="campus_id" name="campus_id" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm" required
            {{ count($campuses) == 1 ? 'disabled' : '' }}>
            @foreach($campuses as $campus)
                <option value="{{ $campus->id }}" {{ (old('campus_id', $expense->campus_id ?? Auth::user()->employee?->campus_id) == $campus->id) ? 'selected' : '' }}>
                    {{ $campus->name }}
                </option>
            @endforeach
        </select>
        @if(count($campuses) == 1)
            <input type="hidden" name="campus_id" value="{{ $campuses->first()->id }}">
        @endif
        <x-input-error :messages="$errors->get('campus_id')" class="mt-2" />
    </div>
    <div class="md:col-span-2">
        <label for="receipt" class="block text-sm font-medium text-slate-700">Receipt (Optional)</label>
        <input type="file" name="receipt" id="receipt"
            class="mt-1 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
        <x-input-error :messages="$errors->get('receipt')" class="mt-2" />
    </div>
</div>