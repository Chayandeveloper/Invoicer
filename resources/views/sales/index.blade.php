@extends('layout')

@section('content')
    @php
        $statusColors = [
            'active' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
            'archived' => 'bg-gray-100 text-gray-500 border-gray-200',
            'lead' => 'bg-amber-50 text-amber-600 border-amber-100'
        ];
    @endphp
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-10 gap-6">
        <div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight">
                Business Partners
            </h1>
            <p class="text-sm text-gray-500 font-medium mt-1">Manage your high-value clients and financial history</p>
        </div>
        <div class="flex gap-3 w-full lg:w-auto">
            <a href="{{ route('clients.create') }}"
                class="flex-1 lg:flex-none bg-indigo-600 text-white px-8 py-3.5 rounded-2xl font-bold hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 flex items-center justify-center gap-2 text-sm">
                <i class="fas fa-plus-circle"></i> New Client
            </a>
        </div>
    </div>

    <!-- Summary Stats Bar -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-indigo-600 p-8 rounded-[2.5rem] shadow-xl shadow-indigo-100 text-white relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-200 mb-2">Total Invoiced</p>
                <h2 class="text-3xl font-black italic">₹{{ number_format($totalInvoiced, 2) }}</h2>
            </div>
            <i class="fas fa-file-invoice-dollar absolute -right-4 -bottom-4 text-7xl text-white/10 group-hover:scale-110 transition-transform"></i>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] shadow-xl shadow-gray-100 border border-gray-50 relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-rose-400 mb-2">Pending Payments</p>
                <h2 class="text-3xl font-black italic text-rose-600">₹{{ number_format($pendingAmount, 2) }}</h2>
            </div>
            <i class="fas fa-clock-rotate-left absolute -right-4 -bottom-4 text-7xl text-rose-50 group-hover:scale-110 transition-transform"></i>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] shadow-xl shadow-gray-100 border border-gray-100 relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-2">Total Clients</p>
                <h2 class="text-3xl font-black italic text-gray-900">{{ count($clients) }}</h2>
            </div>
            <i class="fas fa-users absolute -right-4 -bottom-4 text-7xl text-gray-50 group-hover:scale-110 transition-transform"></i>
        </div>
    </div>

    <!-- Status Tabs (Only show if not strictly on Prospects page) -->

        <div class="mb-8 flex flex-wrap gap-2 p-1.5 bg-gray-100/50 rounded-2xl w-fit border border-gray-200/50">
            @foreach(['active' => 'Active Clients', 'lead' => 'Leads', 'archived' => 'Archived', 'all' => 'All'] as $key => $label)
                <a href="{{ route('sales.clients', ['status' => $key]) }}" 
                   class="px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all {{ $status === $key ? 'bg-white text-indigo-600 shadow-sm border border-gray-100' : 'text-gray-500 hover:text-gray-700' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

    <!-- Mobile Card Layout -->
    <div class="lg:hidden space-y-4">
        @forelse($clients as $client)
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 relative">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 flex-shrink-0 bg-indigo-50 rounded-2xl flex items-center justify-center overflow-hidden border border-indigo-100">
                            @if($client->logo)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($client->logo) }}" class="h-full w-full object-contain">
                            @else
                                <span class="text-indigo-600 font-black">{{ substr($client->name, 0, 1) }}</span>
                            @endif
                        </div>
                        <div>
                            <div class="font-black text-gray-900 text-base tracking-tight">{{ $client->name }}</div>
                            <div class="text-[10px] text-gray-400 font-black uppercase tracking-widest">{{ $client->industry ?: 'Sector Unset' }}</div>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-tighter border {{ $statusColors[$client->status] ?? $statusColors['active'] }}">
                        {{ $client->status }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6 bg-gray-50/50 p-4 rounded-2xl">
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Invoiced</p>
                        <p class="font-black text-gray-900">₹{{ number_format($client->total_invoiced, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Pending</p>
                        <p class="font-black {{ $client->pending_balance > 0 ? 'text-rose-600' : 'text-emerald-600' }}">₹{{ number_format($client->pending_balance, 2) }}</p>
                    </div>
                </div>

                <div class="flex items-center justify-between gap-3">
                    <div class="flex gap-2">
                        <a href="{{ route('clients.edit', $client->id) }}" class="p-3 bg-gray-50 text-gray-400 rounded-xl hover:text-indigo-600 transition">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('clients.destroy', $client->id) }}" method="POST" onsubmit="return confirm('Delete permanently?')">
                            @csrf
                            @method('DELETE')
                            <button class="p-3 bg-gray-50 text-gray-400 rounded-xl hover:text-rose-600 transition">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>

                    <div class="relative group/more-mobile">
                        <button type="button" 
                            class="px-4 py-2.5 bg-indigo-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-indigo-200"
                            onclick="const menu = this.nextElementSibling; document.querySelectorAll('.mobile-menu').forEach(m => m !== menu && m.classList.add('hidden')); menu.classList.toggle('hidden')">
                            Actions
                        </button>
                        <div class="mobile-menu hidden absolute right-0 bottom-full mb-3 w-56 bg-white rounded-2xl shadow-2xl border border-gray-100 py-3 z-[100] animate-slide-up origin-bottom-right">
                            <a href="{{ route('invoices.index', ['client_id' => $client->id]) }}" class="flex items-center gap-3 px-4 py-2.5 text-xs font-bold text-gray-600 hover:bg-indigo-50 transition">
                                <i class="fas fa-file-invoice text-indigo-400 w-4"></i> Invoices
                            </a>
                            <a href="{{ route('quotations.index', ['client_id' => $client->id]) }}" class="flex items-center gap-3 px-4 py-2.5 text-xs font-bold text-gray-600 hover:bg-indigo-50 transition border-b border-gray-50 pb-3 mb-1">
                                <i class="fas fa-file-alt text-indigo-400 w-4"></i> Quotations
                            </a>
                            <a href="{{ route('invoices.create', ['client_id' => $client->id]) }}" class="flex items-center gap-3 px-4 py-2.5 text-xs font-bold text-gray-600 hover:bg-emerald-50 transition">
                                <i class="fas fa-plus text-emerald-400 w-4"></i> New Invoice
                            </a>
                            <a href="{{ route('quotations.create', ['client_id' => $client->id]) }}" class="flex items-center gap-3 px-4 py-2.5 text-xs font-bold text-gray-600 hover:bg-amber-50 transition">
                                <i class="fas fa-plus text-amber-400 w-4"></i> New Quotation
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl p-12 text-center border border-dashed border-gray-200">
                <i class="fas fa-users-slash text-4xl text-gray-200 mb-4"></i>
                <p class="font-black text-gray-400 uppercase tracking-widest text-xs italic">No Clients Found</p>
            </div>
        @endforelse
    </div>

    <!-- Desktop Table Layout -->
    <div class="hidden lg:block bg-white shadow-2xl shadow-gray-200/50 rounded-[2.5rem] border border-gray-100">
        <div class="overflow-visible">
            <table class="w-full text-left text-sm text-gray-600 min-w-[1000px]">
                <thead>
                    <tr class="bg-gray-50/50 text-gray-400 font-black uppercase tracking-widest text-[10px] border-b border-gray-50">
                        <th class="px-8 py-6">Identity</th>
                        <th class="px-8 py-6">Status</th>
                        <th class="px-8 py-6">Industry</th>
                        <th class="px-8 py-6 text-indigo-600/80">Total Invoiced</th>
                        <th class="px-8 py-6 text-rose-500/80">Pending</th>
                        <th class="px-8 py-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($clients as $client)
                        <tr class="group hover:bg-indigo-50/30 transition-all duration-300 relative hover:z-50">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="h-14 w-14 flex-shrink-0 bg-white rounded-2xl overflow-hidden border-2 border-gray-100 p-1 group-hover:border-indigo-200 transition-colors">
                                        @if($client->logo)
                                            <img src="{{ \Illuminate\Support\Facades\Storage::url($client->logo) }}" alt="{{ $client->name }}" class="h-full w-full object-contain">
                                        @else
                                            <div class="h-full w-full flex items-center justify-center bg-indigo-50 rounded-xl">
                                                <span class="text-indigo-600 font-black text-lg">{{ substr($client->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-black text-gray-900 text-base tracking-tight group-hover:text-indigo-700 transition-colors">{{ $client->name }}</div>
                                        <div class="text-xs text-gray-400 font-medium mt-0.5 flex items-center gap-2">
                                            <span class="flex items-center gap-1"><i class="far fa-envelope text-[10px]"></i> {{ $client->email ?: 'No email' }}</span>
                                            <span class="text-gray-200">|</span>
                                            <span class="flex items-center gap-1"><i class="fas fa-phone-alt text-[10px]"></i> {{ $client->phone ?: 'No phone' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-tighter border {{ $statusColors[$client->status] ?? $statusColors['active'] }}">
                                    @if($client->status === 'lead' && $client->invoices()->count() > 0)
                                        Customer
                                    @else
                                        {{ $client->status }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-gray-500 font-bold italic text-xs capitalize">{{ $client->industry ?: 'Uncategorized' }}</span>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-gray-900 font-black text-sm tracking-tighter">₹{{ number_format($client->total_invoiced, 2) }}</span>
                                <div class="text-[10px] text-gray-400 font-medium">{{ $client->invoices()->count() }} Invoices</div>
                            </td>
                            <td class="px-8 py-6">
                                @php $pending = $client->pending_balance; @endphp
                                <span class="font-black text-sm tracking-tighter {{ $pending > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                    ₹{{ number_format($pending, 2) }}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <a href="{{ route('clients.edit', $client->id) }}"
                                        class="p-2.5 text-gray-400 hover:text-indigo-600 transition bg-gray-50 rounded-xl hover:bg-indigo-50"
                                        title="Edit Profile">
                                        <i class="fas fa-user-edit"></i>
                                    </a>
                                    
                                    <div class="relative group/more">
                                        <button type="button" 
                                            class="flex items-center gap-2 px-4 py-2 text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-indigo-600 transition bg-gray-50 rounded-xl hover:bg-indigo-50 border border-transparent hover:border-indigo-100 shadow-sm"
                                            onclick="this.nextElementSibling.classList.toggle('hidden')">
                                            <span>Quick Actions</span>
                                            <i class="fas fa-chevron-down text-[8px] transition-transform group-hover/more:rotate-180"></i>
                                        </button>
                                        <div class="hidden absolute right-0 bottom-full mb-2 w-64 bg-white rounded-2xl shadow-2xl border border-gray-100 py-4 z-[100] animate-slide-up origin-bottom-right overflow-hidden">
                                            <div class="px-5 py-2 mb-2">
                                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Client History</p>
                                            </div>
                                            <a href="{{ route('invoices.index', ['client_id' => $client->id]) }}" class="group/item flex items-center gap-4 px-5 py-3 hover:bg-indigo-50 transition-colors">
                                                <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-400 group-hover/item:bg-indigo-600 group-hover/item:text-white transition-all">
                                                    <i class="fas fa-file-invoice text-xs"></i>
                                                </div>
                                                <div>
                                                    <div class="text-[11px] font-black text-gray-900 uppercase tracking-tighter">See All Invoices</div>
                                                    <div class="text-[9px] text-gray-400 font-bold italic">Review billing history</div>
                                                </div>
                                            </a>
                                            <a href="{{ route('quotations.index', ['client_id' => $client->id]) }}" class="group/item flex items-center gap-4 px-5 py-3 hover:bg-indigo-50 transition-colors border-b border-gray-50">
                                                <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-400 group-hover/item:bg-indigo-600 group-hover/item:text-white transition-all">
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

                                            <div class="mt-4 pt-4 border-t border-gray-50 bg-gray-50/50">
                                                @if($client->status !== 'archived')
                                                    <form action="{{ route('clients.toggleStatus', $client->id) }}" method="POST" class="w-full px-5">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="archived">
                                                        <button type="submit" class="w-full flex items-center gap-3 py-2 text-[10px] font-black text-gray-400 hover:text-rose-600 uppercase tracking-widest transition-colors text-left">
                                                            <i class="fas fa-archive w-4"></i> Archive Client
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('clients.toggleStatus', $client->id) }}" method="POST" class="w-full px-5">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="active">
                                                        <button type="submit" class="w-full flex items-center gap-3 py-2 text-[10px] font-black text-emerald-600 hover:text-emerald-700 uppercase tracking-widest transition-colors text-left">
                                                            <i class="fas fa-undo w-4"></i> Restore Client
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <form action="{{ route('clients.destroy', $client->id) }}" method="POST"
                                        class="inline" onsubmit="return confirm('CRITICAL: Delete permanently?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-2.5 text-gray-400 hover:text-rose-600 transition bg-gray-50 rounded-xl hover:bg-rose-50">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-32 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-24 h-24 bg-indigo-50 rounded-[2rem] flex items-center justify-center mb-6">
                                        <i class="fas fa-users-slash text-4xl text-indigo-200"></i>
                                    </div>
                                    <h3 class="font-black text-gray-900 text-lg">No records found</h3>
                                    <p class="text-sm text-gray-400 font-medium max-w-xs mx-auto mt-2 italic">Try switching filters or add a new business partner to get started.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
