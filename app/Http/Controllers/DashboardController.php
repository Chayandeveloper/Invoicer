<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Client;
use App\Models\Business;
use App\Models\Expense;
use App\Models\Quotation;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistics
        $totalInvoiceRevenue = auth()->user()->invoices()->where('status', 'Paid')->sum('total');
        $totalReceiptRevenue = auth()->user()->salesReceipts()->sum('amount');
        $totalRevenue = $totalInvoiceRevenue + $totalReceiptRevenue;

        $pendingRevenue = auth()->user()->invoices()->where('status', '!=', 'Paid')->sum('total');
        
        $totalPaymentsFromInvoices = auth()->user()->payments()->sum('amount');
        $totalPayments = $totalPaymentsFromInvoices + $totalReceiptRevenue; // Receipts are also considered as payments received
        
        $totalExpenses = auth()->user()->expenses()->where('status', 'Paid')->sum('amount');
        $pendingExpenses = auth()->user()->expenses()->where('status', 'Pending')->sum('amount');
        $netProfit = $totalPayments - $totalExpenses; // Using actual payments for liquid profit

        $invoiceCount = auth()->user()->invoices()->count();
        $quotationCount = auth()->user()->quotations()->count();
        $paymentCount = auth()->user()->payments()->count() + auth()->user()->salesReceipts()->count();
        $clientCount = auth()->user()->clients()->count();

        // Recent Activity
        $recentInvoices = auth()->user()->invoices()->latest()->take(5)->get();
        $recentExpenses = auth()->user()->expenses()->with('business')->latest()->take(5)->get();
        $recentPayments = auth()->user()->payments()->latest()->take(5)->get();

        // Monthly Revenue & Expenses (last 6 months)
        $monthlyInvoiceRevenue = auth()->user()->invoices()->where('status', 'Paid')
            ->where('invoice_date', '>=', Carbon::now()->subMonths(6))
            ->select(
                DB::raw("DATE_FORMAT(invoice_date, '%b %Y') as month"),
                DB::raw('sum(total) as total'),
                DB::raw('max(invoice_date) as max_date')
            )
            ->groupBy('month')
            ->get();

        $monthlyReceiptRevenue = auth()->user()->salesReceipts()
            ->where('receipt_date', '>=', Carbon::now()->subMonths(6))
            ->select(
                DB::raw("DATE_FORMAT(receipt_date, '%b %Y') as month"),
                DB::raw('sum(amount) as total'),
                DB::raw('max(receipt_date) as max_date')
            )
            ->groupBy('month')
            ->get();

        // Merge and group by month for the chart
        $monthlyRevenueMerged = collect();
        $allMonths = $monthlyInvoiceRevenue->pluck('month')
            ->merge($monthlyReceiptRevenue->pluck('month'))
            ->unique();
        
        foreach ($allMonths as $month) {
            $invTotal = $monthlyInvoiceRevenue->where('month', $month)->first()->total ?? 0;
            $recTotal = $monthlyReceiptRevenue->where('month', $month)->first()->total ?? 0;
            $maxDate = max(
                $monthlyInvoiceRevenue->where('month', $month)->first()->max_date ?? '0000-00-00',
                $monthlyReceiptRevenue->where('month', $month)->first()->max_date ?? '0000-00-00'
            );
            
            $monthlyRevenueMerged->push((object)[
                'month' => $month,
                'total' => $invTotal + $recTotal,
                'max_date' => $maxDate
            ]);
        }
        $monthlyRevenue = $monthlyRevenueMerged->sortBy('max_date');

        // Growth Calculation (Current vs Previous Month)
        $currentMonthInvoiceRevenue = auth()->user()->invoices()->where('status', 'Paid')
            ->whereMonth('invoice_date', Carbon::now()->month)
            ->whereYear('invoice_date', Carbon::now()->year)
            ->sum('total');
        $currentMonthReceiptRevenue = auth()->user()->salesReceipts()
            ->whereMonth('receipt_date', Carbon::now()->month)
            ->whereYear('receipt_date', Carbon::now()->year)
            ->sum('amount');
        $currentMonthRevenue = $currentMonthInvoiceRevenue + $currentMonthReceiptRevenue;

        $prevMonthInvoiceRevenue = auth()->user()->invoices()->where('status', 'Paid')
            ->whereMonth('invoice_date', Carbon::now()->subMonth()->month)
            ->whereYear('invoice_date', Carbon::now()->subMonth()->year)
            ->sum('total');
        $prevMonthReceiptRevenue = auth()->user()->salesReceipts()
            ->whereMonth('receipt_date', Carbon::now()->subMonth()->month)
            ->whereYear('receipt_date', Carbon::now()->subMonth()->year)
            ->sum('amount');
        $prevMonthRevenue = $prevMonthInvoiceRevenue + $prevMonthReceiptRevenue;

        $revenueGrowth = $prevMonthRevenue > 0
            ? (($currentMonthRevenue - $prevMonthRevenue) / $prevMonthRevenue) * 100
            : ($currentMonthRevenue > 0 ? 100 : 0);

        // Top Clients (Aggregate from Invoices and Receipts)
        $invoiceTopClients = auth()->user()->invoices()->select('client_name', DB::raw('sum(total) as revenue'))
            ->groupBy('client_name')
            ->get();
            
        $receiptTopClients = auth()->user()->salesReceipts()->select('client_name', DB::raw('sum(amount) as revenue'))
            ->groupBy('client_name')
            ->get();
            
        $topClients = $invoiceTopClients->merge($receiptTopClients)
            ->groupBy('client_name')
            ->map(function($group) {
                return (object)[
                    'client_name' => $group->first()->client_name,
                    'revenue' => $group->sum('revenue')
                ];
            })
            ->sortByDesc('revenue')
            ->take(5)
            ->values();

        // Expense Breakdown
        $expenseBreakdown = auth()->user()->expenses()->select('category', DB::raw('sum(amount) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        $monthlyExpenses = auth()->user()->expenses()->where('expense_date', '>=', Carbon::now()->subMonths(6))
            ->select(
                DB::raw("DATE_FORMAT(expense_date, '%b %Y') as month"),
                DB::raw('sum(amount) as total'),
                DB::raw('max(expense_date) as max_date')
            )
            ->groupBy('month')
            ->orderBy('max_date', 'asc')
            ->get();

        $chartLabels = [];
        $revenueValues = [];
        $expenseValues = [];

        // Determine the end month for the chart (now or the latest data point)
        $latestDataDate = max(
            Carbon::now(),
            $monthlyRevenue->max('max_date') ? Carbon::parse($monthlyRevenue->max('max_date')) : Carbon::now(),
            $monthlyExpenses->max('max_date') ? Carbon::parse($monthlyExpenses->max('max_date')) : Carbon::now()
        );

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::parse($latestDataDate)->subMonths($i)->format('M Y');
            $chartLabels[] = $month;

            $rev = $monthlyRevenue->where('month', $month)->first();
            $revenueValues[] = $rev ? (float)$rev->total : 0;

            $exp = $monthlyExpenses->where('month', $month)->first();
            $expenseValues[] = $exp ? (float)$exp->total : 0;
        }

        $chartLabels = collect($chartLabels);
        $revenueValues = collect($revenueValues);
        $expenseValues = collect($expenseValues);

        // Status Breakdown
        $statusCounts = auth()->user()->invoices()->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        return view('dashboard', compact(
            'totalRevenue',
            'pendingRevenue',
            'totalPayments',
            'totalExpenses',
            'pendingExpenses',
            'netProfit',
            'invoiceCount',
            'quotationCount',
            'paymentCount',
            'clientCount',
            'recentInvoices',
            'recentExpenses',
            'recentPayments',
            'chartLabels',
            'revenueValues',
            'expenseValues',
            'statusCounts',
            'revenueGrowth',
            'topClients',
            'expenseBreakdown'
        ));
    }
}
