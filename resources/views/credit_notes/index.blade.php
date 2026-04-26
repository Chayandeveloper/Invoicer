@extends('layout')

@section('content')
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-10 gap-6">
        <div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight">Credit Notes</h1>
            <p class="text-sm text-gray-500 font-medium mt-1">Manage customer credits and returns</p>
        </div>
        <div class="flex gap-3 w-full lg:w-auto">
            <a href="{{ route('credit-notes.create') }}"
                class="flex-1 lg:flex-none bg-primary text-white px-8 py-3.5 rounded-2xl font-bold hover:bg-primary-dark transition-all shadow-xl shadow-primary/20 flex items-center justify-center gap-2 text-sm">
                <i class="fas fa-plus-circle"></i> New Credit Note
            </a>
        </div>
    </div>

    <div class="bg-white shadow-2xl shadow-gray-200/50 rounded-[2.5rem] border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead>
                    <tr class="bg-gray-50/50 text-gray-400 font-black uppercase tracking-widest text-[10px] border-b border-gray-50">
                        <th class="px-8 py-6">Number</th>
                        <th class="px-8 py-6">Client</th>
                        <th class="px-8 py-6">Date</th>
                        <th class="px-8 py-6">Total Amount</th>
                        <th class="px-8 py-6">Remaining</th>
                        <th class="px-8 py-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($creditNotes as $cn)
                        <tr class="group hover:bg-gray-50/50 transition-colors">
                            <td class="px-8 py-6">
                                <span class="font-black text-gray-900 tracking-tighter">{{ $cn->credit_note_number }}</span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="font-bold text-gray-900">{{ $cn->client->name }}</div>
                                <div class="text-[10px] text-gray-400 font-medium">{{ $cn->business->name }}</div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="text-gray-500 font-medium">{{ \Carbon\Carbon::parse($cn->credit_note_date)->format('d M, Y') }}</div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="font-black text-gray-900">₹{{ number_format($cn->total_amount, 2) }}</span>
                            </td>
                            <td class="px-8 py-6">
                                <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-tighter {{ $cn->remaining_amount > 0 ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-gray-50 text-gray-400 border border-gray-100' }}">
                                    ₹{{ number_format($cn->remaining_amount, 2) }}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('credit-notes.download', $cn->id) }}" class="p-2.5 text-gray-400 hover:text-primary transition bg-gray-50 rounded-xl hover:bg-primary/5">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <a href="{{ route('credit-notes.show', $cn->id) }}" class="p-2.5 text-gray-400 hover:text-primary transition bg-gray-50 rounded-xl hover:bg-primary/5">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-20 text-center text-gray-400 font-medium italic">
                                No credit notes found. Create your first one to manage client returns.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
