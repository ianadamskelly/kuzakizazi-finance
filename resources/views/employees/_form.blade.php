@php
    $employee = $employee ?? null;
@endphp
<div x-data="{ createUser: {{ old('create_user_account', $employee?->user_id ? 'true' : 'false') }} }">
    <!-- ... Employee Details section remains the same ... -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="first_name" class="block text-sm font-medium text-slate-700">First Name</label>
            <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $employee?->first_name) }}" class="mt-1 block w-full rounded-md border-slate-300" required>
            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
        </div>
        <div>
            <label for="last_name" class="block text-sm font-medium text-slate-700">Last Name</label>
            <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $employee?->last_name) }}" class="mt-1 block w-full rounded-md border-slate-300" required>
            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
        </div>
        <div>
            <label for="job_title" class="block text-sm font-medium text-slate-700">Job Title</label>
            <input type="text" name="job_title" id="job_title" value="{{ old('job_title', $employee?->job_title) }}" class="mt-1 block w-full rounded-md border-slate-300" required>
            <x-input-error :messages="$errors->get('job_title')" class="mt-2" />
        </div>
        <div>
            <label for="employee_category_id" class="block text-sm font-medium text-slate-700">Salary Category</label>
            <select id="employee_category_id" name="employee_category_id" class="mt-1 block w-full rounded-md border-slate-300" required>
                <option value="">Select a category...</option>
                @foreach($employeeCategories as $category)
                    <option value="{{ $category->id }}" {{ (old('employee_category_id', $employee?->employee_category_id) == $category->id) ? 'selected' : '' }}>
                        {{ $category->name }} (${{ number_format($category->base_salary, 2) }})
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('employee_category_id')" class="mt-2" />
        </div>
        <div class="md:col-span-2">
            <label for="campus_id" class="block text-sm font-medium text-slate-700">Campus Assignment</label>
            <select id="campus_id" name="campus_id" class="mt-1 block w-full rounded-md border-slate-300">
                <option value="">All Campuses</option>
                @foreach($campuses as $campus)
                    <option value="{{ $campus->id }}" {{ (old('campus_id', $employee?->campus_id) == $campus->id) ? 'selected' : '' }}>
                        {{ $campus->name }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('campus_id')" class="mt-2" />
        </div>
    </div>

    <!-- Bank Details Section -->
    <div class="mt-6 pt-6 border-t border-slate-200">
        <h3 class="text-lg font-medium text-gray-900">Bank Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
            <div>
                <label for="bank_name" class="block text-sm font-medium text-slate-700">Bank Name</label>
                <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name', $employee?->bank_name) }}" class="mt-1 block w-full rounded-md border-slate-300">
            </div>
            <div>
                <label for="bank_account_number" class="block text-sm font-medium text-slate-700">Account Number</label>
                <input type="text" name="bank_account_number" id="bank_account_number" value="{{ old('bank_account_number', $employee?->bank_account_number) }}" class="mt-1 block w-full rounded-md border-slate-300">
            </div>
            <div>
                <label for="bank_branch_code" class="block text-sm font-medium text-slate-700">Branch Code</label>
                <input type="text" name="bank_branch_code" id="bank_branch_code" value="{{ old('bank_branch_code', $employee?->bank_branch_code) }}" class="mt-1 block w-full rounded-md border-slate-300">
            </div>
        </div>
    </div>

    <!-- System User Account Section -->
    <div class="mt-6 pt-6 border-t border-slate-200">
        @if(!$employee?->user_id)
        <div class="flex items-center">
            <input id="create_user_account" name="create_user_account" type="checkbox" value="1" x-model="createUser" class="h-4 w-4 text-blue-600 border-gray-300 rounded">
            <label for="create_user_account" class="ml-2 block text-sm text-gray-900">Create System User Account?</label>
        </div>
        @endif

        <div x-show="createUser" style="{{ $employee?->user_id || old('create_user_account') ? '' : 'display: none;' }}" class="space-y-6 mt-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $employee?->user?->email) }}" class="mt-1 block w-full rounded-md border-slate-300">
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
                <div>
                    <label for="role" class="block text-sm font-medium text-slate-700">System Role</label>
                    <select id="role" name="role" class="mt-1 block w-full rounded-md border-slate-300">
                        <option value="">Select a role...</option>
                        @foreach($roles as $role)
                            <option value="{{ $role }}" {{ (old('role', $employee?->user?->roles->first()?->name ?? '') == $role) ? 'selected' : '' }}>
                                {{ $role }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('role')" class="mt-2" />
                </div>
            </div>

            <!-- Direct Permissions -->
            <div>
                <label class="block text-sm font-medium text-slate-700">Direct Permissions</label>
                <p class="text-xs text-slate-500">Assign extra permissions beyond what the role provides.</p>
                <div class="mt-2 space-y-4">
                    @foreach($permissions as $group => $permissionGroup)
                        <div>
                            <h4 class="font-semibold text-sm text-slate-600 capitalize">{{ $group }}</h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-1">
                                @foreach($permissionGroup as $permission)
                                    <div class="flex items-center">
                                        <input id="permission-{{ $permission->id }}" name="permissions[]" type="checkbox" value="{{ $permission->name }}"
                                               class="h-4 w-4 text-blue-600 border-gray-300 rounded"
                                               @if($employee?->user?->hasDirectPermission($permission->name)) checked @endif>
                                        <label for="permission-{{ $permission->id }}" class="ml-2 block text-sm text-gray-900">{{ $permission->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>