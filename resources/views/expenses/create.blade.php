@extends('layout')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('expenses.index') }}"
                class="p-2 bg-white rounded-xl border border-gray-100 shadow-sm text-gray-400 hover:text-red-500 transition">
                <i class="fas fa-chevron-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Record Expense</h1>
                <p class="text-sm text-gray-500 font-medium">Log a new business expenditure</p>
            </div>
        </div>

        <div class="bg-white p-6 sm:p-10 rounded-2xl border border-gray-100 shadow-sm">
            <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf

                <div class="space-y-6">
                    <!-- Business & Category -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Business
                                Profile</label>
                            <select name="business_id"
                                class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-red-500 focus:border-red-500 p-4">
                                <option value="">-- Select Business --</option>
                                @foreach($businesses as $business)
                                    <option value="{{ $business->id }}">{{ $business->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Category</label>
                            <select name="category" required
                                class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-red-500 focus:border-red-500 p-4">
                                <option value="Travel">Travel</option>
                                <option value="Office Supplies">Office Supplies</option>
                                <option value="Software/SaaS">Software/SaaS</option>
                                <option value="Marketing">Marketing</option>
                                <option value="Salaries">Salaries</option>
                                <option value="Rent">Rent</option>
                                <option value="Utilities">Utilities</option>
                                <option value="Health">Health</option>
                                <option value="Education">Education</option>
                                <option value="Entertainment">Entertainment</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>

                    <!-- Amount & Tax -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Total
                                Amount (₹)</label>
                            <input type="number" name="amount" step="0.01" required placeholder="0.00"
                                class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-black focus:ring-red-500 focus:border-red-500 p-4 text-red-600">
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Tax
                                (Included)</label>
                            <input type="number" name="tax_amount" step="0.01" value="0" placeholder="0.00"
                                class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-red-500 focus:border-red-500 p-4">
                        </div>
                    </div>

                    <!-- Date & Ref -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Expense
                                Date</label>
                            <input type="date" name="expense_date" value="{{ date('Y-m-d') }}" required
                                class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-red-500 focus:border-red-500 p-4">
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Ref #
                                / Invoice</label>
                            <input type="text" name="reference_number" placeholder="E.g. INV-2024-001"
                                class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-red-500 focus:border-red-500 p-4">
                        </div>
                    </div>

                    <!-- Vendor & Payment -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Vendor
                                / Payee</label>
                            <input type="text" name="vendor" placeholder="Who are you paying?"
                                class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-red-500 focus:border-red-500 p-4">
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Payment
                                Method</label>
                            <select name="payment_method" required
                                class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-red-500 focus:border-red-500 p-4">
                                <option value="Cash">Cash</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Credit Card">Credit Card</option>
                                <option value="Debit Card">Debit Card</option>
                                <option value="UPI">UPI / Digital Wallet</option>
                                <option value="Cheque">Cheque</option>
                            </select>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label
                            class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Purpose /
                            Description</label>
                        <textarea name="description" rows="2" placeholder="What was this expense for?"
                            class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-medium focus:ring-red-500 focus:border-red-500 p-4"></textarea>
                    </div>

                    <!-- Receipt & Status -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                        <div>
                            <label
                                class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Receipt
                                (Image/PDF)</label>
                            <div
                                class="mt-1 flex justify-center px-4 pt-4 pb-4 border-2 border-gray-100 border-dashed rounded-2xl bg-gray-50/50">
                                <div class="space-y-1 text-center">
                                    <i class="fas fa-file-invoice-dollar text-gray-200 text-2xl mb-2"></i>
                                    <div class="flex text-xs text-gray-500">
                                        <label for="receipt-upload"
                                            class="relative cursor-pointer bg-white rounded-md font-bold text-red-600 hover:text-red-700 focus-within:outline-none px-2 shadow-sm border border-gray-50">
                                            <span>Upload Receipt</span>
                                            <input id="receipt-upload" name="receipt" type="file" class="sr-only"
                                                accept="image/*,application/pdf">
                                        </label>
                                    </div>
                                    <p class="text-[9px] text-gray-400 uppercase font-bold tracking-widest">Up to 2MB</p>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Payment
                                Status</label>
                            <div
                                class="grid grid-cols-2 gap-2 p-1 bg-gray-50 rounded-2xl border border-gray-100 h-[80px] items-center">
                                <label class="h-full">
                                    <input type="radio" name="status" value="Paid" checked class="hidden peer">
                                    <div
                                        class="h-full flex items-center justify-center rounded-xl text-[10px] font-black uppercase tracking-widest cursor-pointer peer-checked:bg-white peer-checked:text-green-600 peer-checked:shadow-sm transition-all text-gray-400">
                                        Paid
                                    </div>
                                </label>
                                <label class="h-full">
                                    <input type="radio" name="status" value="Pending" class="hidden peer">
                                    <div
                                        class="h-full flex items-center justify-center rounded-xl text-[10px] font-black uppercase tracking-widest cursor-pointer peer-checked:bg-white peer-checked:text-amber-600 peer-checked:shadow-sm transition-all text-gray-400">
                                        Pending
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-center sm:justify-end gap-4 pt-8 border-t border-gray-100">
                    <a href="{{ route('expenses.index') }}"
                        class="px-8 py-4 text-gray-400 font-bold hover:text-gray-600 transition text-xs">Cancel</a>
                    <button type="submit"
                        class="bg-primary text-white px-12 py-4 rounded-xl font-black hover:bg-primary-dark transition shadow-xl shadow-primary/20 uppercase tracking-widest text-xs">
                        Save Expenditure
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection