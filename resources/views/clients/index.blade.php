@extends('layout')

@section('content')
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-6">
        <div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight italic">Operations Hub <span class="text-primary">/ Clients</span></h1>
            <p class="text-sm text-gray-400 font-bold uppercase tracking-widest mt-1">Total Management & Directory</p>
        </div>
        <a href="{{ route('clients.create') }}"
            class="w-full sm:w-auto bg-primary text-white px-8 py-4 rounded-2xl font-black uppercase tracking-[0.2em] hover:bg-primary-dark transition shadow-2xl shadow-primary/20 flex items-center justify-center gap-3 text-[10px]">
            <i class="fas fa-plus-circle text-base"></i> Add New Client
        </a>
    </div>

    <!-- Status Filter Tabs Removed -->

    <!-- Mobile Card Layout -->
    <div class="sm:hidden space-y-4">
        @forelse($clients as $client)
            <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100 relative">
                <div class="flex items-center gap-4 mb-6">
                    <div class="h-14 w-14 flex-shrink-0 bg-white rounded-2xl overflow-hidden border-2 border-gray-100 p-1">
                        @if($client->logo)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($client->logo) }}" class="h-full w-full object-contain">
                        @else
                            <div class="h-full w-full flex items-center justify-center bg-gray-50">
                                <span class="text-primary font-black text-lg">{{ substr($client->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    <div>
                        <div class="font-black text-gray-900 text-base tracking-tight italic">{{ $client->name }}</div>
                        <div class="text-[10px] text-gray-400 font-black uppercase tracking-widest mt-0.5">{{ $client->industry ?: 'Sector Unset' }}</div>
                    </div>
                </div>

                <div class="space-y-3 mb-6 bg-gray-50/50 p-4 rounded-2xl">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Email</span>
                        <span class="text-xs font-bold text-gray-700">{{ $client->email }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Phone</span>
                        <span class="text-xs font-bold text-gray-700">{{ $client->phone ?: '---' }}</span>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex gap-2">
                        <a href="{{ route('clients.edit', $client->id) }}" class="p-3 bg-gray-50 text-gray-400 rounded-xl hover:text-amber-600 transition">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('clients.destroy', $client->id) }}" method="POST" onsubmit="return confirm('Confirm removal?')">
                            @csrf
                            @method('DELETE')
                            <button class="p-3 bg-gray-50 text-gray-400 rounded-xl hover:text-rose-600 transition">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>

                    <div class="relative group/more-mobile">
                        <button type="button" 
                            class="px-5 py-2.5 bg-primary text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-primary/20"
                            onclick="const menu = this.nextElementSibling; document.querySelectorAll('.mobile-menu').forEach(m => m !== menu && m.classList.add('hidden')); menu.classList.toggle('hidden')">
                            Quick Actions
                        </button>
                        <div class="mobile-menu hidden absolute right-0 bottom-full mb-3 w-60 bg-white rounded-2xl shadow-2xl border border-gray-100 py-3 z-[100] animate-slide-up origin-bottom-right">
                            <a href="{{ route('invoices.index', ['client_id' => $client->id]) }}" class="flex items-center gap-3 px-4 py-2.5 text-xs font-bold text-gray-600 hover:bg-indigo-50 transition">
                                <i class="fas fa-file-invoice text-indigo-400 w-4"></i> See All Invoices
                            </a>
                            <a href="{{ route('quotations.index', ['client_id' => $client->id]) }}" class="flex items-center gap-3 px-4 py-2.5 text-xs font-bold text-gray-600 hover:bg-indigo-50 transition border-b border-gray-50 pb-3 mb-1">
                                <i class="fas fa-file-alt text-indigo-400 w-4"></i> See All Quotations
                            </a>
                            <a href="{{ route('invoices.create', ['client_id' => $client->id]) }}" class="flex items-center gap-3 px-4 py-2.5 text-xs font-bold text-gray-600 hover:bg-emerald-50 transition">
                                <i class="fas fa-plus text-emerald-400 w-4"></i> Create New Invoice
                            </a>
                            <a href="{{ route('quotations.create', ['client_id' => $client->id]) }}" class="flex items-center gap-3 px-4 py-2.5 text-xs font-bold text-gray-600 hover:bg-amber-50 transition">
                                <i class="fas fa-plus text-amber-400 w-4"></i> Create New Quotation
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-[2.5rem] p-12 text-center border border-dashed border-gray-200">
                <i class="fas fa-users-slash text-4xl text-gray-200 mb-4"></i>
                <p class="font-black text-gray-400 uppercase tracking-widest text-xs italic">Directory is Empty</p>
            </div>
        @endforelse
    </div>

    <!-- Desktop Table Layout -->
    <div class="hidden sm:block bg-white shadow-2xl shadow-gray-200/50 rounded-[2.5rem] border border-gray-100">
        <div class="overflow-visible">
            <table class="w-full text-left text-sm text-gray-600 min-w-[800px]">
                <thead class="bg-gray-50/50 border-b border-gray-100">
                    <tr class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                        <th class="px-8 py-6">Partner Identity</th>
                        <th class="px-8 py-6">Contact Channels</th>
                        <th class="px-8 py-6 text-right">Operations</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($clients as $client)
                        <tr class="group hover:bg-gray-50/50 transition-all duration-300 relative hover:z-50">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="h-14 w-14 flex-shrink-0 bg-white rounded-2xl overflow-hidden border-2 border-gray-100 p-1 group-hover:border-primary/20 transition-all shadow-sm">
                                        @if($client->logo)
                                            <img src="{{ \Illuminate\Support\Facades\Storage::url($client->logo) }}" alt="{{ $client->name }}" class="h-full w-full object-contain">
                                        @else
                                            <div class="h-full w-full flex items-center justify-center bg-gray-50">
                                                <span class="text-primary font-black text-lg">{{ substr($client->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-black text-gray-900 text-base tracking-tight italic group-hover:text-primary transition-colors">{{ $client->name }}</div>
                                        <div class="text-[10px] text-gray-400 font-black uppercase tracking-widest mt-0.5">{{ $client->industry ?: 'Sector Unset' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex flex-col gap-1">
                                    <span class="font-bold text-gray-700 flex items-center gap-2"><i class="far fa-envelope text-[10px] text-gray-300"></i> {{ $client->email }}</span>
                                    <span class="text-[11px] font-bold text-gray-400 flex items-center gap-2"><i class="fas fa-phone-alt text-[10px] text-gray-300"></i> {{ $client->phone ?: '---' }}</span>
                                </div>
                            </td>

                            <td class="px-8 py-6 text-right">
                                <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-2 group-hover:translate-x-0">
                                    <a href="{{ route('clients.edit', $client->id) }}"
                                        class="p-2.5 text-gray-400 hover:text-amber-600 transition bg-gray-50 rounded-xl hover:bg-amber-50"
                                        title="Modify">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <div class="relative group/more text-left">
                                        <button type="button" 
                                            class="flex items-center gap-2 px-4 py-2 text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-primary transition bg-gray-50 rounded-xl hover:bg-primary/5 border border-transparent hover:border-primary/10 shadow-sm"
                                            onclick="this.nextElementSibling.classList.toggle('hidden')">
                                            <span>Quick Actions</span>
                                            <i class="fas fa-chevron-down text-[8px] transition-transform group-hover/more:rotate-180"></i>
                                        </button>
                                        <div class="hidden absolute right-0 bottom-full mb-2 w-64 bg-white rounded-2xl shadow-2xl border border-gray-100 py-4 z-[100] animate-slide-up origin-bottom-right overflow-hidden">
                                            <div class="px-5 py-2 mb-2">
                                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Client History</p>
                                            </div>
                                            <a href="{{ route('invoices.index', ['client_id' => $client->id]) }}" class="group/item flex items-center gap-4 px-5 py-3 hover:bg-primary/5 transition-colors">
                                                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 group-hover/item:bg-primary group-hover/item:text-white transition-all">
                                                    <i class="fas fa-file-invoice text-xs"></i>
                                                </div>
                                                <div>
                                                    <div class="text-[11px] font-black text-gray-900 uppercase tracking-tighter">See All Invoices</div>
                                                    <div class="text-[9px] text-gray-400 font-bold italic">Review billing history</div>
                                                </div>
                                            </a>
                                            <a href="{{ route('quotations.index', ['client_id' => $client->id]) }}" class="group/item flex items-center gap-4 px-5 py-3 hover:bg-primary/5 transition-colors border-b border-gray-50">
                                                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 group-hover/item:bg-primary group-hover/item:text-white transition-all">
                                                    <i class="fas fa-file-alt text-xs"></i>
                                                </div>
                                                <div>
                                                    <div class="text-[11px] font-black text-gray-900 uppercase tracking-tighter">See All Quotations</div>
                                                    <div class="text-[9px] text-gray-400 font-bold italic">Review proposals</div>
                                                </div>
                                            </a>
                                            
                                            <div class="px-5 py-2 my-2">
                                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">New Entry</p>
                                            </div>
                                            <a href="{{ route('invoices.create', ['client_id' => $client->id]) }}" class="group/item flex items-center gap-4 px-5 py-3 hover:bg-emerald-50 transition-colors">
                                                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-400 group-hover/item:bg-emerald-600 group-hover/item:text-white transition-all">
                                                    <i class="fas fa-plus text-xs"></i>
                                                </div>
                                                <div>
                                                    <div class="text-[11px] font-black text-gray-900 uppercase tracking-tighter">Create Invoice</div>
                                                    <div class="text-[9px] text-emerald-600 font-bold italic">Fast generation</div>
                                                </div>
                                            </a>
                                            <a href="{{ route('quotations.create', ['client_id' => $client->id]) }}" class="group/item flex items-center gap-4 px-5 py-3 hover:bg-amber-50 transition-colors">
                                                <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center text-amber-400 group-hover/item:bg-amber-600 group-hover/item:text-white transition-all">
                                                    <i class="fas fa-plus text-xs"></i>
                                                </div>
                                                <div>
                                                    <div class="text-[11px] font-black text-gray-900 uppercase tracking-tighter">Create Quotation</div>
                                                    <div class="text-[9px] text-amber-600 font-bold italic">Draft a proposal</div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>

                                    <form action="{{ route('clients.destroy', $client->id) }}" method="POST"
                                        class="inline" onsubmit="return confirm('Confirm permanent removal?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-2.5 text-gray-400 hover:text-rose-600 transition bg-gray-50 rounded-xl hover:bg-rose-50"
                                            title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-8 py-32 text-center text-gray-400">
                                <div class="flex flex-col items-center">
                                    <div class="w-24 h-24 bg-gray-50 rounded-[2rem] flex items-center justify-center mb-6">
                                        <i class="fas fa-users-slash text-4xl text-gray-200"></i>
                                    </div>
                                    <p class="font-black text-gray-500 uppercase tracking-[0.2em] text-xs italic">Directory is Empty</p>
                                    <p class="text-[10px] mt-2 font-medium">Add a client to begin operational tracking.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection