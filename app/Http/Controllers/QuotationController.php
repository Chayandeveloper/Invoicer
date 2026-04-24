<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\Business;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $query = auth()->user()->quotations()->latest();
        $filteredClient = null;

        if ($request->has('client_id')) {
            $query->where('client_id', $request->client_id);
            $filteredClient = auth()->user()->clients()->find($request->client_id);
        }

        $quotations = $query->get();
        return view('quotations.index', compact('quotations', 'filteredClient'));
    }

    public function create(Request $request)
    {
        $businesses = auth()->user()->businesses;
        $clients = auth()->user()->clients;
        $selected_client = null;

        if ($request->has('client_id')) {
            $selected_client = auth()->user()->clients()->find($request->client_id);
        }

        return view('quotations.create', compact('businesses', 'clients', 'selected_client'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sender_name' => 'required|string',
            'sender_address' => 'nullable|string',
            'sender_website' => 'nullable|string',
            'sender_phone' => 'nullable|string',
            'sender_logo' => 'nullable|string',
            'client_name' => 'required|string',
            'client_id' => 'nullable|exists:clients,id',
            'client_phone' => 'nullable|string',
            'client_logo' => 'nullable|string',
            'client_address' => 'nullable|string',
            'quotation_number' => 'required|string|unique:quotations',
            'quotation_date' => 'required|date',
            'expiry_date' => 'nullable|date',
            'tax_rate' => 'nullable|numeric|min:0',
            'bank_details' => 'nullable|string',
            'payment_qr_link' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0',
        ]);

        $subtotal = 0;
        $totalItemTax = 0;

        foreach ($request->items as $item) {
            $lineAmount = $item['quantity'] * $item['unit_price'];
            $lineTax = $lineAmount * (($item['tax_rate'] ?? 0) / 100);
            $subtotal += $lineAmount;
            $totalItemTax += $lineTax;
        }

        $globalTax = $subtotal * (($request->tax_rate ?? 0) / 100);
        $total = $subtotal + $totalItemTax + $globalTax;

        $quotation = auth()->user()->quotations()->create(array_merge($request->except(['items', 'action']), [
            'user_id' => auth()->id(),
            'subtotal' => $subtotal,
            'total' => $total,
            'status' => $request->input('action') === 'draft' ? 'Draft' : 'Pending',
        ]));

        foreach ($request->items as $item) {
            $lineAmount = $item['quantity'] * $item['unit_price'];
            $quotation->items()->create(array_merge($item, [
                'amount' => $lineAmount
            ]));
        }

        // Auto-activate client if they are currently a lead
        if ($quotation->client_id) {
            $client = auth()->user()->clients()->find($quotation->client_id);
            if ($client && $client->status === 'lead') {
                $client->update(['status' => 'active']);
            }
        } elseif ($quotation->client_name) {
            $client = auth()->user()->clients()->where('name', $quotation->client_name)->first();
            if ($client) {
                // Link the quotation to the found client
                $quotation->update(['client_id' => $client->id]);
                
                if ($client->status === 'lead') {
                    $client->update(['status' => 'active']);
                }
            }
        }

        return redirect()->route('quotations.index')->with('success', 'Quotation created successfully');
    }

    public function show($id)
    {
        $quotation = auth()->user()->quotations()->with('items')->findOrFail($id);
        return view('quotations.show', compact('quotation'));
    }

    public function edit($id)
    {
        $quotation = auth()->user()->quotations()->with('items')->findOrFail($id);
        $businesses = auth()->user()->businesses;
        $clients = auth()->user()->clients;
        return view('quotations.edit', compact('quotation', 'businesses', 'clients'));
    }

    public function update(Request $request, $id)
    {
        $quotation = auth()->user()->quotations()->findOrFail($id);

        $data = $request->validate([
            'sender_name' => 'required|string',
            'sender_address' => 'nullable|string',
            'sender_website' => 'nullable|string',
            'sender_phone' => 'nullable|string',
            'sender_logo' => 'nullable|string',
            'client_name' => 'required|string',
            'client_phone' => 'nullable|string',
            'client_logo' => 'nullable|string',
            'client_address' => 'nullable|string',
            'quotation_number' => 'required|string|unique:quotations,quotation_number,' . $id,
            'quotation_date' => 'required|date',
            'expiry_date' => 'nullable|date',
            'tax_rate' => 'nullable|numeric|min:0',
            'bank_details' => 'nullable|string',
            'payment_qr_link' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0',
        ]);

        $subtotal = 0;
        $totalItemTax = 0;

        foreach ($request->items as $item) {
            $lineAmount = $item['quantity'] * $item['unit_price'];
            $lineTax = $lineAmount * (($item['tax_rate'] ?? 0) / 100);
            $subtotal += $lineAmount;
            $totalItemTax += $lineTax;
        }

        $globalTax = $subtotal * (($request->tax_rate ?? 0) / 100);
        $total = $subtotal + $totalItemTax + $globalTax;

        $status = $quotation->status;
        if ($request->input('action') === 'draft') {
            $status = 'Draft';
        } elseif ($quotation->status === 'Draft' && $request->input('action') === 'generate') {
            $status = 'Pending';
        }

        $quotation->update(array_merge($request->except(['items', 'action']), [
            'subtotal' => $subtotal,
            'total' => $total,
            'status' => $status,
        ]));

        $quotation->items()->delete();
        foreach ($request->items as $item) {
            $lineAmount = $item['quantity'] * $item['unit_price'];
            $quotation->items()->create(array_merge($item, [
                'amount' => $lineAmount
            ]));
        }

        return redirect()->route('quotations.show', $quotation->id)->with('success', 'Quotation updated successfully');
    }

    public function download($id)
    {
        $quotation = auth()->user()->quotations()->with('items')->findOrFail($id);
        $pdf = Pdf::loadView('quotations.pdf', compact('quotation'));
        return $pdf->download('Quotation-' . $quotation->quotation_number . '.pdf');
    }

    public function convertToInvoice($id)
    {
        $quotation = auth()->user()->quotations()->with('items')->findOrFail($id);

        $invoice = auth()->user()->invoices()->create([
            'user_id' => auth()->id(),
            'sender_name' => $quotation->sender_name,
            'sender_address' => $quotation->sender_address,
            'sender_website' => $quotation->sender_website,
            'sender_phone' => $quotation->sender_phone,
            'logo' => $quotation->sender_logo,
            'client_name' => $quotation->client_name,
            'client_address' => $quotation->client_address,
            'client_phone' => $quotation->client_phone,
            'client_logo' => $quotation->client_logo,
            'invoice_number' => 'INV-' . str_replace('QT-', '', $quotation->quotation_number),
            'invoice_date' => now()->format('Y-m-d'),
            'subtotal' => $quotation->subtotal,
            'tax' => $quotation->total - $quotation->subtotal,
            'total' => $quotation->total,
            'status' => 'Pending',
            'bank_details' => $quotation->bank_details,
            'payment_qr_link' => $quotation->payment_qr_link,
            'tax_rate' => $quotation->tax_rate,
        ]);

        foreach ($quotation->items as $item) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $item->description,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'tax_rate' => $item->tax_rate,
                'amount' => $item->amount,
            ]);
        }

        $quotation->update(['status' => 'Invoiced']);

        return redirect()->route('invoices.show', $invoice->id)->with('success', 'Quotation converted to invoice successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $quotation = auth()->user()->quotations()->findOrFail($id);
        $request->validate(['status' => 'required|string']);
        $quotation->update(['status' => $request->status]);
        return back()->with('success', 'Status updated successfully');
    }

    public function destroy($id)
    {
        $quotation = auth()->user()->quotations()->findOrFail($id);
        $quotation->items()->delete();
        $quotation->delete();

        return redirect()->route('quotations.index')->with('success', 'Quotation deleted successfully');
    }
}
