<x-app-layout>
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl md:text-3xl text-slate-800 font-bold mb-6">Add New Employee</h1>
        <div class="bg-white shadow-lg rounded-2xl p-6 border border-slate-200">
            <form action="{{ route('employees.store') }}" method="POST">
                @csrf
                @include('employees._form')
                <div class="mt-6 pt-6 border-t border-slate-200">
                    <button type="submit" class="inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">
                        Create Employee
                    </button>
                    <a href="{{ route('employees.index') }}" class="ml-3 text-sm font-medium text-slate-600">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>