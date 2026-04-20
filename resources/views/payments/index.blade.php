@extends('layout')

@section('content')
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-6">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Payments</h1>
            <p class="text-sm text-gray-500 font-medium">Track your income and payment receipts</p>
        </div>
        <a href="{{ route('payments.create') }}"
            class="w-full sm:w-auto bg-primary text-white px-6 py-3 rounded-xl font-bold hover:bg-primary-dark transition shadow-lg shadow-primary/20 flex items-center justify-center gap-2 text-sm">
            <i class="fas fa-plus"></i> Record Payment
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto scrolling-touch">
            <table class="w-full text-left text-sm text-gray-600 min-w-[800px]">
                <thead
                    class="bg-gray-50 text-gray-900 font-black border-b border-gray-100 uppercase tracking-widest text-[10px]">
                    <tr>
                        <th class="px-6 py-5">Receipt Details</th>
                        <th class="px-6 py-5">Date</th>
                        <th class="px-6 py-5">Method</th>
                        <th class="px-6 py-5">Invoice</th>
                        <th class="px-6 py-5">Amount</th>
                        <th class="px-6 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 italic tracking-tight">#{{ $payment->receipt_number }}</div>
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    {{ $payment->reference_number ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold text-gray-900">
                                    {{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-lg text-[9px] font-black bg-indigo-50 text-indigo-600 border border-indigo-100 uppercase tracking-widest">
                                    {{ $payment->payment_method }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($payment->invoice)
                                    <a href="{{ route('invoices.show', $payment->invoice_id) }}"
                                        class="text-primary hover:text-primary-dark font-black text-xs tracking-tight">
                                        {{ $payment->invoice->invoice_number }}
                                    </a>
                                @else
                                    <span class="text-gray-300 italic text-[10px] uppercase font-bold tracking-widest">No
                                        link</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-black text-gray-900 text-base">Rs. {{ number_format($payment->amount, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('payments.show', $payment->id) }}"
                                        class="p-2.5 text-gray-400 hover:text-primary transition bg-gray-50 rounded-xl"
                                        title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('payments.download', $payment->id) }}"
                                        class="p-2.5 text-gray-400 hover:text-red-600 transition bg-gray-50 rounded-xl"
                                        title="Download PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    <form action="{{ route('payments.destroy', $payment->id) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Delete this record?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-2.5 text-gray-400 hover:text-gray-900 transition bg-gray-50 rounded-xl"
                                            title="Delete">
                                            <i class="fas fa-trash-alt"></i>
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
                                    <p class="font-black text-gray-500 uppercase tracking-widest text-xs">No payments recorded
                                        yet</p>
                                    <p class="text-[10px] mt-2 font-medium">Record your first payment to track income.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection