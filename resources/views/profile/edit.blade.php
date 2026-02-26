<x-app-layout>
    <div>
        <h1 class="text-2xl md:text-3xl text-slate-800 font-bold mb-8">Profile Settings</h1>

        <div class="space-y-8">
            <!-- Update Profile Information -->
            <div class="bg-white shadow-lg rounded-2xl p-6 border border-slate-200">
                @include('profile.partials.update-profile-information-form')
            </div>

            <!-- Update Password -->
            <div class="bg-white shadow-lg rounded-2xl p-6 border border-slate-200">
                @include('profile.partials.update-password-form')
            </div>

            <!-- Delete Account -->
            <div class="bg-white shadow-lg rounded-2xl p-6 border border-slate-200">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>