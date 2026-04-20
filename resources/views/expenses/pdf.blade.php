<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Expense Voucher #{{ $expense->id }}</title>
    <style>
        @page {
            margin: 0px;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 14px;
            margin: 40px;
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
        .header-table {
            margin-bottom: 30px;
            border-bottom: 2px solid #0C8D5D;
            padding-bottom: 20px;
        }

        .logo {
            max-height: 80px;
            max-width: 200px;
        }

        .voucher-title {
            font-size: 28px;
            font-weight: 800;
            color: #0C8D5D;
            margin-bottom: 5px;
            text-transform: uppercase;
            text-align: right;
        }

        /* Amount Box */
        .amount-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .amount-label {
            font-size: 12px;
            font-weight: bold;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }

        .amount-value {
            font-size: 36px;
            font-weight: bold;
            color: #111827;
        }

        /* Details Table */
        .details-table th {
            text-align: left;
            padding: 12px;
            background-color: #f3f4f6;
            color: #4b5563;
            font-size: 11px;
            text-transform: uppercase;
            font-weight: bold;
            border-bottom: 1px solid #e5e7eb;
        }

        .details-table td {
            padding: 12px;
            border-bottom: 1px solid #f3f4f6;
            color: #111827;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
            text-transform: uppercase;
        }
        
        .badge-paid { background: #d1fae5; color: #065f46; border: 1px solid #065f46; }
        .badge-pending { background: #fef2f2; color: #b91c1c; border: 1px solid #b91c1c; }

        /* Footer */
        .footer {
            margin-top: 50px;
            text-align: center;
            color: #9ca3af;
            font-size: 11px;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
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
    @if($expense->business && $expense->business->logo)
        <div class="watermark">
            <img src="{{ Str::startsWith($expense->business->logo, 'http') ? $expense->business->logo : public_path('storage/' . $expense->business->logo) }}">
        </div>
    @endif

    <table class="header-table">
        <tr>
            <td valign="top">
                @if($expense->business && $expense->business->logo)
                    <img src="{{ Str::startsWith($expense->business->logo, 'http') ? $expense->business->logo : public_path('storage/' . $expense->business->logo) }}" class="logo">
                @else
                    <h2 style="margin: 0; color: #0C8D5D;">{{ $expense->business->name ?? 'Expense Voucher' }}</h2>
                @endif
                @if($expense->business)
                    <div style="font-size: 12px; color: #6b7280; margin-top: 5px;">
                        <strong>{{ $expense->business->name }}</strong><br>
                        {{ $expense->business->address ?? '' }}
                    </div>
                @endif
            </td>
            <td valign="top" class="text-right">
                <div class="voucher-title">EXPENSE VOUCHER</div>
                <div style="font-size: 12px; collapse: #6b7280;">
                    <strong>Voucher ID:</strong> #EXP-{{ $expense->id }}<br>
                    <strong>Date:</strong> {{ \Carbon\Carbon::parse($expense->expense_date)->format('M d, Y') }}<br>
                    <div style="margin-top: 5px;">
                        <span class="badge {{ $expense->status === 'Paid' ? 'badge-paid' : 'badge-pending' }}">
                            {{ $expense->status }}
                        </span>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <div class="amount-box">
        <div class="amount-label">Total Expense Amount</div>
        <div class="amount-value">Rs. {{ number_format($expense->amount, 2) }}</div>
        @if($expense->tax_amount > 0)
            <div style="font-size: 11px; color: #9ca3af; margin-top: 5px;">
                Includes Rs. {{ number_format($expense->tax_amount, 2) }} Tax
            </div>
        @endif
    </div>

    <table class="details-table">
        <tr>
            <th width="35%">Expense Category</th>
            <td>{{ $expense->category }}</td>
        </tr>
        <tr>
            <th>Vendor / Payee</th>
            <td>{{ $expense->vendor ?? '-' }}</td>
        </tr>
        <tr>
            <th>Payment Method</th>
            <td>{{ $expense->payment_method }}</td>
        </tr>
        @if($expense->reference_number)
        <tr>
            <th>Reference Number</th>
            <td>{{ $expense->reference_number }}</td>
        </tr>
        @endif
        @if($expense->description)
        <tr>
            <th>Description / Notes</th>
            <td>{!! nl2br(e($expense->description)) !!}</td>
        </tr>
        @endif
    </table>

    <div style="margin-top: 60px;">
        <table style="width: 100%">
            <tr>
                <td width="50%">
                    <div style="border-top: 2px solid #e5e7eb; width: 80%; padding-top: 10px; font-size: 12px; color: #6b7280;">
                        Approved By
                    </div>
                </td>
                <td width="50%" class="text-right">
                    <div style="border-top: 2px solid #e5e7eb; width: 80%; display: inline-block; padding-top: 10px; font-size: 12px; color: #6b7280;">
                        Receiver's Signature
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Generated by Fillosoft Invoicer &bull; {{ now()->format('F d, Y h:i A') }}
    </div>
</body>
</html>