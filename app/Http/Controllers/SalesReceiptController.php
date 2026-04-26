<?php

namespace App\Http\Controllers;

use App\Models\SalesReceipt;
use App\Models\Invoice;
use App\Models\Business;
use App\Models\Client;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class SalesReceiptController extends Controller
{
    public function index()
    {
        $receipts = auth()->user()->salesReceipts()->with(['business', 'invoice'])->latest()->get();
        return view('sales_receipts.index', compact('receipts'));
    }

    public function create(Request $request)
    {
        $clients = auth()->user()->clients()->get();
        $businesses = auth()->user()->businesses()->get();
        return view('sales_receipts.create', compact('clients', 'businesses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'receipt_number' => 'required|string|unique:sales_receipts',
            'receipt_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'item_description' => 'nullable|string',
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string',
            'client_name' => 'nullable|string',
            'client_logo' => 'nullable|string',
            'notes' => 'nullable|string',
            'business_id' => 'required|exists:businesses,id',
        ]);

        $data['user_id'] = auth()->id();
        $receipt = auth()->user()->salesReceipts()->create($data);

        return redirect()->route('sales-receipts.show', $receipt->id)->with('success', 'Sales receipt generated successfully');
    }

    public function show($id)
    {
        $receipt = auth()->user()->salesReceipts()->with(['invoice.business', 'business'])->findOrFail($id);
        return view('sales_receipts.show', compact('receipt'));
    }

    public function download($id)
    {
        $receipt = auth()->user()->salesReceipts()->with(['invoice.business', 'business'])->findOrFail($id);
        $pdf = Pdf::loadView('sales_receipts.pdf', compact('receipt'));
        return $pdf->download('SalesReceipt-' . $receipt->receipt_number . '.pdf');
    }

    public function destroy($id)
    {
        $receipt = auth()->user()->salesReceipts()->findOrFail($id);
        $receipt->delete();
        return redirect()->route('sales-receipts.index')->with('success', 'Sales receipt deleted');
    }
}
