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

        <!-- Row 1: Logo & Invoice Details -->
        <div class="relative z-10 grid grid-cols-2 gap-2 mb-2 border-b border-gray-100 pb-2 items-start">
            <div class="flex items-start">
                @if($invoice->logo)
                    <img src="{{ Str::startsWith($invoice->logo, 'http') ? $invoice->logo : asset('storage/' . $invoice->logo) }}"
                        class="h-14 object-contain" alt="Business Logo">
                @endif
            </div>
            <div class="text-right">
                <h1 class="text-2xl font-extrabold text-primary tracking-tight mb-1">INVOICE</h1>
                <div class="text-sm space-y-1 text-gray-600">
                    <p><span class="font-semibold text-gray-900">Invoice #:</span> {{ $invoice->invoice_number }}</p>
                    <p><span class="font-semibold text-gray-900">Date:</span> {{ $invoice->invoice_date }}</p>
                    @if($invoice->due_date)
                        <p><span class="font-semibold text-gray-900">Due Date:</span> {{ $invoice->due_date }}</p>
                    @endif
                    <div class="mt-2 flex justify-end">
                        <span
                            class="inline-block px-3 py-1 rounded border-2 transform -rotate-12 font-bold text-xs shadow-sm uppercase tracking-wider
                                {{ $invoice->status === 'Paid' ? 'bg-green-100 text-green-700 border-green-700' : ($invoice->status === 'Draft' ? 'bg-gray-100 text-gray-600 border-gray-600' : 'bg-red-100 text-red-700 border-red-700') }}">
                            {{ strtoupper($invoice->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 2: Bill To & Bill From -->
        <div class="relative z-10 grid grid-cols-2 gap-4 mb-2">
            <!-- Bill To (Left) -->
            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Bill To</h3>
                <div class="flex items-start gap-4">
                    @if($invoice->client_logo)
                        <img src="{{ asset('storage/' . $invoice->client_logo) }}" class="h-12 w-12 object-contain rounded border border-gray-100 p-1 bg-gray-50 flex-shrink-0" alt="Client Logo">
                    @endif
                    <div>
                        <div class="text-gray-900 font-bold text-base mb-0.5">{{ $invoice->client_name }}</div>
                        <div class="text-gray-500 text-xs whitespace-pre-line leading-snug font-medium">{{ $invoice->client_address }}</div>
                        @if($invoice->client_phone)
                            <div class="text-gray-500 text-sm mt-1 font-medium"><i class="fas fa-phone-alt text-xs mr-1"></i> {{ $invoice->client_phone }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Bill From (Right) -->
            <div class="text-right">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Bill From</h3>
                <!-- Business Name Black -->
                <div class="text-black font-bold text-base mb-1">{{ $invoice->sender_name }}</div>
                <div class="text-gray-600 text-sm whitespace-pre-line leading-relaxed">{{ $invoice->sender_address }}</div>
                @if($invoice->sender_phone)
                    <div class="text-gray-600 text-sm mt-1 uppercase font-bold tracking-tighter"><i class="fas fa-phone-alt text-xs mr-1"></i> {{ $invoice->sender_phone }}</div>
                @endif
                @if($invoice->sender_website)
                    <a href="{{ $invoice->sender_website }}" target="_blank"
                        class="text-primary text-sm mt-1 hover:underline block">{{ $invoice->sender_website }}</a>
                @endif
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
                        <th class="py-1 pr-3 text-right text-sm font-bold text-gray-900 uppercase tracking-wider w-24 border-r border-gray-300">Tax (%)</th>
                        <th class="py-1 pr-3 text-right text-sm font-bold text-gray-900 uppercase tracking-wider w-32">Amount</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-700">
                    @php $totalItemTax = 0; @endphp
                    @foreach($invoice->items as $item)
                        @php 
                            $itemTax = $item->amount * ($item->tax_rate / 100); 
                            $totalItemTax += $itemTax;
                        @endphp
                        <tr class="border-b border-gray-300 hover:bg-gray-50/50">
                            <td class="py-1 pl-3 border-r border-gray-300">{{ $item->description }}</td>
                            <td class="py-1 pr-3 text-right border-r border-gray-300">{{ $item->quantity }}</td>
                            <td class="py-1 pr-3 text-right border-r border-gray-300">{{ number_format($item->unit_price, 2) }}</td>
                            <td class="py-1 pr-3 text-right border-r border-gray-300">{{ $item->tax_rate > 0 ? $item->tax_rate . '%' : '-' }}</td>
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
                @if($totalItemTax > 0)
                <div class="flex justify-between py-1 border-b border-gray-100 text-sm">
                    <span class="font-medium text-gray-600">Item Tax</span>
                    <span class="font-bold text-gray-900">Rs. {{ number_format($totalItemTax, 2) }}</span>
                </div>
                @endif
                @if($invoice->tax_rate > 0)
                <div class="flex justify-between py-1 border-b border-gray-100 text-sm">
                    <span class="font-medium text-gray-600">Global Tax ({{ $invoice->tax_rate }}%)</span>
                    <span class="font-bold text-gray-900">Rs. {{ number_format($invoice->subtotal * ($invoice->tax_rate / 100), 2) }}</span>
                </div>
                @endif
                <div class="flex justify-between py-1 text-lg border-t border-gray-900">
                    <span class="font-bold text-gray-900">Total</span>
                    <span class="font-extrabold text-primary">Rs. {{ number_format($invoice->total, 2) }}</span>
                </div>

                @if($invoice->status !== 'Draft' && $invoice->paid_amount > 0)
                <div class="mt-4 pt-4 border-t-2 border-dashed border-gray-100 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="font-medium text-gray-500 italic uppercase tracking-tighter text-[10px]">Total Paid to Date</span>
                        <span class="font-bold text-green-600">Rs. {{ number_format($invoice->paid_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="font-bold text-gray-900 uppercase tracking-tighter text-[10px]">Remaining Balance</span>
                        <span class="font-black {{ $invoice->remaining_balance > 0 ? 'text-primary' : 'text-gray-400' }}">
                            Rs. {{ number_format($invoice->remaining_balance, 2) }}
                        </span>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Amount in Words -->
        @php
            if (!function_exists('numberToWords')) {
                function numberToWords($number) {
                    $hyphen      = '-';
                    $conjunction = ' and ';
                    $separator   = ', ';
                    $negative    = 'negative ';
                    $decimal     = ' point ';
                    $dictionary  = array(
                        0                   => 'zero',
                        1                   => 'one',
                        2                   => 'two',
                        3                   => 'three',
                        4                   => 'four',
                        5                   => 'five',
                        6                   => 'six',
                        7                   => 'seven',
                        8                   => 'eight',
                        9                   => 'nine',
                        10                  => 'ten',
                        11                  => 'eleven',
                        12                  => 'twelve',
                        13                  => 'thirteen',
                        14                  => 'fourteen',
                        15                  => 'fifteen',
                        16                  => 'sixteen',
                        17                  => 'seventeen',
                        18                  => 'eighteen',
                        19                  => 'nineteen',
                        20                  => 'twenty',
                        30                  => 'thirty',
                        40                  => 'fourty',
                        50                  => 'fifty',
                        60                  => 'sixty',
                        70                  => 'seventy',
                        80                  => 'eighty',
                        90                  => 'ninety',
                        100                 => 'hundred',
                        1000                => 'thousand',
                        100000              => 'lakh',
                        10000000            => 'crore'
                    );
                    
                    if (!is_numeric($number)) return false;
                    if ($number < 0) return $negative . numberToWords(abs($number));
                    
                    $string = $fraction = null;
                    if (strpos($number, '.') !== false) {
                        list($number, $fraction) = explode('.', $number);
                    }
                    
                    switch (true) {
                        case $number < 21:
                            $string = $dictionary[$number];
                            break;
                        case $number < 100:
                            $tens   = ((int) ($number / 10)) * 10;
                            $units  = $number % 10;
                            $string = $dictionary[$tens];
                            if ($units) $string .= $hyphen . $dictionary[$units];
                            break;
                        case $number < 1000:
                            $hundreds  = $number / 100;
                            $remainder = $number % 100;
                            $string = $dictionary[(int)$hundreds] . ' ' . $dictionary[100];
                            if ($remainder) $string .= $conjunction . numberToWords($remainder);
                            break;
                        case $number < 100000:
                            $thousands = $number / 1000;
                            $remainder = $number % 1000;
                            $string = numberToWords((int)$thousands) . ' ' . $dictionary[1000];
                            if ($remainder) $string .= $separator . numberToWords($remainder);
                            break;
                        case $number < 10000000:
                            $lakhs = $number / 100000;
                            $remainder = $number % 100000;
                            $string = numberToWords((int)$lakhs) . ' ' . $dictionary[100000];
                            if ($remainder) $string .= $separator . numberToWords($remainder);
                            break;
                        default:
                            $crores = $number / 10000000;
                            $remainder = $number % 10000000;
                            $string = numberToWords((int)$crores) . ' ' . $dictionary[10000000];
                            if ($remainder) $string .= $separator . numberToWords($remainder);
                            break;
                    }
                    return $string;
                }
            }
            $amountInWords = numberToWords($invoice->total);
        @endphp
        <div class="relative z-10 mb-2 border-t border-gray-100 pt-2">
            <h3 class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Amount in Words</h3>
            <div class="text-gray-700 text-[10px] font-bold uppercase italic">
                {{ $amountInWords }} Rupees Only
            </div>
        </div>

        <!-- Row 5: Bank Details & QR -->
        <div class="relative z-10 grid grid-cols-2 gap-8">
            <!-- Bank Details -->
            <div>
                @if($invoice->bank_details)
                    <h3 class="text-[10px] font-bold text-gray-900 uppercase tracking-wider mb-1">Bank Details</h3>
                    <div
                        class="rounded-lg p-2 border border-gray-100 text-[11px] text-gray-600 whitespace-pre-line leading-tight">
                        {{ $invoice->bank_details }}
                    </div>
                @endif
            </div>

            <!-- QR Code -->
            <div class="flex flex-col items-end justify-end">
                @if($invoice->payment_qr_image)
                    <div class="text-center">
                        <div class="mb-1 bg-white p-1 border border-gray-100 rounded inline-block">
                             <img src="{{ asset('storage/' . $invoice->payment_qr_image) }}" alt="Payment QR" class="w-24 h-24 object-contain">
                        </div>
                        <p class="text-[10px] text-gray-500 uppercase tracking-wide font-medium">Scan to Pay</p>
                    </div>
                @elseif($invoice->payment_qr_link)
                    <div class="text-center">
                        <div id="qrcode" class="mb-1 bg-white p-1 border border-gray-100 rounded inline-block"></div>
                        <p class="text-[10px] text-gray-500 uppercase tracking-wide font-medium">Scan to Pay</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Footer / Thank you note -->
        <div class="relative z-10 mt-auto pt-4 border-t border-gray-100 text-center text-gray-500 text-sm">
            @if($invoice->footer_logo)
                <div class="mb-4 flex justify-center">
                    <img src="{{ asset('storage/' . $invoice->footer_logo) }}" class="h-16 object-contain" alt="Footer Logo">
                </div>
            @endif
            <p>Thank you for your business!</p>
        </div>

        <!-- Actions -->
        <div class="print:hidden border-t border-gray-200 mt-6 pt-6 flex justify-end gap-4 relative z-20" data-html2canvas-ignore="true">
            <a href="{{ route('invoices.index') }}"
                class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition flex items-center gap-2 text-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="{{ route('invoices.edit', $invoice->id) }}"
                class="px-5 py-2.5 bg-gray-100 border border-gray-200 rounded-lg text-gray-700 font-medium hover:bg-gray-200 transition flex items-center gap-2 text-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
            @if($invoice->status !== 'Paid')
                <form action="{{ route('invoices.updateStatus', $invoice->id) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="Paid">
                    <button type="submit" class="bg-green-600 text-white px-5 py-2.5 rounded-lg font-medium hover:bg-green-700 transition flex items-center gap-2 text-sm shadow-sm">
                        <i class="fas fa-check-circle"></i> Mark as Paid
                    </button>
                </form>
            @endif
            @if($invoice->status === 'Pending')
                <form action="{{ route('invoices.updateStatus', $invoice->id) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="Sent">
                    <button type="submit" class="bg-blue-600 text-white px-5 py-2.5 rounded-lg font-medium hover:bg-blue-700 transition flex items-center gap-2 text-sm shadow-sm">
                        <i class="fas fa-paper-plane"></i> Mark as Sent
                    </button>
                </form>
            @endif
            <form action="{{ route('invoices.sendEmail', $invoice->id) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg font-medium hover:bg-indigo-700 transition flex items-center gap-2 text-sm shadow-sm">
                    <i class="fas fa-envelope"></i> Send via Email
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

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if($invoice->payment_qr_link)
                new QRCode(document.getElementById("qrcode"), {
                    text: "{{ $invoice->payment_qr_link }}",
                    width: 100,
                    height: 100,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });
            @endif
            });

        function downloadPDF() {
            const element = document.getElementById('invoice');
            // Cloning specific for PDF to remove print-only limits if any, 
            // but commonly we just use the element.
            // Using html2canvas settings to improve quality
            const opt = {
                margin: [5, 5], // top, left, bottom, right - Reduced margin for PDF
                filename: 'Invoice-{{ $invoice->invoice_number }}.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 4, useCORS: true, letterRendering: true },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            html2pdf().set(opt).from(element).save();
        }
    </script>

    <style>
        @media print {
            @page {
                size: A4;
                margin: 10mm;
            }

            html,
            body {
                width: 100%;
                height: auto;
                margin: 0 !important;
                padding: 0 !important;
                background: white;
            }

            body * {
                visibility: hidden;
            }

            #invoice,
            #invoice * {
                visibility: visible;
            }

            #invoice {
                position: relative;
                top: 0;
                left: 0;
                width: 100%;
                min-height: auto;
                margin: 0;
                padding: 5mm;
                border: 1px solid #6932BB !important;
                box-sizing: border-box;
                z-index: 9999;
                background-color: white;
                box-shadow: none;
                border-radius: 0;
            }

            .print\:hidden {
                display: none !important;
            }
        }
    </style>
@endsection