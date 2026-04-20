<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;

use App\Models\Business;
use App\Models\Client;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\InvoiceMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    public function sendEmail($id)
    {
        $invoice = auth()->user()->invoices()->with('items')->findOrFail($id);

        $client = auth()->user()->clients()->where('name', $invoice->client_name)->first();
        $email = $client->email ?? 'client@example.com';

        Mail::to($email)->send(new InvoiceMail($invoice));

        $invoice->update(['status' => 'Sent']);

        return back()->with('success', 'Invoice has been sent to ' . $email);
    }
    public function download($id)
    {
        $invoice = auth()->user()->invoices()->with('items')->findOrFail($id);

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));

        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }
    public function index()
    {
        $invoices = auth()->user()->invoices()->latest()->get();
        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $businesses = auth()->user()->businesses;
        $clients = auth()->user()->clients;

        return view('invoices.create', compact('businesses', 'clients'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_name' => 'required|string',
            'client_address' => 'nullable|string',
            'invoice_number' => 'required|string|unique:invoices',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date',

            'sender_name' => 'required|string',
            'sender_address' => 'nullable|string',
            'sender_website' => 'nullable|string',
            'sender_phone' => 'nullable|string',
            'client_phone' => 'nullable|string',
            'client_logo' => 'nullable|string',
            'bank_details' => 'nullable|string',
            'logo' => 'nullable|string', // URL/Path
            'tax_rate' => 'nullable|numeric|min:0',
            'payment_qr_link' => 'nullable|string',
            'payment_qr_image' => 'nullable|image|max:2048',

            'items' => 'required|array',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0',
        ]);

        $subtotal = 0;
        $totalItemTax = 0;

        foreach ($request->items as $item) {
            $lineAmount = $item['quantity'] * $item['unit_price'];
            $subtotal += $lineAmount;

            $itemTaxRate = $item['tax_rate'] ?? 0;
            $totalItemTax += $lineAmount * ($itemTaxRate / 100);
        }

        $globalTaxRate = $data['tax_rate'] ?? 0;
        $globalTax = $subtotal * ($globalTaxRate / 100);

        $totalTax = $totalItemTax + $globalTax;
        $total = $subtotal + $totalTax;

        // Auto-fetch client logo from clients table if not provided
        $clientLogo = $data['client_logo'] ?? null;
        if (!$clientLogo) {
            $clientRecord = auth()->user()->clients()->where('name', $data['client_name'])->first();
            $clientLogo = $clientRecord?->logo;
        }

        if ($request->hasFile('payment_qr_image')) {
            $data['payment_qr_image'] = $request->file('payment_qr_image')->store('qr_codes', 'public');
        }

        $invoice = auth()->user()->invoices()->create([
            'user_id' => auth()->id(),
            'sender_name' => $data['sender_name'],
            'sender_address' => $data['sender_address'],
            'sender_website' => $data['sender_website'],
            'sender_phone' => $data['sender_phone'] ?? null,
            'bank_details' => $data['bank_details'],
            'logo' => $data['logo'] ?? null,
            'payment_qr_link' => $data['payment_qr_link'] ?? null,
            'payment_qr_image' => $data['payment_qr_image'] ?? null,

            'client_name' => $data['client_name'],
            'client_address' => $data['client_address'],
            'client_phone' => $data['client_phone'] ?? null,
            'client_logo' => $clientLogo,
            'invoice_number' => $data['invoice_number'],
            'invoice_date' => $data['invoice_date'],
            'due_date' => $data['due_date'],
            'subtotal' => $subtotal,
            'tax' => $totalTax, // Storing total tax combined
            'tax_rate' => $globalTaxRate, // Storing global rate for reference
            'total' => $total,
        ]);

        foreach ($request->items as $item) {
            $amount = $item['quantity'] * $item['unit_price'];
            $invoice->items()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'tax_rate' => $item['tax_rate'] ?? 0,
                'amount' => $amount,
            ]);
        }

        return redirect()->route('invoices.show', $invoice->id);
    }

    public function show($id)
    {
        $invoice = auth()->user()->invoices()->with('items')->findOrFail($id);
        return view('invoices.show', compact('invoice'));
    }

    public function edit($id)
    {
        $invoice = auth()->user()->invoices()->with('items')->findOrFail($id);
        $businesses = auth()->user()->businesses;
        $clients = auth()->user()->clients;
        return view('invoices.edit', compact('invoice', 'businesses', 'clients'));
    }

    public function update(Request $request, $id)
    {
        $invoice = auth()->user()->invoices()->findOrFail($id);

        $data = $request->validate([
            'client_name' => 'required|string',
            'client_address' => 'nullable|string',
            'invoice_number' => 'required|string|unique:invoices,invoice_number,' . $id,
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date',

            'sender_name' => 'required|string',
            'sender_address' => 'nullable|string',
            'sender_website' => 'nullable|string',
            'sender_phone' => 'nullable|string',
            'client_phone' => 'nullable|string',
            'client_logo' => 'nullable|string',
            'bank_details' => 'nullable|string',
            'logo' => 'nullable|string',
            'tax_rate' => 'nullable|numeric|min:0',
            'payment_qr_link' => 'nullable|string',
            'payment_qr_image' => 'nullable|image|max:2048',

            'items' => 'required|array',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0',
        ]);

        $subtotal = 0;
        $totalItemTax = 0;

        foreach ($request->items as $item) {
            $lineAmount = $item['quantity'] * $item['unit_price'];
            $subtotal += $lineAmount;

            $itemTaxRate = $item['tax_rate'] ?? 0;
            $totalItemTax += $lineAmount * ($itemTaxRate / 100);
        }

        $globalTaxRate = $data['tax_rate'] ?? 0;
        $globalTax = $subtotal * ($globalTaxRate / 100);

        $totalTax = $totalItemTax + $globalTax;
        $total = $subtotal + $totalTax;

        // Auto-fetch client logo from clients table if not provided
        $clientLogo = $data['client_logo'] ?? null;
        if (!$clientLogo) {
            $clientRecord = auth()->user()->clients()->where('name', $data['client_name'])->first();
            $clientLogo = $clientRecord?->logo;
        }

        if ($request->hasFile('payment_qr_image')) {
            if ($invoice->payment_qr_image && Storage::disk('public')->exists($invoice->payment_qr_image)) {
                Storage::disk('public')->delete($invoice->payment_qr_image);
            }
            $data['payment_qr_image'] = $request->file('payment_qr_image')->store('qr_codes', 'public');
        }

        $invoice->update([
            'sender_name' => $data['sender_name'],
            'sender_address' => $data['sender_address'],
            'sender_website' => $data['sender_website'],
            'sender_phone' => $data['sender_phone'] ?? null,
            'bank_details' => $data['bank_details'],
            'logo' => $data['logo'] ?? null,
            'payment_qr_link' => $data['payment_qr_link'] ?? null,
            'payment_qr_image' => $data['payment_qr_image'] ?? $invoice->payment_qr_image,

            'client_name' => $data['client_name'],
            'client_address' => $data['client_address'],
            'client_phone' => $data['client_phone'] ?? null,
            'client_logo' => $clientLogo,
            'invoice_number' => $data['invoice_number'],
            'invoice_date' => $data['invoice_date'],
            'due_date' => $data['due_date'],
            'subtotal' => $subtotal,
            'tax' => $totalTax,
            'tax_rate' => $globalTaxRate,
            'total' => $total,
        ]);

        // Replace items
        $invoice->items()->delete();
        foreach ($request->items as $item) {
            $amount = $item['quantity'] * $item['unit_price'];
            $invoice->items()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'tax_rate' => $item['tax_rate'] ?? 0,
                'amount' => $amount,
            ]);
        }

        return redirect()->route('invoices.show', $invoice->id)->with('success', 'Invoice updated successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $invoice = auth()->user()->invoices()->findOrFail($id);
        $invoice->update(['status' => $request->status]);
        return back();
    }
}
