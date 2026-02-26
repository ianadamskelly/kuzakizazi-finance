<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Campus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function __construct()
    {
        // Apply permissions to all methods in this controller
        $this->middleware('can:manage expenses');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $expensesQuery = Expense::with('campus');

        // Scope the query based on user's permissions
        if ($user->can('view all campuses')) {
            $selectedCampusId = session('selected_campus_id');
            if ($selectedCampusId && $selectedCampusId !== 'all') {
                $expensesQuery->where('campus_id', $selectedCampusId);
            }
        } else {
            $expensesQuery->where('campus_id', $user->employee->campus_id);
        }

        $expenses = $expensesQuery->latest()->paginate(10);

        return view('expenses.index', compact('expenses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // A Finance Officer can only add expenses to their own campus.
        // A Director can choose which campus to assign the expense to.
        $campuses = Auth::user()->can('view all campuses') ? Campus::all() : Campus::where('id', Auth::user()->employee->campus_id)->get();
        return view('expenses.create', compact('campuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'fund_source' => 'required|string|in:fees,food,transport,others',
            'expense_date' => 'required|date',
            'campus_id' => 'required|exists:campuses,id',
            'vendor' => 'nullable|string|max:255',
            'receipt_path' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        Expense::create($request->all());

        return redirect()->route('expenses.index')->with('success', 'Expense recorded successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense)
    {
        $campuses = Auth::user()->can('view all campuses') ? Campus::all() : Campus::where('id', Auth::user()->employee->campus_id)->get();
        return view('expenses.edit', compact('expense', 'campuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'fund_source' => 'required|string|in:fees,food,transport,others',
            'expense_date' => 'required|date',
            'campus_id' => 'required|exists:campuses,id',
            'vendor' => 'nullable|string|max:255',
            'receipt' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
        ]);

        if ($request->hasFile('receipt')) {
            // If a new receipt is uploaded, delete the old one first
            if ($expense->receipt_path) {
                Storage::disk('public')->delete($expense->receipt_path);
            }
            $validated['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        }

        $expense->update($request->all());

        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        // Also delete the associated receipt file from storage
        if ($expense->receipt_path) {
            Storage::disk('public')->delete($expense->receipt_path);
        }
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully.');
    }
}