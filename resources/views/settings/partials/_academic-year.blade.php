<div class="space-y-6">

    <!-- Academic Year Dates -->
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Academic Year Schedule</h3>
        <form action="{{ route('settings.academic-year.update') }}" method="POST"
            class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Start Date</label>
                <input type="date" name="academic_year_start" value="{{ $academicYearStart }}"
                    class="w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">End Date</label>
                <input type="date" name="academic_year_end" value="{{ $academicYearEnd }}"
                    class="w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    required>
            </div>
            <div>
                <button type="submit"
                    class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-bold shadow-sm transition-colors">
                    Update Dates
                </button>
            </div>
        </form>
    </div>

    <!-- Grade Management & Promotion Path -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Grade List -->
        <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Grade Configuration (Progression Path)</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                Grade Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                Promotes To</th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @foreach($academicGrades as $grade)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                                    {{ $grade->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                    @if($grade->nextGrade)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            &rarr; {{ $grade->nextGrade->name }}
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                            Graduation (Final)
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <form action="{{ route('settings.grades.destroy', $grade) }}" method="POST"
                                        class="inline-block"
                                        onsubmit="return confirm('Note: You cannot delete a grade that has students assigned to it. Proceed?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 pt-6 border-t border-slate-100">
                <h4 class="text-sm font-bold text-slate-700 mb-3">Add New Grade</h4>
                <form action="{{ route('settings.grades.store') }}" method="POST" class="flex gap-4">
                    @csrf
                    <input type="text" name="name" placeholder="Grade Name (e.g. Grade 1)"
                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        required>
                    <select name="next_grade_id"
                        class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">-- Graduation (Final) --</option>
                        @foreach($academicGrades as $g)
                            <option value="{{ $g->id }}">Promote to {{ $g->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit"
                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 font-bold shadow-sm">Add</button>
                </form>
            </div>
        </div>

        <!-- Graduation Action -->
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm h-fit">
            <h3 class="text-lg font-bold text-slate-800 mb-2">End of Year Action</h3>
            <p class="text-sm text-slate-500 mb-6">
                Promote all eligible students to their next grade automatically. Students in the final grade will be
                marked as "Graduated".
            </p>

            <form action="{{ route('settings.academic-year.graduate') }}" method="POST"
                onsubmit="return confirm('WARNING: You are about to graduate/promote ALL students. This action cannot be easily undone. Are you sure you want to proceed?');">
                @csrf
                <button type="submit"
                    class="w-full bg-red-600 text-white text-center py-3 rounded-lg hover:bg-red-700 font-bold shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                    Graduate All Students
                </button>
            </form>
        </div>
    </div>
</div>