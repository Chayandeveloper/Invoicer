@extends('layout')

@section('content')
    <div class="relative bg-white shadow-lg rounded-lg border border-gray-200 p-8 max-w-4xl mx-auto text-gray-800 font-sans flex flex-col min-h-[29.7cm]"
        id="quotation">
    
    @if($quotation->sender_logo)
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-0 opacity-10">
            <img src="{{ Str::startsWith($quotation->sender_logo, 'http') ? $quotation->sender_logo : asset('storage/' . $quotation->sender_logo) }}" 
                 class="w-[500px] object-contain grayscale">
        </div>
    @endif
        
        <!-- Row 1: Logo & Header -->
        <div class="relative z-10 grid grid-cols-2 gap-2 mb-8 border-b border-gray-100 pb-6 items-start">
            <div class="flex items-start">
                @if($quotation->sender_logo)
                    <img src="{{ Str::startsWith($quotation->sender_logo, 'http') ? $quotation->sender_logo : asset('storage/' . $quotation->sender_logo) }}"
                        class="h-20 object-contain" alt="Business Logo">
                @endif
            </div>
            <div class="text-right">
                <h1 class="text-3xl font-extrabold text-primary tracking-tight mb-2 uppercase">QUOTATION</h1>
                <div class="text-sm space-y-1 text-gray-600">
                    <p><span class="font-semibold text-gray-900">Quotation #:</span> {{ $quotation->quotation_number }}</p>
                    <p><span class="font-semibold text-gray-900">Date:</span> {{ \Carbon\Carbon::parse($quotation->quotation_date)->format('M d, Y') }}</p>
                    @if($quotation->expiry_date)
                        <p><span class="font-semibold text-gray-900">Valid Until:</span> {{ \Carbon\Carbon::parse($quotation->expiry_date)->format('M d, Y') }}</p>
                    @endif
                    <div class="mt-2 flex justify-end">
                         <span class="inline-block px-3 py-1 rounded border-2 transform -rotate-12 font-bold text-xs uppercase tracking-wider
                                {{ $quotation->status === 'Accepted' || $quotation->status === 'Invoiced' ? 'bg-green-100 text-green-700 border-green-700' : 'bg-yellow-100 text-yellow-700 border-yellow-700' }}">
                            {{ strtoupper($quotation->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 2: Bill To & Bill From -->
        <div class="relative z-10 grid grid-cols-2 gap-12 mb-10">
            <!-- Bill To (Left) -->
            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Quote For</h3>
                <div class="flex items-start gap-4">
                    @if($quotation->client_logo)
                        <img src="{{ asset('storage/' . $quotation->client_logo) }}" class="h-12 w-12 object-contain rounded border border-gray-100 p-1 bg-gray-50 flex-shrink-0" alt="Client Logo">
                    @endif
                    <div>
                        <div class="text-gray-900 font-bold text-base mb-1">{{ $quotation->client_name }}</div>
                        <div class="text-gray-600 text-sm whitespace-pre-line leading-relaxed">{{ $quotation->client_address }}</div>
                        @if($quotation->client_phone)
                            <div class="text-gray-500 text-xs mt-1 font-medium"><i class="fas fa-phone-alt text-[10px] mr-1"></i> {{ $quotation->client_phone }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Bill From (Right) -->
            <div class="text-right">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">From</h3>
                <div class="text-gray-900 font-bold text-base mb-1">{{ $quotation->sender_name }}</div>
                <div class="text-gray-600 text-sm whitespace-pre-line leading-relaxed">{{ $quotation->sender_address }}</div>
                @if($quotation->sender_phone)
                    <div class="text-gray-600 text-xs mt-1 font-bold"><i class="fas fa-phone-alt text-[10px] mr-1"></i> {{ $quotation->sender_phone }}</div>
                @endif
                @if($quotation->sender_website)
                    <a href="{{ $quotation->sender_website }}" target="_blank"
                        class="text-primary text-sm mt-1 hover:underline block">{{ $quotation->sender_website }}</a>
                @endif
            </div>
        </div>

        <!-- Row 3: Items Table -->
        <div class="relative z-10 mb-8 overflow-hidden rounded-lg border border-gray-200">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-900 uppercase tracking-wider text-xs font-semibold">
                        <th class="py-3 px-4">Description</th>
                        <th class="py-3 px-4 text-right w-24">Qty</th>
                        <th class="py-3 px-4 text-right w-32">Price</th>
                        <th class="py-3 px-4 text-right w-24">Tax</th>
                        <th class="py-3 px-4 text-right w-32">Amount</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-700">
                    @php $totalItemTax = 0; @endphp
                    @foreach($quotation->items as $item)
                        @php 
                            $itemTax = $item->amount * (($item->tax_rate ?? 0) / 100); 
                            $totalItemTax += $itemTax;
                        @endphp
                        <tr class="border-b border-gray-100 hover:bg-gray-50/50">
                            <td class="py-3 px-4 font-medium">{{ $item->description }}</td>
                            <td class="py-3 px-4 text-right">{{ $item->quantity }}</td>
                            <td class="py-3 px-4 text-right">Rs. {{ number_format($item->unit_price, 2) }}</td>
                            <td class="py-3 px-4 text-right">{{ $item->tax_rate > 0 ? $item->tax_rate . '%' : '-' }}</td>
                            <td class="py-3 px-4 text-right font-bold text-gray-900">Rs. {{ number_format($item->amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Row 4: Totals -->
        <div class="relative z-10 flex justify-end mb-12">
            <div class="w-full md:w-5/12 space-y-2">
                <div class="flex justify-between py-1 border-b border-gray-100 text-sm">
                    <span class="font-medium text-gray-600">Subtotal</span>
                    <span class="font-bold text-gray-900">Rs. {{ number_format($quotation->subtotal, 2) }}</span>
                </div>
                @if($totalItemTax > 0)
                <div class="flex justify-between py-1 border-b border-gray-100 text-sm">
                    <span class="font-medium text-gray-600">Item Tax Total</span>
                    <span class="font-bold text-gray-900">Rs. {{ number_format($totalItemTax, 2) }}</span>
                </div>
                @endif
                @if($quotation->tax_rate > 0)
                <div class="flex justify-between py-1 border-b border-gray-100 text-sm">
                    <span class="font-medium text-gray-600">Global Tax ({{ $quotation->tax_rate }}%)</span>
                    <span class="font-bold text-gray-900">Rs. {{ number_format($quotation->subtotal * ($quotation->tax_rate / 100), 2) }}</span>
                </div>
                @endif
                <div class="flex justify-between py-2 text-xl border-t-2 border-gray-900">
                    <span class="font-bold text-gray-900">Total Estimate</span>
                    <span class="font-extrabold text-primary">Rs. {{ number_format($quotation->total, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Row 5: Notes & QR -->
        <div class="relative z-10 mt-auto pt-8 border-t border-gray-100 grid grid-cols-2 gap-8">
            <div>
                <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider mb-2">Terms & Notes</h3>
                <p class="text-xs text-gray-600 leading-relaxed italic">
                    1. This quotation is valid until {{ \Carbon\Carbon::parse($quotation->expiry_date ?? '+30 days')->format('M d, Y') }}.<br>
                    2. Conversion to an invoice will be based on these agreed prices.
                </p>
                @if($quotation->bank_details)
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg text-xs text-gray-600 border border-gray-100">
                        <span class="font-bold">Banking Info:</span> {{ $quotation->bank_details }}
                    </div>
                @endif
            </div>

            @if($quotation->payment_qr_link)
                <div class="flex flex-col items-end justify-center">
                    <div id="qrcode" class="p-2 bg-white border border-gray-100 rounded inline-block"></div>
                    <p class="mt-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Pre-payment Option</p>
                </div>
            @endif
        </div>

        <!-- Actions -->
        <div class="print:hidden border-t border-gray-200 mt-10 pt-8 flex justify-end gap-3" data-html2canvas-ignore="true">
            <a href="{{ route('quotations.index') }}"
                class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition flex items-center gap-2 text-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="{{ route('quotations.edit', $quotation->id) }}"
                class="px-5 py-2.5 bg-gray-100 border border-gray-200 rounded-lg text-gray-700 font-medium hover:bg-gray-200 transition flex items-center gap-2 text-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
            
            @if($quotation->status === 'Pending')
                <form action="{{ route('quotations.updateStatus', $quotation->id) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="Accepted">
                    <button type="submit" class="bg-green-600 text-white px-5 py-2.5 rounded-lg font-medium hover:bg-green-700 transition flex items-center gap-2 text-sm shadow-sm">
                        <i class="fas fa-check-circle"></i> Mark as Accepted
                    </button>
                </form>
            @endif

            @if($quotation->status === 'Accepted')
                <form action="{{ route('quotations.convert', $quotation->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg font-medium hover:bg-indigo-700 transition flex items-center gap-2 text-sm shadow-sm">
                        <i class="fas fa-file-invoice-dollar"></i> Convert to Invoice
                    </button>
                </form>
            @endif

            <a href="{{ route('quotations.download', $quotation->id) }}"
                class="bg-primary text-white px-5 py-2.5 rounded-lg font-medium hover:bg-primary-dark transition flex items-center gap-2 text-sm shadow-sm">
                <i class="fas fa-file-pdf"></i> Download PDF
            </a>
            
            <button onclick="window.print()"
                class="bg-gray-900 text-white px-5 py-2.5 rounded-lg font-medium hover:bg-gray-800 transition flex items-center gap-2 text-sm shadow-sm">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if($quotation->payment_qr_link)
                new QRCode(document.getElementById("qrcode"), {
                    text: "{{ $quotation->payment_qr_link }}",
                    width: 100,
                    height: 100,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });
            @endif
        });
    </script>

    <style>
        @media print {
            @page { size: A4; margin: 0mm; }
            body * { visibility: hidden; }
            #quotation, #quotation * { visibility: visible; }
            #quotation {
                position: absolute; top: 0; left: 0; width: 210mm; min-height: 297mm;
                margin: 0; padding: 10mm; border: 1px solid #0C8D5D !important; box-sizing: border-box;
                z-index: 9999; background-color: white; box-shadow: none; border-radius: 0;
            }
            .print\:hidden { display: none !important; }
        }
    </style>
@endsection
