<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label for="first_name" class="block text-sm font-medium text-slate-700">First Name</label>
        <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $student->first_name ?? '') }}"
            class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            required>
        <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
    </div>
    <div>
        <label for="last_name" class="block text-sm font-medium text-slate-700">Last Name</label>
        <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $student->last_name ?? '') }}"
            class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            required>
        <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
    </div>
    <div>
        <label for="admission_number" class="block text-sm font-medium text-slate-700">Admission Number</label>
        <input type="text" name="admission_number" id="admission_number"
            value="{{ old('admission_number', $student->admission_number ?? '') }}"
            class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            required>
        <x-input-error :messages="$errors->get('admission_number')" class="mt-2" />
    </div>
    <div>
        <label for="grade_id" class="block text-sm font-medium text-slate-700">Grade</label>
        <select id="grade_id" name="grade_id"
            class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            required>
            <option value="">Select a Grade</option>
            @foreach($grades as $grade)
                <option value="{{ $grade->id }}" {{ (old('grade_id', $student->grade_id ?? '') == $grade->id) ? 'selected' : '' }}>
                    {{ $grade->name }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('grade_id')" class="mt-2" />
    </div>
    <div>
        <label for="campus_id" class="block text-sm font-medium text-slate-700">Campus</label>
        <select id="campus_id" name="campus_id"
            class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            required>
            <option value="">Select a Campus</option>
            @foreach($campuses as $campus)
                <option value="{{ $campus->id }}" {{ (old('campus_id', $student->campus_id ?? '') == $campus->id) ? 'selected' : '' }}>
                    {{ $campus->name }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('campus_id')" class="mt-2" />
    </div>
    <div>
        <label for="student_category_id" class="block text-sm font-medium text-slate-700">Student Fee Category</label>
        <select id="student_category_id" name="student_category_id"
            class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            required>
            <option value="">Select a Category</option>
            @foreach($studentCategories as $category)
                <option value="{{ $category->id }}" {{ (old('student_category_id', $student->student_category_id ?? '') == $category->id) ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('student_category_id')" class="mt-2" />
    </div>
</div>