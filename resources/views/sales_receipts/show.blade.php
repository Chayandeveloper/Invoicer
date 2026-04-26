@extends('layout')

@section('content')
@php
    $business = $receipt->business ?? auth()->user()->businesses()->first();
    $client_name = $receipt->client_name ?? 'Valued Client';
@endphp

<div class="max-w-2xl mx-auto px-4 py-8">

    {{-- Action Bar --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-8 no-print">
        <div class="flex items-center gap-4">
            <a href="{{ route('sales-receipts.index') }}"
               class="group flex items-center justify-center w-10 h-10 bg-white rounded-xl shadow-sm border border-gray-100 text-gray-400 hover:text-primary transition-all">
                <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
            </a>
            <div>
                <h1 class="text-xl font-black text-gray-900 tracking-tight">Sales Receipt Detail</h1>
                <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">#{{ $receipt->receipt_number }}</p>
            </div>
        </div>
        <div class="flex gap-3 w-full sm:w-auto">
            <a href="{{ route('sales-receipts.download', $receipt->id) }}"
               class="flex-1 sm:flex-none bg-primary text-white px-6 py-3 rounded-xl font-black hover:bg-primary-dark transition-all shadow-lg shadow-primary/20 flex items-center justify-center gap-2 text-xs uppercase tracking-widest">
                <i class="fas fa-file-pdf"></i> Download PDF
            </a>
            <button onclick="window.print()"
               class="flex-1 sm:flex-none bg-gray-900 text-white px-6 py-3 rounded-xl font-black hover:bg-gray-800 transition-all shadow-lg shadow-gray-900/10 flex items-center justify-center gap-2 text-xs uppercase tracking-widest">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>

    {{-- Receipt Card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-xl overflow-hidden animate-slide-in">

        {{-- Header --}}
        <div class="bg-primary px-8 py-7 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-48 h-48 rounded-full bg-white/5 -translate-y-1/2 translate-x-1/4"></div>
            <div class="absolute bottom-0 right-16 w-28 h-28 rounded-full bg-black/8 translate-y-1/2"></div>

            <div class="relative z-10 flex items-center justify-between">
                {{-- Business --}}
                <div class="flex items-center gap-4">
                    @if($business && $business->logo)
                        <div class="w-12 h-12 bg-white rounded-xl p-2 shadow-md shrink-0">
                            <img src="{{ asset('storage/' . $business->logo) }}" class="w-full h-full object-contain" alt="Logo">
                        </div>
                    @endif
                    <div>
                        <p class="text-white font-black text-base tracking-tight uppercase">{{ $business->name ?? 'Sales Receipt' }}</p>
                        <p class="text-white/60 text-[10px] uppercase tracking-[0.15em] font-bold mt-0.5">{{ $business->tagline ?? 'Official Sales Receipt' }}</p>
                    </div>
                </div>
                {{-- Receipt Meta --}}
                <div class="text-right">
                    <p class="text-white/40 text-[9px] uppercase tracking-[0.2em] font-bold">Receipt</p>
                    <p class="text-white font-black text-sm mt-0.5">{{ \Carbon\Carbon::parse($receipt->receipt_date)->format('d M, Y') }}</p>
                    <p class="text-white/50 text-[10px] font-mono mt-0.5">#{{ $receipt->receipt_number }}</p>
                </div>
            </div>

            {{-- Verified Badge --}}
            <div class="relative z-10 mt-5">
                <span class="inline-flex items-center gap-2 bg-white/10 border border-white/20 text-white rounded-full px-4 py-1.5 text-[10px] font-bold uppercase tracking-[0.12em]">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-400 inline-block"></span>
                    Sales Receipt Verified
                </span>
            </div>
        </div>

        <div class="px-8 py-7 space-y-6">

            {{-- Parties --}}
            <div class="grid grid-cols-2 gap-3">
                {{-- From --}}
                <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                    <p class="text-[9px] font-black uppercase tracking-[0.15em] text-gray-400 mb-2">Received From</p>
                    <p class="text-sm font-black text-gray-900 tracking-tight">{{ $client_name }}</p>
                    <p class="text-[11px] text-gray-400 font-bold mt-0.5">Authorized Payer</p>
                </div>
                {{-- To --}}
                <div class="rounded-xl bg-primary p-4">
                    <p class="text-[9px] font-black uppercase tracking-[0.15em] text-white/50 mb-2">Paid To</p>
                    <p class="text-sm font-black text-white tracking-tight">{{ $business->name ?? 'Company' }}</p>
                    @if($business && $business->address)
                        <p class="text-[10px] text-white/60 font-bold mt-0.5 leading-tight">{{ $business->address }}</p>
                    @endif
                </div>
            </div>

            {{-- Description & Amount --}}
            <div class="rounded-2xl border border-gray-100 bg-gray-50 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-white">
                    <p class="text-[9px] font-black uppercase tracking-[0.15em] text-gray-400 mb-2">Item / Description</p>
                    <p class="text-sm font-bold text-gray-900 leading-relaxed">{{ $receipt->item_description ?? 'General Sales' }}</p>
                </div>
                <div class="py-8 px-6 text-center">
                    <p class="text-[9px] font-black uppercase tracking-[0.2em] text-gray-400 mb-3">Total Transaction Amount</p>
                    <div class="flex items-baseline justify-center gap-2">
                        <span class="text-xl font-black text-primary">Rs.</span>
                        <span class="text-6xl font-black text-gray-900 tracking-tighter leading-none">{{ number_format($receipt->amount, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Payment Method & Reference --}}
            <div class="flex flex-wrap gap-3">
                <div class="flex items-center gap-3 bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 flex-1 min-w-0">
                    <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center shrink-0">
                        <i class="fas fa-credit-card text-primary text-xs"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[9px] font-black uppercase tracking-[0.12em] text-gray-400">Payment Method</p>
                        <p class="text-sm font-black text-gray-900 truncate mt-0.5">{{ $receipt->payment_method }}</p>
                    </div>
                </div>

                @if($receipt->reference_number)
                <div class="flex items-center gap-3 bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 flex-1 min-w-0">
                    <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center shrink-0">
                        <i class="fas fa-fingerprint text-primary text-xs"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[9px] font-black uppercase tracking-[0.12em] text-gray-400">Transaction ID</p>
                        <p class="text-sm font-mono font-bold text-gray-900 truncate mt-0.5">{{ $receipt->reference_number }}</p>
                    </div>
                </div>
                @endif
            </div>

            {{-- Notes --}}
            @if($receipt->notes)
            <div class="bg-amber-50/40 border border-amber-100/50 rounded-2xl p-5 relative overflow-hidden">
                <i class="fas fa-quote-right absolute top-3 right-4 text-2xl text-amber-200/60"></i>
                <p class="text-[9px] font-black uppercase tracking-[0.15em] text-amber-600 mb-2">Internal Notes</p>
                <p class="text-sm text-gray-600 italic font-medium leading-relaxed">"{{ $receipt->notes }}"</p>
            </div>
            @endif

            {{-- Footer --}}
            <div class="flex items-end justify-between pt-5 border-t border-gray-50">
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-file-invoice-dollar text-primary text-sm"></i>
                        <span class="text-sm font-black text-gray-900 uppercase tracking-tight">Invoicer</span>
                    </div>
                    <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest italic leading-relaxed max-w-xs">
                        Official proof of sale. Digitally generated on {{ date('Y-m-d H:i') }}.
                    </p>
                </div>
                <div class="text-center">
                    <span class="font-serif italic text-2xl text-gray-200 select-none block mb-3">{{ auth()->user()->name }}</span>
                    <div class="w-36 h-px bg-gray-900 mb-1.5 mx-auto"></div>
                    <p class="text-[10px] font-black text-gray-900 uppercase tracking-[0.15em]">Authorized Signature</p>
                    <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">Fillosoft Technologies</p>
                </div>
            </div>

        </div>

        {{-- Footer Bar --}}
        <div class="bg-primary py-3 text-center">
            <p class="text-[9px] text-white/60 font-bold uppercase tracking-[0.25em]">Thank you for your business</p>
        </div>

    </div>

    {{-- Links --}}
    <div class="mt-10 flex justify-center gap-10 no-print">
        <a href="{{ route('dashboard') }}" class="group text-[10px] font-black text-gray-400 hover:text-primary uppercase tracking-[0.2em] transition-all flex items-center gap-2">
            <i class="fas fa-chart-line group-hover:-translate-y-1 transition-transform"></i> Analytics
        </a>
        <a href="{{ route('sales-receipts.index') }}" class="group text-[10px] font-black text-gray-400 hover:text-primary uppercase tracking-[0.2em] transition-all flex items-center gap-2">
            <i class="fas fa-history group-hover:rotate-[-30deg] transition-transform"></i> History
        </a>
    </div>
</div>

<style>
    @keyframes slide-in {
        from { transform: translateY(16px); opacity: 0; }
        to   { transform: translateY(0);    opacity: 1; }
    }
    .animate-slide-in { animation: slide-in 0.4s ease both; }

    @media print {
        @page { margin: 0; size: portrait; }
        .no-print { display: none !important; }
        body { background: white !important; padding: 0 !important; }
        .max-w-2xl { max-width: 100% !important; width: 100% !important; margin: 0 !important; padding: 0 !important; }
        .shadow-xl { box-shadow: none !important; }
        .animate-slide-in { animation: none !important; opacity: 1 !important; }
        .rounded-2xl, .rounded-xl, .rounded-2xl { border-radius: 0 !important; }
        .bg-primary {
            background-color: #6932BB !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .text-primary { color: #6932BB !important; -webkit-print-color-adjust: exact; }
        .text-white   { color: white !important; -webkit-print-color-adjust: exact; }
    }
</style>
@endsection
