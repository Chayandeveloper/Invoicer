@extends('layout')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
        <div class="flex-grow">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Proforma Invoices</h1>
            @if(isset($filteredClient) && $filteredClient)
                <div class="flex items-center gap-2 mt-1">
                    <span class="bg-indigo-50 text-indigo-600 px-3 py-1 rounded-lg text-xs font-bold border border-indigo-100 flex items-center gap-2 italic">
                        <i class="fas fa-filter text-[10px]"></i>
                        Filtered for: <span class="font-black text-gray-900">{{ $filteredClient->name }}</span>
                    </span>
                    <a href="{{ route('proforma_invoices.index') }}" class="text-[10px] font-black text-gray-400 hover:text-rose-500 uppercase tracking-widest transition-colors flex items-center gap-1">
                        <i class="fas fa-times-circle"></i> Clear
                    </a>
                </div>
            @else
                <p class="text-sm text-gray-500 font-medium">Manage your pending proforma billing documents</p>
            @endif
        </div>
        
        <div class="flex flex-col sm:flex-row items-center gap-4 w-full md:w-auto">
            <!-- Search Bar -->
            <div class="relative w-full sm:w-72">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-xs transition-colors group-focus-within:text-primary"></i>
                <input type="text" id="invoice-search" placeholder="Search Proforma # or Client..." 
                    class="w-full bg-white border-gray-100 pl-10 pr-4 py-3 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary shadow-sm transition-all placeholder:text-gray-300">
            </div>

            <a href="{{ route('proforma_invoices.create') }}"
                class="w-full sm:w-auto bg-primary text-white px-6 py-3 rounded-xl font-bold hover:bg-primary-dark transition shadow-lg shadow-primary/20 flex items-center justify-center gap-2 text-sm whitespace-nowrap">
                <i class="fas fa-plus"></i> New Proforma
            </a>
        </div>
    </div>

    <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto scrolling-touch">
            <table class="w-full text-left text-sm text-gray-600 min-w-[700px] sm:min-w-0 responsive-table">
                <thead
                    class="bg-gray-50 text-gray-900 font-black border-b border-gray-100 uppercase tracking-widest text-[10px]">
                    <tr>
                        <th class="px-6 py-5">Proforma Details</th>
                        <th class="px-6 py-5">Client</th>
                        <th class="px-6 py-5">Amount</th>
                        <th class="px-6 py-5">Status</th>
                        <th class="px-6 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td data-label="Proforma Details" class="px-6 py-4">
                                <div class="font-bold text-gray-900 italic tracking-tight invoice-num">{{ $invoice->invoice_number }}</div>
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }}</div>
                            </td>
                            <td data-label="Client Name" class="px-6 py-4">
                                <div class="font-black text-gray-800 tracking-tight">{{ $invoice->client_name }}</div>
                            </td>
                            <td data-label="Total Amount" class="px-6 py-4">
                                <div class="font-black text-gray-900 text-base">Rs. {{ number_format($invoice->total, 2) }}</div>
                            </td>
                            <td data-label="Status" class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest border bg-amber-50 text-amber-600 border-amber-100">
                                    {{ $invoice->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('proforma_invoices.show', $invoice->id) }}"
                                        class="p-2.5 text-gray-400 hover:text-primary transition bg-gray-50 rounded-xl"
                                        title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('invoices.download', $invoice->id) }}"
                                        class="p-2.5 text-gray-400 hover:text-blue-600 transition bg-gray-50 rounded-xl"
                                        title="Download PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    
                                    <form action="{{ route('proforma_invoices.convert', $invoice->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                            class="p-2.5 text-gray-400 hover:text-green-600 transition bg-gray-50 rounded-xl"
                                            title="Accept & Convert to Regular Invoice">
                                            <i class="fas fa-check-double"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route('proforma_invoices.destroy', $invoice->id) }}" method="POST"
                                        class="inline" onsubmit="return confirm('Are you sure you want to delete this proforma invoice?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-2.5 text-gray-400 hover:text-red-600 transition bg-gray-50 rounded-xl"
                                            title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="empty-row">
                            <td colspan="5" class="px-6 py-20 text-center text-gray-400">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-6">
                                        <i class="fas fa-file-invoice-dollar text-3xl text-gray-200"></i>
                                    </div>
                                    <p class="font-black text-gray-500 uppercase tracking-widest text-xs">No proforma invoices found</p>
                                    <p class="text-[10px] mt-2 font-medium">Create your first proforma invoice to get started.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('invoice-search');
        const tableRows = document.querySelectorAll('tbody tr:not(.empty-row)');

        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            tableRows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    });
</script>
@endpush
