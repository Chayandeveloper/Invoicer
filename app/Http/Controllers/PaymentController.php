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
        
        // Append remaining balance to each invoice for the view
        foreach ($invoices as $invoice) {
            $invoice->balance = $invoice->remaining_balance;
        }

        $selectedInvoice = null;
        if ($request->has('invoice_id')) {
            $selectedInvoice = auth()->user()->invoices()->with('payments')->find($request->invoice_id);
            if ($selectedInvoice) {
                $selectedInvoice->balance = $selectedInvoice->remaining_balance;
            }
        }
        $clients = auth()->user()->clients()->get()->map(function($client) {
            $client->available_credit = $client->available_credit;
            return $client;
        });
        $businesses = auth()->user()->businesses()->get();
        return view('payments.create', compact('invoices', 'selectedInvoice', 'clients', 'businesses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'invoice_id' => 'nullable|exists:invoices,id',
            'client_id' => 'nullable|exists:clients,id',
            'receipt_number' => 'required|string|unique:payments',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string',
            'client_name' => 'nullable|string',
            'client_logo' => 'nullable|string',
            'notes' => 'nullable|string',
            'business_id' => 'required|exists:businesses,id',
            'use_credit' => 'nullable|boolean',
            'credit_amount' => 'nullable|numeric|min:0',
        ]);

        if (isset($data['invoice_id'])) {
            $invoice = auth()->user()->invoices()->findOrFail($data['invoice_id']);
            // Use the logo from the invoice if linked, otherwise use what was submitted
            $data['client_logo'] = $invoice->logo; 
        }

        // Explicitly set user_id to be safe
        $data['user_id'] = auth()->id();
        
        $creditApplied = 0;
        if ($request->has('use_credit') && $request->credit_amount > 0) {
            $creditAmountToUse = $request->credit_amount;
            
            // Find client to get their credit notes
            $clientId = $data['client_id'] ?? null;
            if (!$clientId && isset($data['invoice_id'])) {
                $invoice = auth()->user()->invoices()->find($data['invoice_id']);
                $clientId = $invoice->client_id;
            }

            if ($clientId) {
                $client = auth()->user()->clients()->find($clientId);
                if ($client) {
                    $creditNotes = $client->creditNotes()->where('remaining_amount', '>', 0)->orderBy('credit_note_date', 'asc')->get();
                    foreach ($creditNotes as $cn) {
                        if ($creditAmountToUse <= 0) break;
                        
                        $useFromThisNote = min($cn->remaining_amount, $creditAmountToUse);
                        $cn->decrement('remaining_amount', $useFromThisNote);
                        $creditAmountToUse -= $useFromThisNote;
                        $creditApplied += $useFromThisNote;
                    }
                }
            }
        }

        $data['credit_applied'] = $creditApplied;

        // Deduction logic: If payment amount entered is greater than the credit applied,
        // subtract the credit from the payment amount to show the "less amount" (net cash payment).
        if ($creditApplied > 0 && $data['amount'] > $creditApplied) {
            $data['amount'] = $data['amount'] - $creditApplied;
        }

        $payment = auth()->user()->payments()->create($data);

        if ($payment->invoice_id) {
            $invoice = auth()->user()->invoices()->with('payments')->find($payment->invoice_id);
            
            // Calculate total paid including this payment (Amount + Credit Applied)
            $totalPaid = $invoice->payments()->sum('amount') + $invoice->payments()->sum('credit_applied');
            
            if ($totalPaid >= $invoice->total) {
                $invoice->update(['status' => 'Paid']);
            } else {
                $invoice->update(['status' => 'Partial']);
            }
        }

        return redirect()->route('payments.show', $payment->id)->with('success', 'Payment recorded and receipt generated');
    }

    public function show($id)
    {
        $payment = auth()->user()->payments()->with(['invoice.business', 'business'])->findOrFail($id);
        return view('payments.show', compact('payment'));
    }

    public function download($id)
    {
        $payment = auth()->user()->payments()->with(['invoice.business', 'business'])->findOrFail($id);
        $pdf = Pdf::loadView('payments.pdf', compact('payment'));
        return $pdf->download('Receipt-' . $payment->receipt_number . '.pdf');
    }

    public function destroy($id)
    {
        $payment = auth()->user()->payments()->findOrFail($id);
        
        // Reset invoice status if linked
        if ($payment->invoice_id) {
            $invoice = auth()->user()->invoices()->find($payment->invoice_id);
            if ($invoice) {
                $invoice->update(['status' => 'Pending']);
            }
        }

        $payment->delete();
        return redirect()->route('payments.index')->with('success', 'Payment record deleted and invoice status reset to pending');
    }
}
