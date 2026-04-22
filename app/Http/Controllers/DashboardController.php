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
        $totalRevenue = auth()->user()->invoices()->where('status', 'Paid')->sum('total');
        $pendingRevenue = auth()->user()->invoices()->where('status', '!=', 'Paid')->sum('total');
        $totalPayments = auth()->user()->payments()->sum('amount');
        $totalExpenses = auth()->user()->expenses()->where('status', 'Paid')->sum('amount');
        $pendingExpenses = auth()->user()->expenses()->where('status', 'Pending')->sum('amount');
        $netProfit = $totalPayments - $totalExpenses; // Using actual payments for liquid profit

        $invoiceCount = auth()->user()->invoices()->count();
        $quotationCount = auth()->user()->quotations()->count();
        $paymentCount = auth()->user()->payments()->count();
        $clientCount = auth()->user()->clients()->count();

        // Recent Activity
        $recentInvoices = auth()->user()->invoices()->latest()->take(5)->get();
        $recentExpenses = auth()->user()->expenses()->with('business')->latest()->take(5)->get();
        $recentPayments = auth()->user()->payments()->latest()->take(5)->get();

        // Monthly Revenue & Expenses (last 6 months)
        $monthlyRevenue = auth()->user()->invoices()->where('status', 'Paid')
            ->where('invoice_date', '>=', Carbon::now()->subMonths(6))
            ->select(
                DB::raw("DATE_FORMAT(invoice_date, '%b %Y') as month"),
                DB::raw('sum(total) as total'),
                DB::raw('max(invoice_date) as max_date')
            )
            ->groupBy('month')
            ->orderBy('max_date', 'asc')
            ->get();

        // Growth Calculation (Current vs Previous Month)
        $currentMonthRevenue = auth()->user()->invoices()->where('status', 'Paid')
            ->whereMonth('invoice_date', Carbon::now()->month)
            ->whereYear('invoice_date', Carbon::now()->year)
            ->sum('total');

        $prevMonthRevenue = auth()->user()->invoices()->where('status', 'Paid')
            ->whereMonth('invoice_date', Carbon::now()->subMonth()->month)
            ->whereYear('invoice_date', Carbon::now()->subMonth()->year)
            ->sum('total');

        $revenueGrowth = $prevMonthRevenue > 0
            ? (($currentMonthRevenue - $prevMonthRevenue) / $prevMonthRevenue) * 100
            : ($currentMonthRevenue > 0 ? 100 : 0);

        // Top Clients
        $topClients = auth()->user()->invoices()->select('client_name', DB::raw('sum(total) as revenue'))
            ->groupBy('client_name')
            ->orderByDesc('revenue')
            ->take(5)
            ->get();

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
