@extends('layout')

@section('content')
    <div class="max-w-4xl mx-auto relative">
        @if($payment->invoice && $payment->invoice->logo)
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-0 opacity-10">
                <img src="{{ Str::startsWith($payment->invoice->logo, 'http') ? $payment->invoice->logo : asset('storage/' . $payment->invoice->logo) }}"
                    class="w-[500px] object-contain grayscale">
            </div>
        @endif
        <div class="flex justify-between items-center mb-6 no-print">
            <div class="flex items-center gap-4">
                <a href="{{ route('payments.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Receipt #{{ $payment->receipt_number }}</h1>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('payments.download', $payment->id) }}"
                    class="bg-primary text-white px-4 py-2 rounded-lg font-medium hover:bg-primary-dark transition shadow-sm flex items-center gap-2">
                    <i class="fas fa-file-pdf"></i> Download PDF
                </a>
                <button onclick="window.print()"
                    class="bg-gray-900 text-white px-4 py-2 rounded-lg font-medium hover:bg-gray-800 transition shadow-sm flex items-center gap-2">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-lg border border-gray-200 p-12 text-gray-800">
            <!-- Header -->
            <div class="flex justify-between items-start border-b border-gray-100 pb-8 mb-8">
                <div>
                    <h2 class="text-3xl font-extrabold text-primary tracking-tight mb-1">PAYMENT RECEIPT</h2>
                    <p class="text-sm text-gray-400 font-semibold uppercase tracking-wider">Transaction Record</p>
                </div>
                <div class="text-right">
                    <p class="text-sm"><span class="font-bold text-gray-900">Receipt #:</span>
                        {{ $payment->receipt_number }}</p>
                    <p class="text-sm"><span class="font-bold text-gray-900">Date:</span>
                        {{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</p>
                </div>
            </div>

            <!-- Amount Card -->
            <div class="bg-gray-50 rounded-xl p-8 border border-gray-100 text-center mb-8">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Total Amount Received</p>
                <h3 class="text-5xl font-black text-gray-900">Rs. {{ number_format($payment->amount, 2) }}</h3>
            </div>

            <!-- Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-12">
                <div>
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Received From</h4>
                    <div class="flex items-center gap-4">
                        @if($payment->client_logo)
                            <img src="{{ asset('storage/' . $payment->client_logo) }}"
                                class="h-10 w-10 object-contain rounded border border-gray-100 p-1 bg-white shadow-sm"
                                alt="Client Logo">
                        @endif
                        <div>
                            <p class="text-lg font-bold text-gray-900">
                                {{ $payment->invoice ? $payment->invoice->client_name : ($payment->client_name ?? 'Client') }}
                            </p>
                        </div>
                    </div>
                    @if($payment->invoice)
                        <div class="mt-1 text-primary font-semibold text-sm">
                            Linked to Invoice: {{ $payment->invoice->invoice_number }}
                        </div>
                    @endif
                </div>
                <div>
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Payment Details</h4>
                    <p class="text-lg font-bold text-gray-900">{{ $payment->payment_method }}</p>
                    @if($payment->reference_number)
                        <p class="text-sm text-gray-500 font-mono mt-1">Ref: {{ $payment->reference_number }}</p>
                    @endif
                </div>
            </div>

            @if($payment->notes)
                <div class="mb-12">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Notes</h4>
                    <div class="bg-gray-50 p-4 rounded-lg text-gray-600 text-sm italic border-l-4 border-gray-200">
                        {{ $payment->notes }}
                    </div>
                </div>
            @endif

            <!-- Footer -->
            <div class="mt-20 flex justify-between items-end border-t border-gray-100 pt-8">
                <div class="text-xs text-gray-400 font-medium italic">
                    This is a computer generated receipt and does not require a physical signature.
                </div>
                <div class="text-center">
                    <div class="w-48 border-b border-gray-900 mb-2"></div>
                    <p class="text-[10px] font-bold text-gray-900 uppercase tracking-widest">Authorized Signatory</p>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white;
                padding: 0;
            }

            .shadow-lg {
                shadow: none;
            }
        }
    </style>
@endsection