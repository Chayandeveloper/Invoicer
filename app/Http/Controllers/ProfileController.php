<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $businessCount = $user->businesses()->count();
        $clientCount = $user->clients()->count();
        
        return view('profile.index', compact('user', 'businessCount', 'clientCount'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = auth()->user();
        $user->update([
            'name' => $request->name,
        ]);

        return back()->with('success', 'Profile updated successfully!');
    }
}
