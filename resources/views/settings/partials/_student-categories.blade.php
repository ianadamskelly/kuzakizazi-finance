<div class="space-y-8">
    <!-- Existing "Add Category" Section (Preserved for functionality) -->
    <div class="bg-white shadow-lg rounded-2xl p-6 border border-slate-200">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Add New Student Category (Grade)</h3>
        <form action="{{ route('settings.student-categories.store') }}" method="POST" class="flex gap-4 items-end">
            @csrf
            <div class="flex-grow">
                <label for="name"
                    class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Name</label>
                <input type="text" name="name" placeholder="e.g., Grade 1"
                    class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    required>
            </div>
            <div class="flex-grow">
                <label for="description"
                    class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Description
                    (Optional)</label>
                <input type="text" name="description" placeholder="Short description"
                    class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            </div>
            <button type="submit"
                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Add
            </button>
        </form>
    </div>

    <!-- New "Class Fee Configuration" Table (User Request) -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-bold text-slate-800">Class Fee Configuration</h3>
                <p class="text-sm text-slate-500">Define charges for each category to automate invoice generation.</p>
            </div>
            <!-- Optional Delete Handling could go here, but omitted for simplicity/safety -->
        </div>

        <form action="{{ route('settings.fee-structure.update') }}" method="POST">
            @csrf
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 text-slate-600 text-xs uppercase font-bold">
                        <tr>
                            <th class="px-6 py-4">Grade / Class</th>
                            <th class="px-6 py-4 text-blue-600">Tuition (Fees)</th>
                            <th class="px-6 py-4 text-orange-600">Food/Canteen</th>
                            <th class="px-6 py-4 text-purple-600">Transport</th>
                            <th class="px-6 py-4 text-slate-600">Others</th>
                            <th class="px-6 py-4">Total</th>
                            <th class="px-6 py-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($grades as $grade)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-semibold text-slate-700">
                                    {{ $grade->name }}
                                    <div class="text-xs text-slate-400 font-normal">{{ $grade->description }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <input type="number" name="fees[{{ $grade->id }}][tuition]"
                                        value="{{ $grade->tuition_amount }}"
                                        class="w-24 p-2 border border-slate-300 rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        step="0.01">
                                </td>
                                <td class="px-6 py-4">
                                    <input type="number" name="fees[{{ $grade->id }}][food]"
                                        value="{{ $grade->food_amount }}"
                                        class="w-24 p-2 border border-slate-300 rounded text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                        step="0.01">
                                </td>
                                <td class="px-6 py-4">
                                    <input type="number" name="fees[{{ $grade->id }}][transport]"
                                        value="{{ $grade->transport_amount }}"
                                        class="w-24 p-2 border border-slate-300 rounded text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                        step="0.01">
                                </td>
                                <td class="px-6 py-4">
                                    <input type="number" name="fees[{{ $grade->id }}][others]"
                                        value="{{ $grade->others_amount }}"
                                        class="w-24 p-2 border border-slate-300 rounded text-sm focus:ring-2 focus:ring-slate-500 focus:border-slate-500"
                                        step="0.01">
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-900">
                                    ${{ number_format($grade->tuition_amount + $grade->food_amount + $grade->transport_amount + $grade->others_amount, 2) }}
                                </td>
                                <td class="px-6 py-4">
                                    <!-- Delete Category Button (Small) -->
                                    <button type="button"
                                        onclick="if(confirm('Delete {{ $grade->name }} and all its settings?')) document.getElementById('delete-cat-{{ $grade->id }}').submit()"
                                        class="text-slate-400 hover:text-red-600 transition-colors" title="Delete Category">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-6 bg-slate-50/50 border-t border-slate-100 text-right">
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-300/50">
                    Save Fee Structures
                </button>
            </div>
        </form>
    </div>

    <!-- Hidden Delete Forms -->
    @foreach($grades as $grade)
        <form id="delete-cat-{{ $grade->id }}" action="{{ route('settings.student-categories.destroy', $grade) }}"
            method="POST" class="hidden">
            @csrf @method('DELETE')
        </form>
    @endforeach
</div>