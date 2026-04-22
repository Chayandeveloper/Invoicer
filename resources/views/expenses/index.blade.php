@extends('layout')

@section('content')
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-6">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Expenses</h1>
            <p class="text-sm text-gray-500 font-medium">Manage and track your business expenditures</p>
        </div>
        <a href="{{ route('expenses.create') }}"
            class="w-full sm:w-auto bg-primary text-white px-6 py-3 rounded-xl font-bold hover:bg-primary-dark transition shadow-lg shadow-primary/20 flex items-center justify-center gap-2 text-sm">
            <i class="fas fa-plus"></i> Record Expense
        </a>
    </div>

    <!-- Stats Bar -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-10">
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm transition hover:shadow-md group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-red-50 text-red-600 rounded-xl group-hover:bg-red-600 group-hover:text-white transition-colors">
                    <i class="fas fa-calendar-alt text-lg"></i>
                </div>
                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total (Month)</div>
            </div>
            <div class="text-2xl font-black text-gray-900 truncate tracking-tight">₹{{ number_format($expenses->where('expense_date', '>=', now()->startOfMonth())->sum('amount'), 2) }}</div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm transition hover:shadow-md group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-blue-50 text-blue-600 rounded-xl group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <i class="fas fa-chart-line text-lg"></i>
                </div>
                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total (Year)</div>
            </div>
            <div class="text-2xl font-black text-gray-900 truncate tracking-tight">₹{{ number_format($expenses->where('expense_date', '>=', now()->startOfYear())->sum('amount'), 2) }}</div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm transition hover:shadow-md group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-amber-50 text-amber-600 rounded-xl group-hover:bg-amber-600 group-hover:text-white transition-colors">
                    <i class="fas fa-clock text-lg"></i>
                </div>
                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Unpaid/Pending</div>
            </div>
            <div class="text-2xl font-black text-amber-600 truncate tracking-tight">₹{{ number_format($expenses->where('status', 'Pending')->sum('amount'), 2) }}</div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm transition hover:shadow-md group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                    <i class="fas fa-tags text-lg"></i>
                </div>
                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Top Category</div>
            </div>
            <div class="text-2xl font-black text-indigo-600 truncate tracking-tight">
                {{ $expenses->groupBy('category')->map->sum('amount')->sortDesc()->keys()->first() ?? 'N/A' }}
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm mb-6">
        <form action="{{ route('expenses.index') }}" method="GET" class="space-y-4">
            <div class="flex flex-wrap items-end gap-2">
                <div class="w-full sm:w-auto flex-1 min-w-[140px]">
                    <label
                        class="block text-[10px] font-black text-gray-400 uppercase mb-1 ml-1 tracking-widest">Business</label>
                    <select name="business_id"
                        class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-2.5">
                        <option value="">All Businesses</option>
                        @foreach($businesses as $business)
                            <option value="{{ $business->id }}" {{ request('business_id') == $business->id ? 'selected' : '' }}>
                                {{ $business->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-auto flex-1 min-w-[140px]">
                    <label
                        class="block text-[10px] font-black text-gray-400 uppercase mb-1 ml-1 tracking-widest">Category</label>
                    <select name="category"
                        class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-2.5">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-auto min-w-[100px]">
                    <label
                        class="block text-[10px] font-black text-gray-400 uppercase mb-1 ml-1 tracking-widest">Status</label>
                    <select name="status"
                        class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-2.5">
                        <option value="">All</option>
                        <option value="Paid" {{ request('status') == 'Paid' ? 'selected' : '' }}>Paid</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>
                <div class="w-full sm:w-auto">
                    <label
                        class="block text-[10px] font-black text-gray-400 uppercase mb-1 ml-1 tracking-widest">From</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                        class="w-full sm:w-32 border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-2.5">
                </div>
                <div class="w-full sm:w-auto">
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-1 ml-1 tracking-widest">To</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                        class="w-full sm:w-32 border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-2.5">
                </div>
                <button type="submit"
                    class="bg-gray-900 text-white px-4 py-2.5 rounded-xl font-bold hover:bg-black transition shadow-lg text-xs flex items-center justify-center gap-2">
                    <i class="fas fa-filter"></i>
                </button>
                <a href="{{ route('expenses.index') }}"
                    class="bg-gray-100 text-gray-600 p-2.5 rounded-xl hover:bg-gray-200 transition">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto scrolling-touch">
            <table class="w-full text-left text-sm text-gray-600 min-w-[800px] sm:min-w-0 responsive-table">
                <thead
                    class="bg-gray-50 text-gray-900 font-black border-b border-gray-100 uppercase tracking-widest text-[10px]">
                    <tr>
                        <th class="px-6 py-5">Date / Ref</th>
                        <th class="px-6 py-5">Receipt</th>
                        <th class="px-6 py-5">Business / Category</th>
                        <th class="px-6 py-5">Vendor / Description</th>
                        <th class="px-6 py-5">Amount</th>
                        <th class="px-6 py-5">Payment / Status</th>
                        <th class="px-6 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($expenses as $expense)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td data-label="Date/Ref" class="px-6 py-4">
                                <div class="font-bold text-gray-900 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($expense->expense_date)->format('M d, Y') }}
                                </div>
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    #{{ $expense->reference_number ?? 'NO-REF' }}
                                </div>
                            </td>
                            <td data-label="Receipt" class="px-6 py-4">
                                @if($expense->receipt_path)
                                    @if(Str::endsWith($expense->receipt_path, '.pdf'))
                                        <a href="{{ asset('storage/' . $expense->receipt_path) }}" target="_blank" 
                                           class="w-10 h-10 rounded-lg bg-red-50 border border-red-100 flex items-center justify-center text-red-500 hover:bg-red-500 hover:text-white transition-all">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    @else
                                        <div class="relative group cursor-pointer" onclick="openPreview('{{ asset('storage/' . $expense->receipt_path) }}')">
                                            <img src="{{ asset('storage/' . $expense->receipt_path) }}" 
                                                 class="w-10 h-10 object-cover rounded-lg border border-gray-100 shadow-sm group-hover:scale-110 transition-transform">
                                            <div class="absolute inset-0 bg-black/20 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                <i class="fas fa-search-plus text-white text-[10px]"></i>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-gray-50 border border-dotted border-gray-200 flex items-center justify-center text-gray-300">
                                        <i class="fas fa-receipt text-[10px]"></i>
                                    </div>
                                @endif
                            </td>
                            <td data-label="Business/Category" class="px-6 py-4">
                                <div class="text-xs font-black text-gray-800 tracking-tight">
                                    {{ $expense->business->name ?? 'N/A' }}
                                </div>
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-lg text-[9px] font-black bg-gray-50 text-gray-400 uppercase tracking-tighter mt-1 border border-gray-100">
                                    {{ $expense->category }}
                                </span>
                            </td>
                            <td data-label="Vendor/Desc" class="px-6 py-4">
                                <div class="font-bold text-gray-900">{{ $expense->vendor ?? '-' }}</div>
                                <div class="text-[10px] text-gray-400 line-clamp-1 italic">{{ $expense->description }}</div>
                            </td>
                            <td data-label="Amount" class="px-6 py-4">
                                <div class="font-black text-red-600 text-base">₹{{ number_format($expense->amount, 2) }}</div>
                                @if($expense->tax_amount > 0)
                                    <div class="text-[9px] font-bold text-gray-300 uppercase tracking-tighter">Incl.
                                        ₹{{ number_format($expense->tax_amount, 2) }} Tax</div>
                                @endif
                            </td>
                            <td data-label="Status" class="px-6 py-4">
                                <div class="text-[10px] font-black text-gray-500 mb-1 uppercase tracking-widest">
                                    <i
                                        class="fas fa-credit-card text-[10px] mr-1 text-gray-300"></i>{{ $expense->payment_method }}
                                </div>
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest
                                                            {{ $expense->status === 'Paid' ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-amber-50 text-amber-600 border border-amber-100' }}">
                                    {{ $expense->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    @if($expense->receipt_path)
                                        <a href="{{ asset('storage/' . $expense->receipt_path) }}" target="_blank"
                                            class="p-2.5 text-gray-400 hover:text-primary transition bg-gray-50 rounded-xl border border-transparent hover:border-primary/20"
                                            title="View Receipt">
                                            <i class="fas fa-receipt"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('expenses.show', $expense->id) }}"
                                        class="p-2.5 text-gray-400 hover:text-green-600 transition bg-gray-50 rounded-xl border border-transparent hover:border-green-100"
                                        title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('expenses.edit', $expense->id) }}"
                                        class="p-2.5 text-gray-400 hover:text-blue-600 transition bg-gray-50 rounded-xl border border-transparent hover:border-blue-100"
                                        title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-2.5 text-gray-400 hover:text-red-600 transition bg-gray-50 rounded-xl border border-transparent hover:border-red-100"
                                            title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-20 text-center text-gray-400">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-6">
                                        <i class="fas fa-receipt text-3xl text-gray-200"></i>
                                    </div>
                                    <p class="font-black text-gray-500 uppercase tracking-widest text-xs">No expenses recorded
                                        yet</p>
                                    <p class="text-[10px] mt-2 font-medium">Start tracking your business spendings today.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
    <div id="preview-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm transition-opacity">
        <button onclick="closePreview()" class="absolute top-6 right-6 text-white text-3xl hover:text-red-400 transition">
            <i class="fas fa-times"></i>
        </button>
        <img id="modal-img" src="" class="max-w-full max-h-[90vh] rounded-2xl shadow-2xl object-contain">
    </div>

    <script>
        function openPreview(src) {
            const modal = document.getElementById('preview-modal');
            const img = document.getElementById('modal-img');
            img.src = src;
            modal.classList.remove('hidden');
            setTimeout(() => modal.classList.add('opacity-100'), 10);
        }

        function closePreview() {
            const modal = document.getElementById('preview-modal');
            modal.classList.add('opacity-0');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }
    </script>
@endsection