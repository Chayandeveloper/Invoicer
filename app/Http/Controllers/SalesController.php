<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function clients(Request $request)
    {
        $status = $request->get('status', 'active');
        
        $query = auth()->user()->clients()->latest();

        // Filter by status
        if ($status === 'lead') {
            // Prospects/Leads are those with status 'lead' AND NO invoices
            $query->where('status', 'lead')->doesntHave('invoices');
        } elseif ($status === 'active') {
            // Active includes those with status 'active' OR anyone with at least one invoice
            $query->where(function($q) {
                $q->where('status', 'active')
                  ->orWhereHas('invoices');
            });
        } elseif ($status !== 'all') {
            $query->where('status', $status);
        }

        $clients = $query->get();

        $totalInvoiced = $clients->sum->total_invoiced;
        $pendingAmount = $clients->sum->pending_balance;

        return view('sales.index', compact('clients', 'status', 'totalInvoiced', 'pendingAmount'));
    }

}
