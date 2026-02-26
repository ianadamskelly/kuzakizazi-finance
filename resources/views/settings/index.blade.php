<x-app-layout>
    <div x-data="{ tab: 'students' }">
        <h1 class="text-2xl md:text-3xl text-slate-800 font-bold mb-6">Settings</h1>

        <!-- Tabs -->
        <div class="mb-6 border-b border-slate-200">
            <nav class="flex -mb-px">
                <button @click="tab = 'students'"
                    :class="{'border-blue-500 text-blue-600': tab === 'students', 'border-transparent text-slate-500 hover:text-slate-600 hover:border-slate-300': tab !== 'students'}"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Student Fees
                </button>
                <button @click="tab = 'employees'"
                    :class="{'border-blue-500 text-blue-600': tab === 'employees', 'border-transparent text-slate-500 hover:text-slate-600 hover:border-slate-300': tab !== 'employees'}"
                    class="whitespace-nowrap ml-8 py-4 px-1 border-b-2 font-medium text-sm">
                    Employee Salaries
                </button>
                <button @click="tab = 'campuses'"
                    :class="{'border-blue-500 text-blue-600': tab === 'campuses', 'border-transparent text-slate-500 hover:text-slate-600 hover:border-slate-300': tab !== 'campuses'}"
                    class="whitespace-nowrap ml-8 py-4 px-1 border-b-2 font-medium text-sm">
                    Campuses
                </button>
                <button @click="tab = 'academic'"
                    :class="{'border-blue-500 text-blue-600': tab === 'academic', 'border-transparent text-slate-500 hover:text-slate-600 hover:border-slate-300': tab !== 'academic'}"
                    class="whitespace-nowrap ml-8 py-4 px-1 border-b-2 font-medium text-sm">
                    Academic Year
                </button>
                @role('Super Admin')
                <button @click="tab = 'application'"
                    :class="{'border-blue-500 text-blue-600': tab === 'application', 'border-transparent text-slate-500 hover:text-slate-600 hover:border-slate-300': tab !== 'application'}"
                    class="whitespace-nowrap ml-8 py-4 px-1 border-b-2 font-medium text-sm">
                    Application
                </button>
                @endrole
            </nav>
        </div>

        <!-- Session Messages -->
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <!-- Content -->
        <div>
            <div x-show="tab === 'students'">
                @include('settings.partials._student-categories', ['categories' => $studentCategories])
            </div>
            <div x-show="tab === 'employees'" style="display: none;">
                @include('settings.partials._employee-categories', ['categories' => $employeeCategories])
            </div>
            <div x-show="tab === 'campuses'" style="display: none;">
                @include('settings.partials._campuses', ['campuses' => $campuses])
            </div>
            <div x-show="tab === 'academic'" style="display: none;">
                @include('settings.partials._academic-year', ['academicGrades' => $academicGrades, 'academicYearStart' => $academicYearStart, 'academicYearEnd' => $academicYearEnd])
            </div>
            @role('Super Admin')
            <div x-show="tab === 'application'" style="display: none;">
                @include('settings.partials._application-settings')
            </div>
            @endrole
        </div>
    </div>
</x-app-layout>