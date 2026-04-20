<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Payment Receipt #{{ $payment->receipt_number }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap');

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #111827;
            margin: 0;
            padding: 40px;
            background: #fff;
        }

        .header {
            border-bottom: 4px solid #0C8D5D;
            padding-bottom: 20px;
            margin-bottom: 40px;
        }

        .receipt-title {
            font-size: 32px;
            font-weight: 900;
            color: #0C8D5D;
            text-transform: uppercase;
            letter-spacing: -1px;
        }

        .receipt-meta {
            text-align: right;
            font-size: 12px;
            color: #6B7280;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .receipt-number {
            font-size: 20px;
            color: #111827;
            font-weight: 900;
            margin-top: 5px;
        }

        .info-grid {
            width: 100%;
            margin-bottom: 40px;
        }

        .info-box {
            width: 50%;
            vertical-align: top;
        }

        .label {
            font-size: 10px;
            font-weight: bold;
            color: #9CA3AF;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .value {
            font-size: 14px;
            font-weight: bold;
            color: #111827;
        }

        .amount-card {
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 40px;
        }

        .amount-label {
            font-size: 12px;
            font-weight: bold;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 10px;
        }

        .amount-value {
            font-size: 40px;
            font-weight: 900;
            color: #111827;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }

        .details-table th {
            text-align: left;
            font-size: 10px;
            font-weight: bold;
            color: #9CA3AF;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 10px 0;
            border-bottom: 1px solid #E5E7EB;
        }

        .details-table td {
            padding: 15px 0;
            font-size: 13px;
            border-bottom: 1px solid #F3F4F6;
        }

        .details-table td.label-td {
            font-weight: bold;
            color: #6B7280;
        }

        .details-table td.value-td {
            font-weight: bold;
            color: #111827;
            text-align: right;
        }

        .footer {
            margin-top: 60px;
            text-align: center;
            font-size: 11px;
            color: #9CA3AF;
            border-top: 1px solid #E5E7EB;
            padding-top: 20px;
        }

        .signature-box {
            margin-top: 60px;
            text-align: right;
        }

        .signature-line {
            display: inline-block;
            width: 200px;
            border-top: 2px solid #111827;
            padding-top: 10px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

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
    @if($payment->invoice && $payment->invoice->logo)
        <div class="watermark">
            <img
                src="{{ Str::startsWith($payment->invoice->logo, 'http') ? $payment->invoice->logo : public_path('storage/' . $payment->invoice->logo) }}">
        </div>
    @endif
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td><span class="receipt-title">Payment Receipt</span></td>
                <td class="receipt-meta">
                    <div>Receipt Number</div>
                    <div class="receipt-number">#{{ $payment->receipt_number }}</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="info-grid">
        <tr>
            <td class="info-box">
                <div class="label">Received From</div>
                <div style="display: flex; align-items: center;">
                    @if($payment->client_logo)
                        <img src="{{ public_path('storage/' . $payment->client_logo) }}"
                            style="height: 30px; margin-right: 15px; vertical-align: middle;">
                    @endif
                    <div class="value" style="display: inline-block; vertical-align: middle;">
                        {{ $payment->invoice ? $payment->invoice->client_name : ($payment->client_name ?? 'Valued Client') }}
                    </div>
                </div>
            </td>
            <td class="info-box" style="text-align: right;">
                <div class="label">Date of Payment</div>
                <div class="value">{{ \Carbon\Carbon::parse($payment->payment_date)->format('F d, Y') }}</div>
            </td>
        </tr>
    </table>

    <div class="amount-card">
        <div class="amount-label">Amount Received</div>
        <div class="amount-value">Rs. {{ number_format($payment->amount, 2) }}</div>
    </div>

    <table class="details-table">
        <thead>
            <tr>
                <th colspan="2">Payment Details</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="label-td">Payment Method</td>
                <td class="value-td">{{ $payment->payment_method }}</td>
            </tr>
            @if($payment->reference_number)
                <tr>
                    <td class="label-td">Reference / Transaction ID</td>
                    <td class="value-td">{{ $payment->reference_number }}</td>
                </tr>
            @endif
            @if($payment->invoice)
                <tr>
                    <td class="label-td">Linked Invoice</td>
                    <td class="value-td">{{ $payment->invoice->invoice_number }}</td>
                </tr>
            @endif
            @if($payment->notes)
                <tr>
                    <td class="label-td">Account Notes</td>
                    <td class="value-td" style="font-weight: normal; color: #6B7280; font-style: italic;">
                        {{ $payment->notes }}
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="signature-box">
        <div class="signature-line">Authorized Signatory</div>
    </div>

    <div class="footer">
        Thank you for your payment. This is a computer generated receipt.
    </div>
</body>

</html>