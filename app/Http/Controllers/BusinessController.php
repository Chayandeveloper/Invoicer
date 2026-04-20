<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;

class BusinessController extends Controller
{
    public function index()
    {
        $businesses = auth()->user()->businesses()->latest()->get();
        return view('businesses.index', compact('businesses'));
    }

    public function create()
    {
        return view('businesses.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'address' => 'nullable|string',
            'website' => 'nullable|url',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'bank_details' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        auth()->user()->businesses()->create(array_merge($data, ['user_id' => auth()->id()]));

        return redirect()->route('businesses.index');
    }

    public function edit($id)
    {
        $business = auth()->user()->businesses()->findOrFail($id);
        return view('businesses.edit', compact('business'));
    }

    public function update(Request $request, $id)
    {
        $business = auth()->user()->businesses()->findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string',
            'address' => 'nullable|string',
            'website' => 'nullable|url',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'bank_details' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($business->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($business->logo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($business->logo);
            }
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $business->update($data);

        return redirect()->route('businesses.index');
    }
}
