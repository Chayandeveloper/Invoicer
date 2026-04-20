<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Client;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = auth()->user()->payments()->with('invoice')->latest()->get();
        return view('payments.index', compact('payments'));
    }

    public function create(Request $request)
    {
        $invoices = auth()->user()->invoices()->where('status', '!=', 'Paid')->get();
        $selectedInvoice = null;
        if ($request->has('invoice_id')) {
            $selectedInvoice = auth()->user()->invoices()->find($request->invoice_id);
        }
        $clients = auth()->user()->clients()->get();
        return view('payments.create', compact('invoices', 'selectedInvoice', 'clients'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'invoice_id' => 'nullable|exists:invoices,id',
            'receipt_number' => 'required|string|unique:payments',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string',
            'client_name' => 'nullable|string',
            'client_logo' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if (isset($data['invoice_id'])) {
            $invoice = auth()->user()->invoices()->findOrFail($data['invoice_id']);
            $data['client_logo'] = $invoice->client_logo;
        }

        $payment = auth()->user()->payments()->create(array_merge($data, ['user_id' => auth()->id()]));

        if ($payment->invoice_id) {
            $invoice = auth()->user()->invoices()->find($payment->invoice_id);
            if ($payment->amount >= $invoice->total) {
                $invoice->update(['status' => 'Paid']);
            }
        }

        return redirect()->route('payments.index')->with('success', 'Payment recorded successfully');
    }

    public function show($id)
    {
        $payment = auth()->user()->payments()->findOrFail($id);
        return view('payments.show', compact('payment'));
    }

    public function download($id)
    {
        $payment = auth()->user()->payments()->with('invoice')->findOrFail($id);
        $pdf = Pdf::loadView('payments.pdf', compact('payment'));
        return $pdf->download('Receipt-' . $payment->receipt_number . '.pdf');
    }

    public function destroy($id)
    {
        $payment = auth()->user()->payments()->findOrFail($id);
        $payment->delete();
        return redirect()->route('payments.index')->with('success', 'Payment record deleted');
    }
}
