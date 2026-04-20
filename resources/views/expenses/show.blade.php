@extends('layout')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6 no-print">
            <div class="flex items-center gap-4">
                <a href="{{ route('expenses.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Expense Details</h1>
            </div>
            <div class="flex gap-3">
                <button onclick="window.print()"
                    class="bg-gray-900 text-white px-4 py-2 rounded-lg font-medium hover:bg-gray-800 transition shadow-sm flex items-center gap-2">
                    <i class="fas fa-print"></i> Print
                </button>
                <a href="{{ route('expenses.download', $expense->id) }}"
                    class="bg-primary text-white px-4 py-2 rounded-lg font-medium hover:bg-primary-dark transition shadow-sm flex items-center gap-2">
                    <i class="fas fa-file-pdf"></i> Download PDF
                </a>
                <a href="{{ route('expenses.edit', $expense->id) }}"
                    class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-200 transition shadow-sm flex items-center gap-2">
                    <i class="fas fa-edit"></i> Edit
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden" id="print-area">
            <div class="p-8 border-b border-gray-100 flex justify-between items-start">
                <div>
                    <div class="text-sm font-black text-gray-400 uppercase tracking-widest mb-1">Expense</div>
                    <div class="text-3xl font-black text-gray-900">₹{{ number_format($expense->amount, 2) }}</div>
                    @if($expense->tax_amount > 0)
                        <div class="text-xs font-bold text-gray-400 mt-1">
                            Includes ₹{{ number_format($expense->tax_amount, 2) }} Tax
                        </div>
                    @endif
                </div>
                <div class="text-right">
                    <div
                        class="inline-flex px-3 py-1 rounded-lg text-xs font-black uppercase tracking-widest border
                            {{ $expense->status === 'Paid' ? 'bg-green-50 text-green-600 border-green-100' : 'bg-amber-50 text-amber-600 border-amber-100' }}">
                        {{ $expense->status }}
                    </div>
                    <div class="mt-2 text-sm font-bold text-gray-500">
                        {{ \Carbon\Carbon::parse($expense->expense_date)->format('M d, Y') }}</div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-8 p-8 border-b border-gray-100">
                <div>
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Details</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="text-xs font-bold text-gray-400 uppercase tracking-wide">Category</div>
                            <div class="font-bold text-gray-900 text-lg">{{ $expense->category }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-gray-400 uppercase tracking-wide">Business Profile</div>
                            <div class="font-bold text-gray-900">{{ $expense->business->name ?? 'N/A' }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-gray-400 uppercase tracking-wide">Payment Method</div>
                            <div class="font-bold text-gray-900">{{ $expense->payment_method }}</div>
                        </div>
                        @if($expense->reference_number)
                            <div>
                                <div class="text-xs font-bold text-gray-400 uppercase tracking-wide">Reference #</div>
                                <div class="font-bold text-gray-900">{{ $expense->reference_number }}</div>
                            </div>
                        @endif
                    </div>
                </div>
                <div>
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Vendor Info</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="text-xs font-bold text-gray-400 uppercase tracking-wide">Vendor Name</div>
                            <div class="font-bold text-gray-900 text-lg">{{ $expense->vendor ?? '-' }}</div>
                        </div>
                        @if($expense->description)
                            <div>
                                <div class="text-xs font-bold text-gray-400 uppercase tracking-wide">Description / Notes</div>
                                <div class="text-gray-700 font-medium bg-gray-50 p-3 rounded-lg border border-gray-100 mt-1">
                                    {{ $expense->description }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($expense->receipt_path)
                <div class="p-8 bg-gray-50/50">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">Receipt Attachment</h3>
                        <a href="{{ asset('storage/' . $expense->receipt_path) }}" download
                            class="text-xs font-bold text-primary hover:text-primary-dark flex items-center gap-1">
                            <i class="fas fa-download"></i> Download File
                        </a>
                    </div>
                    <div class="bg-white border-2 border-dashed border-gray-200 rounded-xl p-4 flex justify-center">
                        @php
                            $extension = pathinfo($expense->receipt_path, PATHINFO_EXTENSION);
                        @endphp
                        @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                            <img src="{{ asset('storage/' . $expense->receipt_path) }}"
                                class="max-h-[500px] object-contain rounded-lg shadow-sm">
                        @elseif(strtolower($extension) === 'pdf')
                            <iframe src="{{ asset('storage/' . $expense->receipt_path) }}"
                                class="w-full h-[600px] rounded-lg border border-gray-200"></iframe>
                        @else
                            <div class="text-center py-10">
                                <i class="fas fa-file text-4xl text-gray-300 mb-2"></i>
                                <p class="text-gray-500 font-medium">File preview not available</p>
                                <a href="{{ asset('storage/' . $expense->receipt_path) }}" target="_blank"
                                    class="text-primary font-bold hover:underline mt-2 inline-block">
                                    Download File
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection