<x-app-layout>
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl md:text-3xl text-slate-800 font-bold mb-6">Generate Payroll Run</h1>
        <div class="bg-white shadow-lg rounded-2xl p-6 border border-slate-200">
            <form action="{{ route('payroll.store') }}" method="POST">
                @csrf
                <p class="text-sm text-gray-600">This will generate a new payslip for all employees with an assigned salary category for the selected month and year. It will not create duplicates.</p>
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="month" class="block text-sm font-medium text-slate-700">Month</label>
                        <select name="month" id="month" class="mt-1 block w-full rounded-md border-slate-300" required>
                            @for ($m=1; $m<=12; $m++)
                                <option value="{{ $m }}" {{ $m == date('m') ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m, 1, date('Y'))) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label for="year" class="block text-sm font-medium text-slate-700">Year</label>
                        <input type="number" name="year" id="year" value="{{ date('Y') }}" class="mt-1 block w-full rounded-md border-slate-300" required>
                    </div>
                </div>
                <div class="mt-6 pt-6 border-t border-slate-200">
                    <button type="submit" class="inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700" onclick="return confirm('Are you sure you want to generate payslips for this period?');">
                        Generate Payslips
                    </button>
                    <a href="{{ route('payroll.index') }}" class="ml-3 text-sm font-medium text-slate-600">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>