<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label for="donation_date" class="block text-sm font-medium text-slate-700">Donation Date</label>
        <input type="date" name="donation_date" id="donation_date" value="{{ old('donation_date', date('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm" required>
        <x-input-error :messages="$errors->get('donation_date')" class="mt-2" />
    </div>
    <div>
        <label for="donor_name" class="block text-sm font-medium text-slate-700">Donor Name</label>
        <input type="text" name="donor_name" id="donor_name" value="{{ old('donor_name', '') }}" placeholder="e.g., Alumni Association" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm" required>
        <x-input-error :messages="$errors->get('donor_name')" class="mt-2" />
    </div>
    <div>
        <label for="amount" class="block text-sm font-medium text-slate-700">Amount</label>
        <input type="number" name="amount" id="amount" step="0.01" value="{{ old('amount', '') }}" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm" required>
        <x-input-error :messages="$errors->get('amount')" class="mt-2" />
    </div>
    <div>
        <label for="campus_id" class="block text-sm font-medium text-slate-700">For Campus (Optional)</label>
        <select id="campus_id" name="campus_id" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm">
            <option value="">All Campuses</option>
            @foreach($campuses as $campus)
                <option value="{{ $campus->id }}" {{ old('campus_id') == $campus->id ? 'selected' : '' }}>
                    {{ $campus->name }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('campus_id')" class="mt-2" />
    </div>
</div>