@extends('layout')

@section('content')
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-6">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Quotations</h1>
            @if(isset($filteredClient) && $filteredClient)
                <div class="flex items-center gap-2 mt-1">
                    <span class="bg-indigo-50 text-indigo-600 px-3 py-1 rounded-lg text-xs font-bold border border-indigo-100 flex items-center gap-2 italic">
                        <i class="fas fa-filter text-[10px]"></i>
                        Filtered for: <span class="font-black text-gray-900">{{ $filteredClient->name }}</span>
                    </span>
                    <a href="{{ route('quotations.index') }}" class="text-[10px] font-black text-gray-400 hover:text-rose-500 uppercase tracking-widest transition-colors flex items-center gap-1">
                        <i class="fas fa-times-circle"></i> Clear
                    </a>
                </div>
            @else
                <p class="text-sm text-gray-500 font-medium">Manage and track your business proposals</p>
            @endif
        </div>
        <a href="{{ route('quotations.create') }}"
            class="w-full sm:w-auto bg-primary text-white px-6 py-3 rounded-xl font-bold hover:bg-primary-dark transition shadow-lg shadow-primary/20 flex items-center justify-center gap-2 text-sm">
            <i class="fas fa-plus"></i> New Quotation
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto scrolling-touch">
            <table class="w-full text-left text-sm text-gray-600 min-w-[700px] sm:min-w-0 responsive-table">
                <thead
                    class="bg-gray-50 text-gray-900 font-black border-b border-gray-100 uppercase tracking-widest text-[10px]">
                    <tr>
                        <th class="px-6 py-5">Quotation Details</th>
                        <th class="px-6 py-5">Client</th>
                        <th class="px-6 py-5">Amount</th>
                        <th class="px-6 py-5">Status</th>
                        <th class="px-6 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($quotations as $quotation)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td data-label="Quotation Details" class="px-6 py-4">
                                            <div class="font-bold text-gray-900 italic tracking-tight">{{ $quotation->quotation_number }}
                                            </div>
                                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                                {{ \Carbon\Carbon::parse($quotation->quotation_date)->format('M d, Y') }}</div>
                                        </td>
                                        <td data-label="Client" class="px-6 py-4">
                                            <div class="font-black text-gray-800 tracking-tight">{{ $quotation->client_name }}</div>
                                        </td>
                                        <td data-label="Amount" class="px-6 py-4">
                                            <div class="font-black text-gray-900 text-base">Rs. {{ number_format($quotation->total, 2) }}</div>
                                        </td>
                                        <td data-label="Status" class="px-6 py-4">
                                            @php
                                                $badgeStyle = match (strtolower($quotation->status)) {
                                                    'accepted' => 'bg-green-50 text-green-600 border-green-100',
                                                    'invoiced' => 'bg-blue-50 text-blue-600 border-blue-100',
                                                    'rejected' => 'bg-red-50 text-red-600 border-red-100',
                                                    default => 'bg-amber-50 text-amber-600 border-amber-100'
                                                };
                                            @endphp
                         <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest border {{ $badgeStyle }}">
                                                {{ $quotation->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('quotations.show', $quotation->id) }}"
                                                    class="p-2.5 text-gray-400 hover:text-primary transition bg-gray-50 rounded-xl"
                                                    title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('quotations.download', $quotation->id) }}"
                                                    class="p-2.5 text-gray-400 hover:text-blue-600 transition bg-gray-50 rounded-xl"
                                                    title="Download PDF">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                                @if($quotation->status === 'Pending')
                                                    <form action="{{ route('quotations.updateStatus', $quotation->id) }}" method="POST"
                                                        class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="Accepted">
                                                        <button type="submit"
                                                            class="p-2.5 text-gray-400 hover:text-green-600 transition bg-gray-50 rounded-xl"
                                                            title="Accept">
                                                            <i class="fas fa-check-circle"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                @if($quotation->status === 'Accepted')
                                                    <form action="{{ route('quotations.convert', $quotation->id) }}" method="POST"
                                                        class="inline">
                                                        @csrf
                                                        <button type="submit"
                                                            class="p-2.5 text-gray-400 hover:text-blue-500 transition bg-gray-50 rounded-xl"
                                                            title="Convert to Invoice">
                                                            <i class="fas fa-file-invoice-dollar"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                <a href="{{ route('quotations.edit', $quotation->id) }}"
                                                    class="p-2.5 text-gray-400 hover:text-amber-600 transition bg-gray-50 rounded-xl"
                                                    title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('quotations.destroy', $quotation->id) }}" method="POST"
                                                    class="inline" onsubmit="return confirm('Are you sure you want to delete this quotation?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="p-2.5 text-gray-400 hover:text-red-600 transition bg-gray-50 rounded-xl"
                                                        title="Delete">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center text-gray-400">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-6">
                                        <i class="fas fa-file-contract text-3xl text-gray-200"></i>
                                    </div>
                                    <p class="font-black text-gray-500 uppercase tracking-widest text-xs">No quotations found
                                    </p>
                                    <p class="text-[10px] mt-2 font-medium">Create your first quotation to get started.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection