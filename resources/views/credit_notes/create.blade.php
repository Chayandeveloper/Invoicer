@extends('layout')

@section('content')
    <div class="mb-10">
        <h1 class="text-4xl font-black text-gray-900 tracking-tight">Issue Credit Note</h1>
        <p class="text-sm text-gray-500 font-medium mt-1">Record returns or credits for a client</p>
    </div>

    <form action="{{ route('credit-notes.store') }}" method="POST" id="creditNoteForm">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8">
                <!-- Main Form Card -->
                <div class="bg-white p-8 sm:p-10 rounded-[2.5rem] shadow-2xl shadow-gray-200/50 border border-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Business Profile</label>
                            <select name="business_id" required class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-primary/20 transition-all">
                                @foreach($businesses as $business)
                                    <option value="{{ $business->id }}">{{ $business->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Client / Business Partner</label>
                            <select name="client_id" id="client_select" required class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-primary/20 transition-all">
                                <option value="">Select Client</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ $selectedClientId == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Credit Note #</label>
                            <input type="text" name="credit_note_number" value="CN-{{ time() }}" required
                                class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-primary/20 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Date</label>
                            <input type="date" name="credit_note_date" value="{{ date('Y-m-d') }}" required
                                class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-primary/20 transition-all">
                        </div>
                    </div>

                    <div class="h-px bg-gray-100 mb-8"></div>

                    <!-- Items Section -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-black uppercase tracking-widest text-gray-900">Items & Adjustments</h3>
                            <button type="button" onclick="addItem()" class="text-primary hover:text-primary-dark font-black text-[10px] uppercase tracking-widest flex items-center gap-2">
                                <i class="fas fa-plus-circle"></i> Add Line Item
                            </button>
                        </div>

                        <div id="items-container" class="space-y-4">
                            <!-- Items will be injected here -->
                        </div>
                    </div>
                </div>

                <!-- Notes Card -->
                <div class="bg-white p-8 rounded-[2.5rem] shadow-2xl shadow-gray-200/50 border border-gray-100">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1 block mb-3">Internal Notes / Reason for Credit</label>
                    <textarea name="notes" rows="3" placeholder="Explain why this credit is being issued..."
                        class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-primary/20 transition-all resize-none"></textarea>
                </div>
            </div>

            <!-- Sidebar / Summary -->
            <div class="space-y-6">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-2xl shadow-gray-200/50 border border-gray-100 sticky top-24">
                    <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-6 italic">Total Credit Value</h3>
                    <div class="flex items-end gap-1 mb-8">
                        <span class="text-gray-400 font-bold text-lg mb-1">₹</span>
                        <span id="grand-total" class="text-5xl font-black text-primary tracking-tighter leading-none">0.00</span>
                    </div>
                    
                    <button type="submit" class="w-full bg-indigo-600 text-white py-5 rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100">
                        Generate & Record
                    </button>
                    
                    <div class="mt-6 pt-6 border-t border-gray-50 flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-500 text-xs">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <p class="text-[10px] text-gray-400 font-bold leading-relaxed">
                            This credit will be automatically available for deduction on the client's next invoice payment.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Templates & Scripts -->
    <template id="item-row-template">
        <div class="item-row bg-gray-50/50 p-6 rounded-3xl border border-transparent hover:border-gray-100 transition-all relative group">
            <button type="button" onclick="this.closest('.item-row').remove(); calculateTotal();" class="absolute -right-2 -top-2 w-8 h-8 bg-white border border-gray-100 text-rose-500 rounded-full opacity-0 group-hover:opacity-100 transition-all shadow-lg flex items-center justify-center hover:bg-rose-500 hover:text-white">
                <i class="fas fa-times text-[10px]"></i>
            </button>
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-6">
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Description</label>
                    <input type="text" name="items[INDEX][description]" required list="suggestion-list"
                        class="w-full bg-white border-none rounded-xl px-4 py-3 text-xs font-bold focus:ring-2 focus:ring-primary/20 transition-all" placeholder="Enter item name...">
                </div>
                <div class="md:col-span-2">
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Qty</label>
                    <input type="number" step="0.01" name="items[INDEX][quantity]" required onchange="calculateTotal()"
                        class="w-full bg-white border-none rounded-xl px-4 py-3 text-xs font-bold focus:ring-2 focus:ring-primary/20 transition-all" value="1">
                </div>
                <div class="md:col-span-2">
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Rate</label>
                    <input type="number" step="0.01" name="items[INDEX][rate]" required onchange="calculateTotal()"
                        class="w-full bg-white border-none rounded-xl px-4 py-3 text-xs font-bold focus:ring-2 focus:ring-primary/20 transition-all" value="0.00">
                </div>
                <div class="md:col-span-2">
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block text-right">Amount</label>
                    <div class="py-3 text-right font-black text-gray-900 item-amount">₹0.00</div>
                </div>
            </div>
        </div>
    </template>

    <datalist id="suggestion-list">
        @foreach($suggestedItems as $item)
            <option value="{{ $item }}">
        @endforeach
    </datalist>

    @push('scripts')
    <script>
        let itemIndex = 0;

        function addItem() {
            const container = document.getElementById('items-container');
            const template = document.getElementById('item-row-template').innerHTML;
            const newRow = template.replace(/INDEX/g, itemIndex++);
            container.insertAdjacentHTML('beforeend', newRow);
            calculateTotal();
        }

        function calculateTotal() {
            let grandTotal = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const qty = parseFloat(row.querySelector('[name*="[quantity]"]').value) || 0;
                const rate = parseFloat(row.querySelector('[name*="[rate]"]').value) || 0;
                const amount = qty * rate;
                row.querySelector('.item-amount').innerText = '₹' + amount.toLocaleString('en-IN', {minimumFractionDigits: 2});
                grandTotal += amount;
            });
            document.getElementById('grand-total').innerText = grandTotal.toLocaleString('en-IN', {minimumFractionDigits: 2});
        }

        // Handle Client Change for Suggestions
        document.getElementById('client_select').addEventListener('change', function() {
            const clientId = this.value;
            if (!clientId) return;

            fetch(`{{ route('credit-notes.suggestions') }}?client_id=${clientId}`)
                .then(res => res.json())
                .then(data => {
                    const list = document.getElementById('suggestion-list');
                    list.innerHTML = '';
                    data.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item;
                        list.appendChild(opt);
                    });
                });
        });

        // Initialize with one item
        addItem();
    </script>
    @endpush
@endsection
