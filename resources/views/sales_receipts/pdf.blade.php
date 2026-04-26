<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Receipt #{{ $receipt->receipt_number }}</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #111827;
            margin: 0;
            padding: 0;
            background: #fff;
            line-height: 1.4;
        }
        .header-bar {
            background-color: #6932BB;
            padding: 40px;
            color: #ffffff;
            position: relative;
        }
        .header-table {
            width: 100%;
        }
        .business-logo-container {
            width: 70px;
            height: 70px;
            background-color: #ffffff;
            border-radius: 10px;
            padding: 5px;
            text-align: center;
        }
        .business-logo {
            max-width: 60px;
            max-height: 60px;
        }
        .receipt-label {
            font-size: 10px;
            font-weight: bold;
            color: rgba(255, 255, 255, 0.6);
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 5px;
        }
        .receipt-title {
            font-size: 32px;
            font-weight: bold;
            margin: 0;
            color: #ffffff;
        }
        .container {
            padding: 40px;
            position: relative;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            margin-top: -150px;
            margin-left: -225px;
            width: 450px;
            opacity: 0.2; 
            z-index: -1000;
            text-align: center;
        }
        .parties-table {
            width: 100%;
            margin-bottom: 30px;
        }
        .party-box {
            width: 48%;
            vertical-align: top;
        }
        .section-label {
            font-size: 9px;
            font-weight: bold;
            color: #9CA3AF;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
            border-left: 3px solid #6932BB;
            padding-left: 8px;
        }
        .party-card {
            background-color: #f8fafc;
            padding: 15px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
        }
        .party-card-primary {
            background-color: #6932BB;
            padding: 15px;
            border-radius: 10px;
            color: #ffffff;
        }
        .party-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .party-detail {
            font-size: 10px;
            color: #64748b;
        }
        .description-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .amount-section {
            background-color: #f8fafc;
            padding: 30px;
            border-radius: 20px;
            text-align: center;
            margin-bottom: 30px;
            border: 1px solid #e2e8f0;
        }
        .amount-label {
            font-size: 10px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 10px;
        }
        .amount-value {
            font-size: 38px;
            font-weight: bold;
            color: #111827;
        }
        .amount-verified {
            font-size: 9px;
            color: #6932BB;
            font-weight: bold;
            margin-top: 10px;
            text-transform: uppercase;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .details-table td {
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 11px;
        }
        .details-label {
            color: #64748b;
            font-weight: bold;
        }
        .details-value {
            text-align: right;
            font-weight: bold;
        }
        .notes-box {
            padding: 15px;
            background-color: #fffbeb;
            border-radius: 10px;
            font-size: 11px;
            color: #92400e;
            font-style: italic;
            margin-bottom: 30px;
        }
        .footer {
            margin-top: 40px;
        }
        .signature-table {
            width: 100%;
        }
        .signature-box {
            text-align: right;
            width: 200px;
        }
        .signature-line {
            border-top: 1px solid #111827;
            padding-top: 5px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .thanks-bar {
            background-color: #6932BB;
            color: #ffffff;
            text-align: center;
            padding: 10px;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 3px;
            position: absolute;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    @php
        $business = $receipt->business ?? auth()->user()->businesses()->first();
        $client_name = $receipt->client_name ?? 'Valued Client';
    @endphp

    <div class="header-bar">
        <table class="header-table">
            <tr>
                <td style="width: 80px;">
                    @if($business && $business->logo)
                        <div class="business-logo-container">
                            <img src="{{ public_path('storage/' . $business->logo) }}" class="business-logo">
                        </div>
                    @endif
                </td>
                <td>
                    <div style="font-size: 18px; font-weight: bold; letter-spacing: -1px;">{{ $business->name ?? 'Sales Receipt' }}</div>
                    <div style="font-size: 10px; color: rgba(255, 255, 255, 0.7); font-weight: bold; text-transform: uppercase;">{{ $business->tagline ?? 'Sales Confirmation' }}</div>
                </td>
                <td style="text-align: right;">
                    <div class="receipt-label">Receipt #{{ $receipt->receipt_number }}</div>
                    <div class="receipt-title">SALES RECEIPT</div>
                    <div style="font-size: 11px; color: #ffffff; font-weight: bold; margin-top: 5px; opacity: 0.8;">{{ \Carbon\Carbon::parse($receipt->receipt_date)->format('d M, Y') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="container">
        <table class="parties-table">
            <tr>
                <td class="party-box">
                    <div class="section-label">Received From</div>
                    <div class="party-card">
                        <table style="width: 100%;">
                            <tr>
                                @if($receipt->client_logo)
                                    <td style="width: 45px;">
                                        <img src="{{ public_path('storage/' . $receipt->client_logo) }}" style="width: 35px; border-radius: 5px;">
                                    </td>
                                @endif
                                <td>
                                    <div class="party-name">{{ $client_name }}</div>
                                    <div class="party-detail">Authorized Payer</div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
                <td style="width: 4%;"></td>
                <td class="party-box">
                    <div class="section-label" style="border-left-color: #111827; text-align: right; padding-right: 8px; border-right: 3px solid #111827; border-left: none;">Paid To</div>
                    <div class="party-card-primary">
                        <table style="width: 100%;">
                            <tr>
                                <td>
                                    <div class="party-name" style="color: #ffffff;">{{ $business->name ?? 'Company Name' }}</div>
                                    <div class="party-detail" style="color: rgba(255, 255, 255, 0.7);">Authorized Business</div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <div class="section-label">Item / Description</div>
        <div class="description-box">
            <div style="font-size: 12px; color: #111827; font-weight: bold;">
                {{ $receipt->item_description ?? 'General Sales' }}
            </div>
        </div>

        <div class="amount-section">
            <div class="amount-label">Total Amount Received</div>
            <div class="amount-value"><span style="color: #6932BB; font-size: 18px;">Rs.</span> {{ number_format($receipt->amount, 2) }}</div>
            <div class="amount-verified">★ Transaction Verified ★</div>
        </div>

        <table class="details-table">
            <tr>
                <td class="details-label">Payment Method</td>
                <td class="details-value">{{ $receipt->payment_method }}</td>
            </tr>
            @if($receipt->reference_number)
                <tr>
                    <td class="details-label">Transaction Reference</td>
                    <td class="details-value">{{ $receipt->reference_number }}</td>
                </tr>
            @endif
        </table>

        @if($receipt->notes)
            <div class="notes-box">
                "{{ $receipt->notes }}"
            </div>
        @endif

        <div class="footer">
            <table class="signature-table">
                <tr>
                    <td style="font-size: 9px; color: #94a3b8; font-style: italic; vertical-align: bottom;">
                        This document serves as an official proof of sale. <br>
                        Generated on {{ date('Y-m-d H:i:s') }}
                    </td>
                    <td class="signature-box">
                        <div style="height: 30px;"></div>
                        <div class="signature-line">Authorized Signatory</div>
                        <div style="font-size: 8px; color: #94a3b8; margin-top: 2px;">{{ auth()->user()->name }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Watermark (Drawn on Top) -->
        <div class="watermark">
            @if($business && $business->logo)
                <img src="{{ public_path('storage/' . $business->logo) }}" style="width: 100%;">
            @elseif($business)
                <div style="font-size: 90px; font-weight: 900; color: #6932BB; opacity: 0.1; transform: rotate(-25deg); white-space: nowrap; text-transform: uppercase; letter-spacing: 8px;">
                    {{ $business->name }}
                </div>
            @endif
        </div>
    </div>

    <div class="thanks-bar">
        Thank you for your business
    </div>
</body>
</html>
