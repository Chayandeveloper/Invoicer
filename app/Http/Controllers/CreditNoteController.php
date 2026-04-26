<?php

namespace App\Http\Controllers;

use App\Models\CreditNote;
use App\Models\CreditNoteItem;
use App\Models\Client;
use App\Models\Business;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class CreditNoteController extends Controller
{
    public function index()
    {
        $creditNotes = auth()->user()->creditNotes()->with(['client', 'business'])->latest()->get();
        return view('credit_notes.index', compact('creditNotes'));
    }

    public function create(Request $request)
    {
        $clients = auth()->user()->clients()->get();
        $businesses = auth()->user()->businesses()->get();
        
        $selectedClientId = $request->get('client_id');
        $suggestedItems = [];
        
        if ($selectedClientId) {
            // Suggest items from previous invoices for this client
            $suggestedItems = InvoiceItem::whereHas('invoice', function($q) use ($selectedClientId) {
                $q->where('client_id', $selectedClientId)->where('user_id', auth()->id());
            })->distinct()->pluck('description')->toArray();
        }

        return view('credit_notes.create', compact('clients', 'businesses', 'suggestedItems', 'selectedClientId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'business_id' => 'required|exists:businesses,id',
            'client_id' => 'required|exists:clients,id',
            'credit_note_number' => 'required|string|unique:credit_notes',
            'credit_note_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.rate' => 'required|numeric|min:0',
        ]);

        $totalAmount = 0;
        foreach ($data['items'] as $item) {
            $totalAmount += $item['quantity'] * $item['rate'];
        }

        $creditNote = auth()->user()->creditNotes()->create([
            'business_id' => $data['business_id'],
            'client_id' => $data['client_id'],
            'credit_note_number' => $data['credit_note_number'],
            'credit_note_date' => $data['credit_note_date'],
            'total_amount' => $totalAmount,
            'remaining_amount' => $totalAmount,
            'notes' => $data['notes'],
        ]);

        foreach ($data['items'] as $item) {
            $creditNote->items()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'rate' => $item['rate'],
                'amount' => $item['quantity'] * $item['rate'],
            ]);
        }

        return redirect()->route('credit-notes.index')->with('success', 'Credit Note created successfully');
    }

    public function show($id)
    {
        $creditNote = auth()->user()->creditNotes()->with(['items', 'business', 'client'])->findOrFail($id);
        return view('credit_notes.show', compact('creditNote'));
    }

    public function download($id)
    {
        $creditNote = auth()->user()->creditNotes()->with(['items', 'business', 'client'])->findOrFail($id);
        $pdf = Pdf::loadView('credit_notes.pdf', compact('creditNote'));
        return $pdf->download('CreditNote-' . $creditNote->credit_note_number . '.pdf');
    }

    public function getSuggestions(Request $request)
    {
        $clientId = $request->get('client_id');
        if (!$clientId) return response()->json([]);

        $suggestions = InvoiceItem::whereHas('invoice', function($q) use ($clientId) {
            $q->where('client_id', $clientId)->where('user_id', auth()->id());
        })->distinct()->pluck('description')->toArray();

        return response()->json($suggestions);
    }
}
