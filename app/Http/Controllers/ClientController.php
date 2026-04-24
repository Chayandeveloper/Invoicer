<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $clients = auth()->user()->clients()->latest()->get();
        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'address' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'industry' => 'nullable|string',
            'website' => 'nullable|url',
            'gst_number' => 'nullable|string',
            'notes' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data['status'] = 'lead';

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('clients', 'public');
        }

        auth()->user()->clients()->create($data);

        return redirect()->route('clients.index')->with('success', 'Client created successfully');
    }

    public function edit($id)
    {
        $client = auth()->user()->clients()->findOrFail($id);
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, $id)
    {
        $client = auth()->user()->clients()->findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string',
            'address' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'industry' => 'nullable|string',
            'website' => 'nullable|url',
            'gst_number' => 'nullable|string',
            'notes' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($client->logo && Storage::disk('public')->exists($client->logo)) {
                Storage::disk('public')->delete($client->logo);
            }
            $data['logo'] = $request->file('logo')->store('clients', 'public');
        }

        $client->update($data);

        return redirect()->route('clients.index')->with('success', 'Client updated successfully');
    }

    public function destroy($id)
    {
        $client = auth()->user()->clients()->findOrFail($id);
        
        if ($client->logo && Storage::disk('public')->exists($client->logo)) {
            Storage::disk('public')->delete($client->logo);
        }
        
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Client deleted successfully');
    }

    public function toggleStatus(Request $request, $id)
    {
        $client = auth()->user()->clients()->findOrFail($id);
        $newStatus = $request->get('status');
        
        if (in_array($newStatus, ['active', 'archived', 'lead'])) {
            $client->update(['status' => $newStatus]);
            return back()->with('success', "Client status updated to {$newStatus}");
        }
        
        return back()->with('error', 'Invalid status');
    }
}
