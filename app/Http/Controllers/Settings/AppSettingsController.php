<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

class AppSettingsController extends Controller
{
    public function __construct()
    {
        // Only Super Admins should be able to change these settings
        $this->middleware('role:Super Admin');
    }

    /**
     * Update the application settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'app_logo' => 'nullable|image|mimes:png,jpg,svg|max:1024',
            'app_favicon' => 'nullable|image|mimes:png,ico|max:256',
        ]);

        // Update App Name
        DB::table('settings')->where('key', 'app_name')->update(['value' => $validated['app_name']]);

        // Handle Logo Upload
        if ($request->hasFile('app_logo')) {
            $this->updateFileSetting('app_logo', $request->file('app_logo'), 'logos');
        }

        // Handle Favicon Upload
        if ($request->hasFile('app_favicon')) {
            $this->updateFileSetting('app_favicon', $request->file('app_favicon'), 'favicons');
        }

        // Clear the config cache to apply changes immediately
        Artisan::call('config:clear');

        return back()->with('success', 'Application settings updated successfully.');
    }

    /**
     * Helper function to update a file-based setting.
     */
    private function updateFileSetting($key, $file, $directory)
    {
        // Delete the old file if it exists
        $oldPath = DB::table('settings')->where('key', $key)->value('value');
        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        // Store the new file and update the database
        $newPath = $file->store($directory, 'public');
        DB::table('settings')->where('key', $key)->update(['value' => $newPath]);
    }
}
