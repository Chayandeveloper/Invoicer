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
    public function index()
    {
        $quotations = auth()->user()->quotations()->latest()->get();
        return view('quotations.index', compact('quotations'));
    }

    public function create()
    {
        $businesses = auth()->user()->businesses;
        $clients = auth()->user()->clients;
        return view('quotations.create', compact('businesses', 'clients'));
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

        $quotation = auth()->user()->quotations()->create(array_merge($request->except('items'), [
            'user_id' => auth()->id(),
            'subtotal' => $subtotal,
            'total' => $total,
        ]));

        foreach ($request->items as $item) {
            $lineAmount = $item['quantity'] * $item['unit_price'];
            $quotation->items()->create(array_merge($item, [
                'amount' => $lineAmount
            ]));
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

        $quotation->update(array_merge($request->except('items'), [
            'subtotal' => $subtotal,
            'total' => $total,
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
}
