@extends('layout')

@section('content')
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-8 no-print">
        <div class="flex items-center gap-4">
            <a href="{{ route('credit-notes.index') }}"
               class="group flex items-center justify-center w-10 h-10 bg-white rounded-xl shadow-sm border border-gray-100 text-gray-400 hover:text-primary transition-all">
                <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
            </a>
            <div>
                <h1 class="text-xl font-black text-gray-900 tracking-tight">Credit Note Detail</h1>
                <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">#{{ $creditNote->credit_note_number }}</p>
            </div>
        </div>
        <div class="flex gap-3 w-full sm:w-auto">
            <a href="{{ route('credit-notes.download', $creditNote->id) }}"
               class="flex-1 sm:flex-none bg-primary text-white px-6 py-3 rounded-xl font-black hover:bg-primary-dark transition-all shadow-lg shadow-primary/20 flex items-center justify-center gap-2 text-xs uppercase tracking-widest">
                <i class="fas fa-file-pdf"></i> Download PDF
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-xl overflow-hidden max-w-4xl mx-auto">
        <div class="bg-primary px-8 py-7 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-black uppercase tracking-tighter">{{ $creditNote->business->name }}</h2>
                    <p class="text-white/60 text-[10px] font-bold uppercase tracking-widest mt-1">Official Credit Note</p>
                </div>
                <div class="text-right">
                    <p class="text-white/40 text-[9px] font-black uppercase tracking-widest">Date Issued</p>
                    <p class="text-lg font-black">{{ \Carbon\Carbon::parse($creditNote->credit_note_date)->format('d M, Y') }}</p>
                </div>
            </div>
        </div>

        <div class="p-8">
            <div class="grid grid-cols-2 gap-8 mb-10">
                <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-3">Issued To</p>
                    <p class="text-lg font-black text-gray-900">{{ $creditNote->client->name }}</p>
                    <p class="text-xs text-gray-500 font-bold mt-1 leading-relaxed">{{ $creditNote->client->address }}</p>
                </div>
                <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-3">Credit Status</p>
                    <div class="flex flex-col gap-2">
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-bold text-gray-500 uppercase">Original Total</span>
                            <span class="font-black text-gray-900">₹ {{ number_format($creditNote->total_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-bold text-gray-500 uppercase">Remaining Credit</span>
                            <span class="font-black text-primary">₹ {{ number_format($creditNote->remaining_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <table class="w-full text-left border-collapse mb-10">
                <thead>
                    <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                        <th class="py-4">Description</th>
                        <th class="py-4 text-center">Qty</th>
                        <th class="py-4 text-right">Rate</th>
                        <th class="py-4 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($creditNote->items as $item)
                        <tr>
                            <td class="py-4 font-bold text-gray-900">{{ $item->description }}</td>
                            <td class="py-4 text-center text-gray-500 font-bold">{{ $item->quantity }}</td>
                            <td class="py-4 text-right text-gray-500 font-bold">₹{{ number_format($item->rate, 2) }}</td>
                            <td class="py-4 text-right font-black text-gray-900">₹{{ number_format($item->amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="flex flex-col items-end gap-3">
                <div class="w-64 space-y-3">
                    <div class="flex justify-between text-sm font-bold text-gray-500 uppercase tracking-tight">
                        <span>Subtotal</span>
                        <span>₹{{ number_format($creditNote->total_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                        <span class="text-[10px] font-black text-primary uppercase tracking-widest">Credit Total</span>
                        <span class="text-3xl font-black text-primary tracking-tighter leading-none">₹{{ number_format($creditNote->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>

            @if($creditNote->notes)
                <div class="mt-10 pt-10 border-t border-gray-50">
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-3">Internal Notes</p>
                    <p class="text-xs text-gray-600 italic font-medium leading-relaxed">{{ $creditNote->notes }}</p>
                </div>
            @endif
        </div>
    </div>
@endsection
