<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    <!-- Left Column: Add New Campus -->
    <div class="md:col-span-1">
        <div class="bg-white shadow-lg rounded-2xl p-6 border border-slate-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Add New Campus</h3>
            <form action="{{ route('settings.campuses.store') }}" method="POST">
                @csrf
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Campus Name</label>
                    <input type="text" name="name" placeholder="e.g., North Campus" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm" required>
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div class="mt-4">
                    <label for="address" class="block text-sm font-medium text-gray-700">Address (Optional)</label>
                    <input type="text" name="address" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm">
                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                </div>
                <div class="mt-6 pt-6 border-t border-slate-200">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">Create Campus</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Right Column: Existing Campuses -->
    <div class="md:col-span-2">
         <h3 class="text-lg font-medium text-gray-900 mb-4">Manage Campuses</h3>
        <div class="bg-white shadow-lg rounded-2xl border border-slate-200">
            <div class="overflow-x-auto">
                <table class="table-auto w-full">
                    <thead class="text-xs font-semibold uppercase text-slate-500 bg-slate-50">
                        <tr>
                            <th class="p-4"><div class="font-semibold text-left">Campus Name</div></th>
                            <th class="p-4"><div class="font-semibold text-left">Address</div></th>
                            <th class="p-4"></th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-200">
                        @forelse ($campuses as $campus)
                            <tr>
                                <td class="p-4"><div class="font-medium text-slate-800">{{ $campus->name }}</div></td>
                                <td class="p-4"><div>{{ $campus->address }}</div></td>
                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end space-x-4">
                                        <a href="{{ route('settings.campuses.edit', $campus) }}" class="font-medium text-blue-600 hover:text-blue-800">Edit</a>
                                        <form action="{{ route('settings.campuses.destroy', $campus) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="font-medium text-red-500 hover:text-red-700">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="p-4 text-center text-slate-500">No campuses created yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
