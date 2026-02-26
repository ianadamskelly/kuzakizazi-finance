<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    <!-- Left Column: Add New Category -->
    <div class="md:col-span-1">
        <div class="bg-white shadow-lg rounded-2xl p-6 border border-slate-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Add Employee Category</h3>
            <form action="{{ route('settings.employee-categories.store') }}" method="POST">
                @csrf
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Category Name</label>
                    <input type="text" name="name" placeholder="e.g., Senior Teacher" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm" required>
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div class="mt-4">
                    <label for="base_salary" class="block text-sm font-medium text-gray-700">Base Monthly Salary</label>
                    <input type="number" name="base_salary" step="0.01" placeholder="e.g., 2500" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm" required>
                    <x-input-error :messages="$errors->get('base_salary')" class="mt-2" />
                </div>
                <div class="mt-6 pt-6 border-t border-slate-200">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">Create Category</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Right Column: Existing Categories -->
    <div class="md:col-span-2">
         <h3 class="text-lg font-medium text-gray-900 mb-4">Manage Employee Salaries</h3>
        <div class="bg-white shadow-lg rounded-2xl border border-slate-200">
            <div class="overflow-x-auto">
                <table class="table-auto w-full">
                    <thead class="text-xs font-semibold uppercase text-slate-500 bg-slate-50">
                        <tr>
                            <th class="p-4"><div class="font-semibold text-left">Category Name</div></th>
                            <th class="p-4"><div class="font-semibold text-right">Base Salary</div></th>
                            <th class="p-4"></th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-200">
                        @forelse ($categories as $category)
                            <tr>
                                <td class="p-4"><div class="font-medium text-slate-800">{{ $category->name }}</div></td>
                                <td class="p-4"><div class="text-right font-semibold text-slate-700">${{ number_format($category->base_salary, 2) }}</div></td>
                                <td class="p-4 text-right">
                                    <form action="{{ route('settings.employee-categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="p-4 text-center text-slate-500">No employee categories created yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
