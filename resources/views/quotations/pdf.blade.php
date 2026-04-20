<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quotation-{{ $quotation->quotation_number }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; margin: 0; padding: 30px; line-height: 1.4; }
        .header { border-bottom: 2px solid #0C8D5D; padding-bottom: 20px; margin-bottom: 20px; }
        .logo { height: 60px; }
        .title { font-size: 28px; font-weight: bold; color: #0C8D5D; text-align: right; }
        .details-table { width: 100%; margin-bottom: 30px; }
        .details-table td { vertical-align: top; width: 50%; }
        .section-title { font-size: 10px; color: #999; text-transform: uppercase; margin-bottom: 5px; font-weight: bold; }
        .info-text { font-size: 13px; font-weight: bold; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; border-radius: 8px; overflow: hidden; }
        .items-table th { background: #f9f9f9; padding: 12px; font-size: 11px; color: #666; text-transform: uppercase; text-align: left; }
        .items-table td { padding: 12px; font-size: 12px; border-bottom: 1px solid #eee; }
        .text-right { text-align: right; }
        .totals-table { width: 40%; margin-left: auto; margin-top: 20px; }
        .totals-table td { padding: 5px 0; font-size: 13px; }
        .total-row { font-size: 18px; font-weight: bold; color: #0C8D5D; border-top: 2px solid #eee; padding-top: 10px; }
        .footer { margin-top: 50px; font-size: 11px; color: #999; text-align: center; border-top: 1px solid #eee; padding-top: 20px; }
        .validity { margin-top: 20px; font-size: 11px; color: #666; background: #fdfdfd; padding: 10px; border: 1px solid #f0f0f0; border-radius: 4px; }
        /* Watermark */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 500px;
            opacity: 0.1;
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
    @if($quotation->sender_logo)
        <div class="watermark">
            <img src="{{ public_path('storage/' . $quotation->sender_logo) }}">
        </div>
    @endif
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td>
                    @if($quotation->sender_logo)
                        <img src="{{ public_path('storage/' . $quotation->sender_logo) }}" class="logo">
                    @else
                        <h2 style="color: #0C8D5D; margin: 0;">{{ $quotation->sender_name }}</h2>
                    @endif
                </td>
                <td class="title">QUOTATION</td>
            </tr>
        </table>
    </div>

    <table class="details-table">
        <tr>
            <td>
                <div class="section-title">Quotation To</div>
                <div class="flex-box" style="display: flex; align-items: flex-start;">
                    @if($quotation->client_logo)
                        <img src="{{ public_path('storage/' . $quotation->client_logo) }}" style="height: 40px; margin-right: 15px; margin-bottom: 10px;">
                    @endif
                    <div style="display: inline-block; vertical-align: top;">
                        <div class="info-text">{{ $quotation->client_name }}</div>
                        <div style="font-size: 12px; color: #666; margin-top: 3px;">{!! nl2br(e($quotation->client_address)) !!}</div>
                        @if($quotation->client_phone)
                            <div style="font-size: 11px; color: #666; margin-top: 2px;">Phone: {{ $quotation->client_phone }}</div>
                        @endif
                    </div>
                </div>
            </td>
            <td class="text-right">
                <div class="section-title">Quotation Details</div>
                <div style="font-size: 12px;">
                    <strong>Quotation #:</strong> {{ $quotation->quotation_number }}<br>
                    <strong>Date:</strong> {{ \Carbon\Carbon::parse($quotation->quotation_date)->format('M d, Y') }}<br>
                    @if($quotation->expiry_date)
                        <strong>Expires:</strong> {{ \Carbon\Carbon::parse($quotation->expiry_date)->format('M d, Y') }}
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right" style="width: 60px;">Qty</th>
                <th class="text-right" style="width: 90px;">Price</th>
                <th class="text-right" style="width: 60px;">Tax</th>
                <th class="text-right" style="width: 100px;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @php $totalItemTax = 0; @endphp
            @foreach($quotation->items as $item)
                @php 
                    $itemTax = $item->amount * (($item->tax_rate ?? 0) / 100); 
                    $totalItemTax += $itemTax;
                @endphp
                <tr>
                    <td>{{ $item->description }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">Rs. {{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">{{ $item->tax_rate > 0 ? $item->tax_rate . '%' : '-' }}</td>
                    <td class="text-right"><strong>Rs. {{ number_format($item->amount, 2) }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td>Subtotal</td>
            <td class="text-right">Rs. {{ number_format($quotation->subtotal, 2) }}</td>
        </tr>
        @if($totalItemTax > 0)
        <tr>
            <td>Item Tax</td>
            <td class="text-right">Rs. {{ number_format($totalItemTax, 2) }}</td>
        </tr>
        @endif
        @if($quotation->tax_rate > 0)
        <tr>
            <td>Global Tax ({{ $quotation->tax_rate }}%)</td>
            <td class="text-right">Rs. {{ number_format($quotation->subtotal * ($quotation->tax_rate / 100), 2) }}</td>
        </tr>
        @endif
        <tr class="total-row">
            <td>TOTAL</td>
            <td class="text-right">Rs. {{ number_format($quotation->total, 2) }}</td>
        </tr>
    </table>

    <div class="validity">
        <strong>Terms & Conditions:</strong><br>
        1. Validity: This quotation is valid for 30 days from the date of issuance.<br>
        2. Prices are subject to change after the expiry date.<br>
        3. 50% advance payment required to commence work.<br>
        @if($quotation->bank_details)
            <br><strong>Banking Details:</strong><br>
            {{ $quotation->bank_details }}
        @endif
    </div>

    <div class="footer">
        <strong>{{ $quotation->sender_name }}</strong><br>
        {{ $quotation->sender_address }}<br>
        @if($quotation->sender_phone) Phone: {{ $quotation->sender_phone }} @endif
        @if($quotation->sender_website) &bull; {{ $quotation->sender_website }} @endif<br>
        Thank you for choosing us for your project!
    </div>
</body>
</html>
