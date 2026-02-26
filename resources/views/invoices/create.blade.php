<x-app-layout>
    <div class="max-w-4xl mx-auto py-10 px-6">
        <div class="bg-white shadow-2xl rounded-2xl border border-slate-200 overflow-hidden">
            <div class="bg-slate-50 border-b border-slate-100 p-8">
                <h1 class="text-2xl font-black text-slate-800">Generate Invoices</h1>
                <p class="text-slate-500 text-sm">This will generate invoices for ALL active students based on their fee
                    structure.</p>
            </div>

            @if ($errors->any())
                <div class="p-8 pb-0">
                    <div class="bg-red-50 border-l-4 border-red-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('invoices.store') }}" method="POST" x-data="{ 
                type: 'all',
                search: '',
                selectedIds: [],
                students: {{ $students->map(fn($s) => ['id' => $s->id, 'name' => $s->first_name . ' ' . $s->last_name, 'adm' => $s->admission_number])->toJson() }},
                get filteredStudents() {
                    if (!this.search) return this.students;
                    return this.students.filter(s => 
                        s.name.toLowerCase().includes(this.search.toLowerCase()) || 
                        s.adm.toLowerCase().includes(this.search.toLowerCase())
                    );
                }
            }" class="p-8 space-y-6">
                @csrf

                <!-- Hidden inputs for selected students to ensure they are submitted even if filtered out of view -->
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="student_ids[]" :value="id">
                </template>

                <div class="space-y-4">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Invoicing Type</label>
                    <div class="flex gap-4">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="type" value="all" x-model="type" class="hidden peer">
                            <div
                                class="p-4 border-2 rounded-xl text-center font-bold transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50 text-slate-600 peer-checked:text-blue-600 border-slate-100 bg-slate-50">
                                All Students
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="type" value="specific" x-model="type" class="hidden peer">
                            <div
                                class="p-4 border-2 rounded-xl text-center font-bold transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50 text-slate-600 peer-checked:text-blue-600 border-slate-100 bg-slate-50">
                                Specific Students
                            </div>
                        </label>
                    </div>
                </div>

                <div x-show="type === 'specific'" x-cloak
                    class="space-y-4 animate-in fade-in slide-in-from-top-4 duration-300">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Select Students (<span
                                x-text="selectedIds.length"></span> selected)</label>
                        <button type="button" x-show="selectedIds.length > 0" @click="selectedIds = []"
                            class="text-xs text-red-600 font-bold hover:underline">Clear Selection</button>
                    </div>
                    <div class="relative">
                        <input type="text" x-model="search" placeholder="Search students by name or admission number..."
                            class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-4 py-3 focus:border-blue-500 outline-none pl-10">
                        <svg class="w-5 h-5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>

                    <div
                        class="max-h-60 overflow-y-auto border-2 border-slate-100 rounded-xl divide-y divide-slate-100">
                        <template x-for="student in filteredStudents" :key="student.id">
                            <label class="flex items-center p-3 hover:bg-slate-50 cursor-pointer transition-colors"
                                :class="selectedIds.includes(student.id) ? 'bg-blue-50/50' : ''">
                                <input type="checkbox" :value="student.id" x-model="selectedIds"
                                    class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                <div class="ml-3">
                                    <p class="text-sm font-bold text-slate-800" x-text="student.name"></p>
                                    <p class="text-xs text-slate-500" x-text="'ADM: ' + student.adm"></p>
                                </div>
                            </label>
                        </template>
                        <div x-show="filteredStudents.length === 0" class="p-8 text-center text-slate-500 text-sm">
                            No students found matching your search.
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Due Date</label>
                    <input type="date" name="due_date"
                        value="{{ $filters['due_date'] ?? date('Y-m-d', strtotime('+14 days')) }}"
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-4 py-3 focus:border-blue-500 outline-none">
                </div>

                <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-100">
                    <a href="{{ route('invoices.index') }}"
                        class="text-slate-500 font-bold hover:text-slate-800 px-4">Cancel</a>
                    <button type="submit"
                        class="bg-slate-900 text-white px-8 py-3 rounded-xl font-bold hover:bg-slate-800 shadow-lg shadow-slate-200 transition-all">
                        <span
                            x-text="type === 'all' ? 'Generate All Invoices' : 'Generate Selected Invoices (' + selectedIds.length + ')'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>