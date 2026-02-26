<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Campus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DonationController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage donations');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $donationsQuery = Donation::with('campus');

        if ($user->can('view all campuses')) {
            $selectedCampusId = session('selected_campus_id');
            if ($selectedCampusId && $selectedCampusId !== 'all') {
                $donationsQuery->where('campus_id', $selectedCampusId);
            }
        } else {
            $donationsQuery->where('campus_id', $user->employee->campus_id);
        }

        $donations = $donationsQuery->latest()->paginate(10);
        return view('donations.index', compact('donations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $campuses = Campus::all();
        return view('donations.create', compact('campuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'donor_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'donation_date' => 'required|date',
            'campus_id' => 'nullable|exists:campuses,id', // Can be for "All Campuses"
        ]);

        Donation::create($request->all());

        return redirect()->route('donations.index')->with('success', 'Donation recorded successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Donation $donation)
    {
        $donation->delete();
        return redirect()->route('donations.index')->with('success', 'Donation deleted successfully.');
    }
}