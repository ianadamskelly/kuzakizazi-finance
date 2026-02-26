<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use App\Models\Campus;
use App\Models\EmployeeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission; // Import Permission model
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage employees');
    }

    public function index()
    {
        $user = Auth::user();
        // Eager load direct permissions as well
        $employeesQuery = Employee::with(['user.roles', 'user.permissions', 'campus', 'employeeCategory']);

        if ($user->can('view all campuses')) {
            $selectedCampusId = session('selected_campus_id');
            if ($selectedCampusId && $selectedCampusId !== 'all') {
                $employeesQuery->where('campus_id', $selectedCampusId);
            }
        } else {
            $employeesQuery->where('campus_id', $user->employee->campus_id);
        }

        $employees = $employeesQuery->latest()->paginate(10);
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        $campuses = Campus::all();
        $roles = Role::where('name', '!=', 'Super Admin')->pluck('name', 'name');
        $employeeCategories = EmployeeCategory::all();
        $permissions = Permission::all()->groupBy(function($item) {
            return explode(' ', $item->name)[1]; // Group by resource (e.g., 'students', 'employees')
        });
        return view('employees.create', compact('campuses', 'roles', 'employeeCategories', 'permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'job_title' => 'required|string|max:255',
            'campus_id' => 'nullable|exists:campuses,id',
            'employee_category_id' => 'required|exists:employee_categories,id',
            'create_user_account' => 'nullable|boolean',
            'email' => ['required_if:create_user_account,1', 'nullable', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required_if:create_user_account,1', 'nullable', 'exists:roles,name'],
            'permissions' => 'nullable|array'
        ]);

        $employeeData = $request->only(['first_name', 'last_name', 'job_title', 'campus_id', 'employee_category_id']);
        
        if ($request->boolean('create_user_account')) {
            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'password' => Hash::make(Str::random(10)),
            ]);
            $user->assignRole($request->role);

            // Sync direct permissions
            if ($request->has('permissions')) {
                $user->syncPermissions($request->permissions);
            }

            $employeeData['user_id'] = $user->id;
        }

        Employee::create($employeeData);

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    public function edit(Employee $employee)
    {
        $employee->load('user.roles', 'user.permissions');
        $campuses = Campus::all();
        $roles = Role::where('name', '!=', 'Super Admin')->pluck('name', 'name');
        $employeeCategories = EmployeeCategory::all();
        $permissions = Permission::all()->groupBy(function($item) {
            return explode(' ', $item->name)[1];
        });
        return view('employees.edit', compact('employee', 'campuses', 'roles', 'employeeCategories', 'permissions'));
    }

    public function update(Request $request, Employee $employee)
    {
        $user = $employee->user;

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'job_title' => 'required|string|max:255',
            'campus_id' => 'nullable|exists:campuses,id',
            'employee_category_id' => 'required|exists:employee_categories,id',
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user?->id)],
            'role' => ['nullable', 'exists:roles,name'],
            'permissions' => 'nullable|array'
        ]);
        
        $employee->update($request->only(['first_name', 'last_name', 'job_title', 'campus_id', 'employee_category_id']));

        if ($user) {
            $user->update([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
            ]);
            if ($request->role) {
                $user->syncRoles([$request->role]);
            }
            // Sync direct permissions, removing any that were unchecked
            $user->syncPermissions($request->permissions ?? []);
        }

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }
}