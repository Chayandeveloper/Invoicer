<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Credit Note #{{ $creditNote->credit_note_number }}</title>
    <style>
        @page { margin: 0; }
        body { font-family: 'Helvetica', sans-serif; color: #1f2937; margin: 0; padding: 0; }
        .header { background-color: #6932BB; padding: 40px; color: #ffffff; }
        .container { padding: 40px; }
        .party-table { width: 100%; margin-bottom: 40px; }
        .party-box { width: 48%; vertical-align: top; }
        .label { font-size: 10px; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 5px; }
        .value { font-size: 14px; font-weight: bold; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        .items-table th { background-color: #f9fafb; padding: 12px; text-align: left; font-size: 10px; font-weight: bold; text-transform: uppercase; border-bottom: 2px solid #e5e7eb; }
        .items-table td { padding: 12px; border-bottom: 1px solid #f3f4f6; font-size: 12px; }
        .total-box { float: right; width: 250px; background-color: #f9fafb; padding: 20px; border-radius: 10px; }
        .total-row { margin-bottom: 10px; }
        .total-label { font-size: 10px; font-weight: bold; color: #6b7280; }
        .total-value { float: right; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td>
                    <div style="font-size: 24px; font-weight: bold;">{{ $creditNote->business->name }}</div>
                    <div style="font-size: 10px; opacity: 0.8;">CREDIT NOTE</div>
                </td>
                <td style="text-align: right;">
                    <div style="font-size: 14px; font-weight: bold;">#{{ $creditNote->credit_note_number }}</div>
                    <div style="font-size: 10px; opacity: 0.8;">{{ \Carbon\Carbon::parse($creditNote->credit_note_date)->format('d M, Y') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="container">
        <table class="party-table">
            <tr>
                <td class="party-box">
                    <div class="label">Issued By</div>
                    <div class="value">{{ $creditNote->business->name }}</div>
                    <div style="font-size: 10px; color: #6b7280;">{{ $creditNote->business->address }}</div>
                </td>
                <td style="width: 4%;"></td>
                <td class="party-box">
                    <div class="label">Issued To</div>
                    <div class="value">{{ $creditNote->client->name }}</div>
                    <div style="font-size: 10px; color: #6b7280;">{{ $creditNote->client->address }}</div>
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Rate</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($creditNote->items as $item)
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td style="text-align: center;">{{ $item->quantity }}</td>
                        <td style="text-align: right;">₹{{ number_format($item->rate, 2) }}</td>
                        <td style="text-align: right; font-weight: bold;">₹{{ number_format($item->amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-box">
            <div class="total-row">
                <span class="total-label">Subtotal</span>
                <span class="total-value">₹{{ number_format($creditNote->total_amount, 2) }}</span>
            </div>
            <div style="border-top: 1px solid #e5e7eb; margin: 10px 0; padding-top: 10px;">
                <span style="font-size: 12px; font-weight: bold; color: #6932BB;">CREDIT VALUE</span>
                <span style="float: right; font-size: 18px; font-weight: bold; color: #6932BB;">₹{{ number_format($creditNote->total_amount, 2) }}</span>
            </div>
        </div>

        <div style="clear: both; margin-top: 40px; font-size: 10px; color: #9ca3af; font-style: italic;">
            Notes: {{ $creditNote->notes ?? 'N/A' }}
        </div>
    </div>
</body>
</html>
