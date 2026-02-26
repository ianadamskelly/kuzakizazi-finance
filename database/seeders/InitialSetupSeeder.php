<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Campus;
use App\Models\Employee;
use App\Models\Grade;

class InitialSetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // --- 1. Reset cached roles and permissions ---
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // --- 2. Create Permissions ---
        // Using a simple loop to create a batch of permissions.
        $permissions = [
            'view all campuses', 'view own campus',
            'manage students', 'manage employees',
            'manage fees', 'manage expenses', 'manage donations',
            'generate reports', 'manage settings'
        ];
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // --- 3. Create Roles and Assign Permissions ---

        // Role: Campus Finance Officer (can only see their own campus data)
        $financeOfficerRole = Role::create(['name' => 'Campus Finance Officer']);
        $financeOfficerRole->givePermissionTo([
            'view own campus', 'manage fees', 'manage expenses',
            'manage donations', 'generate reports'
        ]);

        // Role: Campus Manager (can manage staff/students on their campus)
        $campusManagerRole = Role::create(['name' => 'Campus Manager']);
        $campusManagerRole->givePermissionTo([
            'view own campus', 'manage students', 'manage employees'
        ]);

        // Role: School Director (can see everything, but can't change core settings)
        $directorRole = Role::create(['name' => 'School Director']);
        $directorRole->givePermissionTo([
            'view all campuses', 'manage students', 'manage employees',
            'manage fees', 'manage expenses', 'manage donations',
            'generate reports'
        ]);

        // Role: Super Admin (has all permissions)
        $superAdminRole = Role::create(['name' => 'Super Admin']);
        $superAdminRole->givePermissionTo(Permission::all());


        // --- 4. Create Campuses ---
        $northCampus = Campus::create(['name' => 'North Campus', 'address' => '123 North Avenue']);
        $southCampus = Campus::create(['name' => 'South Campus', 'address' => '456 South Street']);


        // --- 5. Create Grades ---
        $grades = ['Playgroup', 'PP1', 'PP2', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'];
        foreach ($grades as $gradeName) {
            Grade::create(['name' => $gradeName]);
        }


        // --- 6. Create Users and Assign Roles/Employees ---

        // Create Super Admin User
        $superAdminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@edufinance.test',
            'password' => Hash::make('password')
        ]);
        $superAdminUser->assignRole($superAdminRole);
        // Note: The Super Admin doesn't need an employee profile as they are outside the campus structure.

        // Create School Director User
        $directorUser = User::create([
            'name' => 'Director Jones',
            'email' => 'director@edufinance.test',
            'password' => Hash::make('password')
        ]);
        $directorUser->assignRole($directorRole);
        // The Director also doesn't belong to a single campus. We'll create an employee record for them
        // but might assign them to a "Head Office" campus if we were to create one. For now, we'll use North.
        Employee::create([
            'user_id' => $directorUser->id,
            'campus_id' => $northCampus->id, // Or a dedicated "Head Office" campus ID
            'first_name' => 'Alex',
            'last_name' => 'Jones',
            'job_title' => 'School Director'
        ]);


        // Create Finance Officer for North Campus
        $financeNorthUser = User::create([
            'name' => 'Finance North',
            'email' => 'finance.north@edufinance.test',
            'password' => Hash::make('password')
        ]);
        $financeNorthUser->assignRole($financeOfficerRole);
        Employee::create([
            'user_id' => $financeNorthUser->id,
            'campus_id' => $northCampus->id,
            'first_name' => 'Sarah',
            'last_name' => 'Davis',
            'job_title' => 'Finance Officer'
        ]);

        // Create Finance Officer for South Campus
        $financeSouthUser = User::create([
            'name' => 'Finance South',
            'email' => 'finance.south@edufinance.test',
            'password' => Hash::make('password')
        ]);
        $financeSouthUser->assignRole($financeOfficerRole);
        Employee::create([
            'user_id' => $financeSouthUser->id,
            'campus_id' => $southCampus->id,
            'first_name' => 'Mike',
            'last_name' => 'Miller',
            'job_title' => 'Finance Officer'
        ]);

        $this->command->info('Initial setup complete! You can log in with the created users.');
        $this->command->info('Super Admin: admin@edufinance.test');
        $this->command->info('School Director: director@edufinance.test');
        $this->command->info('Finance (North): finance.north@edufinance.test');
        $this->command->info('Finance (South): finance.south@edufinance.test');
        $this->command->info('Default password for all is: password');
    }
}
