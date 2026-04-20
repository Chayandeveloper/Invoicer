<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = auth()->user()->expenses()->with('business')->latest();

        if ($request->filled('business_id')) {
            $query->where('business_id', $request->business_id);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->where('expense_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('expense_date', '<=', $request->end_date);
        }

        $expenses = $query->get();
        $businesses = auth()->user()->businesses;
        $categories = auth()->user()->expenses()->select('category')->distinct()->pluck('category');

        return view('expenses.index', compact('expenses', 'businesses', 'categories'));
    }

    public function create()
    {
        $businesses = auth()->user()->businesses;
        return view('expenses.create', compact('businesses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'business_id' => 'nullable|exists:businesses,id',
            'amount' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'expense_date' => 'required|date',
            'category' => 'required|string',
            'description' => 'nullable|string',
            'vendor' => 'nullable|string',
            'reference_number' => 'nullable|string',
            'payment_method' => 'required|string',
            'status' => 'required|string|in:Paid,Pending',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($request->hasFile('receipt')) {
            $data['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        }
        unset($data['receipt']);

        auth()->user()->expenses()->create(array_merge($data, ['user_id' => auth()->id()]));

        return redirect()->route('expenses.index')->with('success', 'Expense recorded successfully');
    }

    public function show($id)
    {
        $expense = auth()->user()->expenses()->findOrFail($id);
        return view('expenses.show', compact('expense'));
    }

    public function download($id)
    {
        $expense = auth()->user()->expenses()->with('business')->findOrFail($id);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('expenses.pdf', compact('expense'));
        return $pdf->download('expense-' . \Carbon\Carbon::parse($expense->expense_date)->format('Ymd') . '-' . $expense->id . '.pdf');
    }

    public function edit($id)
    {
        $expense = auth()->user()->expenses()->findOrFail($id);
        $businesses = auth()->user()->businesses;
        return view('expenses.edit', compact('expense', 'businesses'));
    }

    public function update(Request $request, $id)
    {
        $expense = auth()->user()->expenses()->findOrFail($id);
        $data = $request->validate([
            'business_id' => 'nullable|exists:businesses,id',
            'amount' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'expense_date' => 'required|date',
            'category' => 'required|string',
            'description' => 'nullable|string',
            'vendor' => 'nullable|string',
            'reference_number' => 'nullable|string',
            'payment_method' => 'required|string',
            'status' => 'required|string|in:Paid,Pending',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($request->hasFile('receipt')) {
            // Delete old receipt if exists
            if ($expense->receipt_path) {
                Storage::disk('public')->delete($expense->receipt_path);
            }
            $data['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        }
        unset($data['receipt']);

        $expense->update($data);

        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully');
    }

    public function destroy($id)
    {
        $expense = auth()->user()->expenses()->findOrFail($id);
        if ($expense->receipt_path) {
            Storage::disk('public')->delete($expense->receipt_path);
        }
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully');
    }
}
