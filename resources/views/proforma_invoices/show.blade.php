@extends('layout')

@section('content')
    <div class="relative bg-white shadow-lg rounded-lg border-2 border-gray-800 p-4 max-w-4xl mx-auto text-gray-800 font-sans flex flex-col min-h-screen print:min-h-0"
        id="invoice">
    
    @if($invoice->logo)
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-0 opacity-10">
            <img src="{{ Str::startsWith($invoice->logo, 'http') ? $invoice->logo : asset('storage/' . $invoice->logo) }}" 
                 class="w-[500px] object-contain grayscale">
        </div>
    @endif

        <!-- Row 1: Logo & Details -->
        <div class="relative z-10 grid grid-cols-2 gap-2 mb-2 border-b border-gray-100 pb-2 items-start">
            <div class="flex items-start">
                @if($invoice->logo)
                    <img src="{{ Str::startsWith($invoice->logo, 'http') ? $invoice->logo : asset('storage/' . $invoice->logo) }}"
                        class="h-14 object-contain" alt="Business Logo">
                @endif
            </div>
            <div class="text-right">
                <h1 class="text-2xl font-extrabold text-primary tracking-tight mb-1 uppercase">Proforma Invoice</h1>
                <div class="text-sm space-y-1 text-gray-600">
                    <p><span class="font-semibold text-gray-900">Proforma #:</span> {{ $invoice->invoice_number }}</p>
                    <p><span class="font-semibold text-gray-900">Date:</span> {{ $invoice->invoice_date }}</p>
                    @if($invoice->due_date)
                        <p><span class="font-semibold text-gray-900">Expiry Date:</span> {{ $invoice->due_date }}</p>
                    @endif
                    <div class="mt-2 flex justify-end">
                        <span class="inline-block px-3 py-1 rounded border-2 transform -rotate-12 font-bold text-xs shadow-sm uppercase tracking-wider bg-amber-100 text-amber-700 border-amber-700">
                            PROFORMA
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 2: Bill To & Bill From -->
        <div class="relative z-10 grid grid-cols-2 gap-4 mb-2">
            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Bill To</h3>
                <div class="flex items-start gap-4">
                    @if($invoice->client_logo)
                        <img src="{{ asset('storage/' . $invoice->client_logo) }}" class="h-12 w-12 object-contain rounded border border-gray-100 p-1 bg-gray-50 flex-shrink-0">
                    @endif
                    <div>
                        <div class="text-gray-900 font-bold text-base mb-0.5">{{ $invoice->client_name }}</div>
                        <div class="text-gray-500 text-xs whitespace-pre-line leading-snug font-medium">{{ $invoice->client_address }}</div>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Bill From</h3>
                <div class="text-black font-bold text-base mb-1">{{ $invoice->sender_name }}</div>
                <div class="text-gray-600 text-sm whitespace-pre-line leading-relaxed">{{ $invoice->sender_address }}</div>
            </div>
        </div>

        <!-- Row 3: Items Table -->
        <div class="relative z-10 mb-4">
            <table class="w-full text-left border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-300">
                        <th class="py-1 pl-3 text-sm font-bold text-gray-900 uppercase tracking-wider border-r border-gray-300">Description</th>
                        <th class="py-1 pr-3 text-right text-sm font-bold text-gray-900 uppercase tracking-wider w-24 border-r border-gray-300">Qty</th>
                        <th class="py-1 pr-3 text-right text-sm font-bold text-gray-900 uppercase tracking-wider w-32 border-r border-gray-300">Price</th>
                        <th class="py-1 pr-3 text-right text-sm font-bold text-gray-900 uppercase tracking-wider w-32">Amount</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-700">
                    @foreach($invoice->items as $item)
                        <tr class="border-b border-gray-300 hover:bg-gray-50/50">
                            <td class="py-1 pl-3 border-r border-gray-300">{{ $item->description }}</td>
                            <td class="py-1 pr-3 text-right border-r border-gray-300">{{ $item->quantity }}</td>
                            <td class="py-1 pr-3 text-right border-r border-gray-300">{{ number_format($item->unit_price, 2) }}</td>
                            <td class="py-1 pr-3 text-right font-semibold text-gray-900">{{ number_format($item->amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Row 4: Totals -->
        <div class="relative z-10 flex justify-end mb-4">
            <div class="w-full md:w-5/12 space-y-2">
                <div class="flex justify-between py-1 border-b border-gray-100 text-sm">
                    <span class="font-medium text-gray-600">Subtotal</span>
                    <span class="font-bold text-gray-900">Rs. {{ number_format($invoice->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between py-1 text-lg border-t border-gray-900">
                    <span class="font-bold text-gray-900">Total</span>
                    <span class="font-extrabold text-primary">Rs. {{ number_format($invoice->total, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="print:hidden border-t border-gray-200 mt-6 pt-6 flex flex-wrap justify-end gap-4 relative z-20">
            <a href="{{ route('proforma_invoices.index') }}"
                class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition flex items-center gap-2 text-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            
            <form action="{{ route('proforma_invoices.convert', $invoice->id) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-green-600 text-white px-5 py-2.5 rounded-lg font-medium hover:bg-green-700 transition flex items-center gap-2 text-sm shadow-sm">
                    <i class="fas fa-check-double"></i> Accept & Create Invoice
                </button>
            </form>

            <form action="{{ route('proforma_invoices.sendEmail', $invoice->id) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg font-medium hover:bg-indigo-700 transition flex items-center gap-2 text-sm shadow-sm">
                    <i class="fas fa-envelope"></i> Send Proforma
                </button>
            </form>

            <a href="{{ route('invoices.download', $invoice->id) }}"
                class="bg-primary text-white px-5 py-2.5 rounded-lg font-medium hover:bg-primary-dark transition flex items-center gap-2 text-sm shadow-sm">
                <i class="fas fa-file-pdf"></i> Download PDF
            </a>
            
            <button onclick="window.print()"
                class="bg-gray-900 text-white px-5 py-2.5 rounded-lg font-medium hover:bg-gray-800 transition flex items-center gap-2 text-sm shadow-sm">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>
@endsection
