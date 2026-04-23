<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        @page {
            margin: 0px;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 14px;
            margin: 50px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: bold;
        }

        .text-sm {
            font-size: 12px;
        }

        .text-xs {
            font-size: 10px;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .text-gray {
            color: #6b7280;
        }

        .text-primary {
            color: #0C8D5D;
        }

        .bg-gray-100 {
            background-color: #f3f4f6;
        }

        .border-b {
            border-bottom: 1px solid #e5e7eb;
        }

        .py-2 {
            padding-top: 8px;
            padding-bottom: 8px;
        }

        .px-2 {
            padding-left: 8px;
            padding-right: 8px;
        }

        /* Header */
        .logo {
            max-height: 80px;
            max-width: 200px;
        }

        .invoice-title {
            font-size: 32px;
            font-weight: 800;
            color: #0C8D5D;
            margin-bottom: 5px;
            letter-spacing: -1px;
        }

        /* Badge */
        .badge {
            padding: 4px 12px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            border: 1px solid;
            display: inline-block;
            margin-top: 5px;
        }

        .badge-paid {
            background-color: #d1fae5;
            color: #065f46;
            border-color: #065f46;
        }

        /* Green-100/700 */
        .badge-pending {
            background-color: #fef2f2;
            color: #b91c1c;
            border-color: #b91c1c;
        }

        /* Red-100/700 */

        /* Address Box */
        .address-box {
            margin-bottom: 30px;
            vertical-align: top;
        }

        .address-title {
            font-size: 10px;
            font-weight: bold;
            color: #9ca3af;
            text-transform: uppercase;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }

        /* Items Table */
        .items-header {
            background-color: #f3f4f6;
            border-bottom: 1px solid #d1d5db;
        }

        .items-header th {
            padding: 8px 12px;
            text-align: left;
            font-size: 12px;
            font-weight: bold;
            color: #111827;
            text-transform: uppercase;
            border-right: 1px solid #e5e7eb;
        }

        .items-header th:last-child {
            border-right: none;
        }

        .items-row td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
            color: #374151;
            border-right: 1px solid #e5e7eb;
        }

        .items-row td:last-child {
            border-right: none;
        }

        /* Totals */
        .totals-table td {
            padding: 4px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .total-row td {
            border-top: 2px solid #111827;
            padding-top: 10px;
            font-size: 18px;
            font-weight: bold;
            color: #0C8D5D;
            border-bottom: none;
        }

        /* Bank & QR */
        .bank-box {
            /* background-color: #f9fafb; Removed per user request */
            border: 1px solid #e5e7eb;
            padding: 10px;
            border-radius: 6px;
            font-size: 12px;
            line-height: 1.5;
            color: #4b5563;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 12px;
            background: white;
        }
        /* Watermark */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 500px;
            opacity: 0.15; /* Increased slightly to show color better */
            z-index: -1000;
            text-align: center;
        }
        .watermark img {
            width: 100%;
            height: auto;
            object-fit: contain;
        }
    </style>
</head>

<body>
    @if($invoice->logo)
        <div class="watermark">
            <img src="{{ Str::startsWith($invoice->logo, 'http') ? $invoice->logo : public_path('storage/' . $invoice->logo) }}">
        </div>
    @endif
    <!-- Header -->
    <table style="margin-bottom: 20px; border-bottom: 1px solid #f3f4f6; padding-bottom: 10px;">
        <tr>
            <td valign="top">
                @if($invoice->logo)
                    <img src="{{ Str::startsWith($invoice->logo, 'http') ? $invoice->logo : public_path('storage/' . $invoice->logo) }}"
                        class="logo">
                @endif
            </td>
            <td valign="top" class="text-right">
                <div class="invoice-title">INVOICE</div>
                <div style="line-height: 1.6; color: #4b5563; font-size: 12px;">
                    <p style="margin: 0;"><span style="font-weight: bold; color: #111827;">Invoice #:</span>
                        {{ $invoice->invoice_number }}</p>
                    <p style="margin: 0;"><span style="font-weight: bold; color: #111827;">Date:</span>
                        {{ $invoice->invoice_date }}</p>
                    @if($invoice->due_date)
                        <p style="margin: 0;"><span style="font-weight: bold; color: #111827;">Due Date:</span>
                            {{ $invoice->due_date }}</p>
                    @endif
                </div>
                <div style="margin-top: 5px;">
                    <span class="badge {{ $invoice->status === 'Paid' ? 'badge-paid' : 'badge-pending' }}">
                        {{ strtoupper($invoice->status) }}
                    </span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Addresses -->
    <table style="margin-bottom: 30px;">
        <tr>
            <td width="50%" valign="top">
                <div class="address-title">Bill To</div>
                <table cellpadding="0" cellspacing="0">
                    <tr>
                        @if($invoice->client_logo)
                            <td valign="top" style="padding-right: 15px; width: 60px;">
                                <img src="{{ public_path('storage/' . $invoice->client_logo) }}"
                                    style="width: 50px; height: 50px; object-fit: contain; border: 1px solid #eee; padding: 2px; border-radius: 4px; background-color: #f9fafb;">
                            </td>
                        @endif
                        <td valign="top">
                            <div class="font-bold" style="font-size: 16px; margin-bottom: 2px; color: #111827;">
                                {{ $invoice->client_name }}</div>
                            <div class="text-gray" style="white-space: pre-line; font-size: 12px; font-weight: 500;">
                                {{ $invoice->client_address }}</div>
                                <div class="text-gray" style="margin-top: 4px; font-size: 12px; font-weight: 500;">
                                    <span style="font-family: 'DejaVu Sans'; font-size: 10px;">📞</span> {{ $invoice->client_phone }}
                                </div>
                        </td>
                    </tr>
                </table>
            </td>
            <td width="50%" valign="top" class="text-right">
                <div class="address-title">Bill From</div>
                <div class="font-bold" style="font-size: 16px; margin-bottom: 2px; color: #000;">
                    {{ $invoice->sender_name }}</div>
                <div class="text-gray" style="white-space: pre-line; font-size: 12px;">{{ $invoice->sender_address }}
                </div>
                @if($invoice->sender_phone)
                    <div class="text-gray"
                        style="margin-top: 4px; font-size: 12px; text-transform: uppercase; font-weight: bold;">
                        <span style="font-family: 'DejaVu Sans'; font-size: 10px;">📞</span> {{ $invoice->sender_phone }}
                    </div>
                @endif
                @if($invoice->sender_website)
                    <div style="margin-top: 2px;">
                        <a href="{{ $invoice->sender_website }}"
                            style="color: #0C8D5D; text-decoration: none; font-size: 12px;">{{ $invoice->sender_website }}</a>
                    </div>
                @endif
            </td>
        </tr>
    </table>

    <!-- Items -->
    <table style="margin-bottom: 30px; border: 1px solid #d1d5db;">
        <thead>
            <tr class="items-header">
                <th>Description</th>
                <th class="text-right" width="10%">Qty</th>
                <th class="text-right" width="15%">Price</th>
                <th class="text-right" width="10%">Tax (%)</th>
                <th class="text-right" width="15%">Amount</th>
            </tr>
        </thead>
        <tbody>
            @php $totalItemTax = 0; @endphp
            @foreach($invoice->items as $item)
                @php 
                                    $itemTax = $item->amount * ($item->tax_rate / 100);
                    $totalItemTax += $itemTax;
                @endphp
                <tr class="items-row">
                    <td>{{ $item->description }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-right">{{ $item->tax_rate > 0 ? $item->tax_rate . '%' : '-' }}</td>
                        <td class="text-right font-bold" style="color: #111827;">{{ number_format($item->amount, 2) }}</td>
                    </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals & Bank -->
    <table>
    <tr>
        <td width="55%" valign="top" style="padding-right: 40px;">
            @if($invoice->bank_details)
                <div class="address-title" style="color: #111827;">Bank Details</div>
                    <div class="bank-box">
                        {!! nl2br(e($invoice->bank_details)) !!}
                    </div>
            @endif
            </td>
            <td width="45%" valign="top">
                <table class="totals-table">

                                                <tr>
                        <td class="text-gray" style="font-weight: 500;">Subtotal</td>
                        <td class="text-right font-bold" style="color: #111827;">Rs. {{ number_format($invoice->subtotal, 2) }}</td>
                    </tr>
                    @if($totalItemTax > 0)

                                                <tr>
                            <td class="text-gray" style="font-weight: 500;">Item Tax</td>
                            <td class="text-right font-bold" style="color: #111827;">Rs. {{ number_format($totalItemTax, 2) }}</td>
                        </tr>
                    @endif
                    @if($invoice->tax_rate > 0)

                                                    <tr>
                            <td class="text-gray" style="font-weight: 500;">Global Tax ({{ $invoice->tax_rate }}%)</td>
                            <td class="text-right font-bold" style="color: #111827;">Rs. {{ number_format($invoice->subtotal * ($invoice->tax_rate / 100), 2) }}</td>
                        </tr>
                    @endif
                    <tr class="total-row">
                        <td style="color: #111827;">Total</td>
                        <td class="text-right">Rs. {{ number_format($invoice->total, 2) }}</td>
                    </tr>
                    @if($invoice->status !== 'Draft' && $invoice->paid_amount > 0)
                        <tr>
                            <td class="text-gray text-xs" style="padding-top: 10px; border-bottom: none;">Total Paid</td>
                            <td class="text-right font-bold text-xs" style="padding-top: 10px; color: #065f46; border-bottom: none;">Rs. {{ number_format($invoice->paid_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="font-bold text-xs" style="border-bottom: none;">Remaining Balance</td>
                            <td class="text-right font-bold text-xs" style="color: {{ $invoice->remaining_balance > 0 ? '#0C8D5D' : '#9ca3af' }}; border-bottom: none;">Rs. {{ number_format($invoice->remaining_balance, 2) }}</td>
                        </tr>
                    @endif
                </table>
        </td>
        </tr>
    </table>

    <!-- Amount in Words -->
    @php
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
        $amountInWords = numberToWords($invoice->total);
    @endphp
    <div style="margin-top: 10px; border-top: 1px solid #f3f4f6; padding-top: 10px;">
        <span class="address-title" style="color: #111827; display: block; margin-bottom: 2px;">Amount in Words</span>
        <div style="font-size: 11px; font-weight: bold; text-transform: uppercase; color: #4b5563;">
            {{ $amountInWords }} Rupees Only
        </div>
    </div>
    
    <!-- QR Code Section (Separate row for clean alignment) -->
    @if($invoice->payment_qr_image || $invoice->payment_qr_link)
           <table style="margin-top: 20px;">
                <tr>
                    <td width="100%" class="text-right">
                         <div style="display: inline-block; text-align: center;">
                            @if($invoice->payment_qr_image)
                                <img src="{{ public_path('storage/' . $invoice->payment_qr_image) }}" style="width: 140px; height: 140px; object-fit: contain; border: 1px solid #eee; padding: 5px; background: white;">
                            @else
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($invoice->payment_qr_link) }}" style="width: 100px; border: 1px solid #eee; padding: 5px; background: white;">
                            @endif
                            <div class="text-xs text-gray uppercase font-bold mt-1" style="margin-top: 5px;">Scan to Pay</div>
                        </div>
                    </td>
                </tr>
            </table>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Thank you for your business!</p>
    </div>
</body>
</html>