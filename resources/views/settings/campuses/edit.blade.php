<x-app-layout>
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl md:text-3xl text-slate-800 font-bold mb-6">Edit Campus</h1>
        <div class="bg-white shadow-lg rounded-2xl p-6 border border-slate-200">
            <form action="{{ route('settings.campuses.update', $campus) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Campus Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $campus->name) }}" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm" required>
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700">Address (Optional)</label>
                        <input type="text" name="address" id="address" value="{{ old('address', $campus->address) }}" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm">
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>
                </div>
                <div class="mt-6 pt-6 border-t border-slate-200">
                    <button type="submit" class="inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">
                        Update Campus
                    </button>
                    <a href="{{ route('settings.index') }}" class="ml-3 text-sm font-medium text-slate-600">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
