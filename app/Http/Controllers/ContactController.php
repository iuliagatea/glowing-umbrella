<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function show()
    {
        return view('contact');
    }

    public function send(Request $request)
    {
        // "website" is a honeypot field: real visitors leave it empty
        if ($request->filled('website')) {
            return back();
        }

        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:190',
            'phone' => 'nullable|string|max:40',
            'message' => 'required|string|max:3000',
        ]);

        ContactMessage::create($data);

        return back()->with('success', 'Thank you! We will get back to you soon.');
    }
}
