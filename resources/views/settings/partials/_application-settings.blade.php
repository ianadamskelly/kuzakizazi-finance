<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    <div class="md:col-span-1">
        <h3 class="text-lg font-medium text-gray-900">Branding & Identity</h3>
        <p class="mt-1 text-sm text-gray-600">Update the application's name and logos. Changes will be reflected globally.</p>
    </div>
    <div class="md:col-span-2">
        <div class="bg-white shadow-lg rounded-2xl p-6 border border-slate-200">
            <form action="{{ route('settings.app.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label for="app_name" class="block text-sm font-medium text-gray-700">Application Name</label>
                        <input type="text" name="app_name" id="app_name" value="{{ config('app.name') }}" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm" required>
                    </div>
                    <div>
                        <label for="app_logo" class="block text-sm font-medium text-gray-700">Application Logo (PNG, JPG, SVG)</label>
                        <input type="file" name="app_logo" id="app_logo" class="mt-1 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                    <div>
                        <label for="app_favicon" class="block text-sm font-medium text-gray-700">Favicon (PNG, ICO)</label>
                        <input type="file" name="app_favicon" id="app_favicon" class="mt-1 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                </div>
                <div class="mt-6 pt-6 border-t border-slate-200">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">Save Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>