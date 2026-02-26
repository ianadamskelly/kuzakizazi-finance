<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactSupportMail;
use Illuminate\Support\Facades\Auth;

class HelpController extends Controller
{
    /**
     * Show the help and documentation page.
     */
    public function index()
    {
        return view('help.index');
    }

    /**
     * Handle the contact form submission.
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // Send the email
        Mail::to('help@kuzakizazi.com')->send(new ContactSupportMail($validated));

        return back()->with('success', 'Your message has been sent successfully! We will get back to you shortly.');
    }
}