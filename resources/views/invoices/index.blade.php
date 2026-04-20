@extends('layout')

@section('content')
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-6">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Invoices</h1>
            <p class="text-sm text-gray-500 font-medium">Manage and track your billing operations</p>
        </div>
        <a href="{{ route('invoices.create') }}"
            class="w-full sm:w-auto bg-primary text-white px-6 py-3 rounded-xl font-bold hover:bg-primary-dark transition shadow-lg shadow-primary/20 flex items-center justify-center gap-2 text-sm">
            <i class="fas fa-plus"></i> New Invoice
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto scrolling-touch">
            <table class="w-full text-left text-sm text-gray-600 min-w-[700px] sm:min-w-0 responsive-table">
                <thead
                    class="bg-gray-50 text-gray-900 font-black border-b border-gray-100 uppercase tracking-widest text-[10px]">
                    <tr>
                        <th class="px-6 py-5">Invoice Details</th>
                        <th class="px-6 py-5">Client</th>
                        <th class="px-6 py-5">Amount</th>
                        <th class="px-6 py-5">Status</th>
                        <th class="px-6 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($invoices as $invoice)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td data-label="Invoice Details" class="px-6 py-4">
                                            <div class="font-bold text-gray-900 italic tracking-tight">{{ $invoice->invoice_number }}</div>
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
                                            @php
                                                $badgeStyle = match (strtolower($invoice->status)) {
                                                    'paid' => 'bg-green-50 text-green-600 border-green-100',
                                                    'sent' => 'bg-blue-50 text-blue-600 border-blue-100',
                                                    'overdue' => 'bg-red-50 text-red-600 border-red-100',
                                                    default => 'bg-amber-50 text-amber-600 border-amber-100'
                                                };
                                            @endphp
                        <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest border {{ $badgeStyle }}">
                                                {{ $invoice->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('invoices.show', $invoice->id) }}"
                                                    class="p-2.5 text-gray-400 hover:text-primary transition bg-gray-50 rounded-xl"
                                                    title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('invoices.download', $invoice->id) }}"
                                                    class="p-2.5 text-gray-400 hover:text-blue-600 transition bg-gray-50 rounded-xl"
                                                    title="Download PDF">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                                @if($invoice->status !== 'Paid')
                                                    <form action="{{ route('invoices.updateStatus', $invoice->id) }}" method="POST"
                                                        class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="Paid">
                                                        <button type="submit"
                                                            class="p-2.5 text-gray-400 hover:text-green-600 transition bg-gray-50 rounded-xl"
                                                            title="Mark as Paid">
                                                            <i class="fas fa-check-circle"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                <a href="{{ route('invoices.edit', $invoice->id) }}"
                                                    class="p-2.5 text-gray-400 hover:text-amber-600 transition bg-gray-50 rounded-xl"
                                                    title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center text-gray-400">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-6">
                                        <i class="fas fa-file-invoice text-3xl text-gray-200"></i>
                                    </div>
                                    <p class="font-black text-gray-500 uppercase tracking-widest text-xs">No invoices found</p>
                                    <p class="text-[10px] mt-2 font-medium">Create your first invoice to get started.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection