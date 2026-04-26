@extends('layout')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('payments.index') }}" class="p-2 bg-white rounded-xl border border-gray-100 shadow-sm text-gray-400 hover:text-primary transition">
                <i class="fas fa-chevron-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Record Payment</h1>
                <p class="text-sm text-gray-500 font-medium">Log a new client payment receipt</p>
            </div>
        </div>

        <div class="bg-white p-6 sm:p-10 rounded-2xl border border-gray-100 shadow-sm">
            <form action="{{ route('payments.store') }}" method="POST" class="space-y-8">
                @csrf

                <div class="space-y-6">
                    <!-- Receipt Number & Date -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Receipt Number</label>
                            <input type="text" name="receipt_number" value="REC-{{ strtoupper(Str::random(6)) }}" required
                                class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-black focus:ring-primary focus:border-primary p-4">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Payment Date</label>
                            <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required
                                class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4">
                        </div>
                    </div>

                    <!-- Invoice & Amount -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Link to Invoice</label>
                            <select name="invoice_id" id="invoice_id" onchange="updateFromInvoice()"
                                class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4">
                                <option value="">-- Manual Entry / No Invoice --</option>
                                @foreach($invoices as $invoice)
                                    <option value="{{ $invoice->id }}" 
                                        data-total="{{ $invoice->total }}" 
                                        data-balance="{{ $invoice->balance }}"
                                        data-client-name="{{ $invoice->client_name }}"
                                        data-client-logo="{{ $invoice->logo }}"
                                        data-business-id="{{ $invoice->business_profile }}"
                                        {{ (isset($selectedInvoice) && $selectedInvoice->id == $invoice->id) ? 'selected' : '' }}>
                                        {{ $invoice->invoice_number }} (Bal: Rs. {{ number_format($invoice->balance, 2) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Amount Received (Rs.)</label>
                            <input type="number" name="amount" id="amount" step="0.01" required
                                value="{{ isset($selectedInvoice) ? $selectedInvoice->balance : '' }}"
                                class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-black focus:ring-primary focus:border-primary p-4 text-primary"
                                oninput="updateBreakage()">
                            
                            <div id="balance-summary" class="hidden mt-3 p-3 bg-primary/5 rounded-xl border border-primary/10">
                                <div class="flex justify-between text-[10px] font-black uppercase tracking-tight">
                                    <span class="text-gray-400">Invoice Total</span>
                                    <span class="text-gray-900" id="summary-total">Rs. 0.00</span>
                                </div>
                                <div class="flex justify-between text-[10px] font-black uppercase tracking-tight mt-1">
                                    <span class="text-gray-400">Already Paid</span>
                                    <span class="text-green-600" id="summary-paid">Rs. 0.00</span>
                                </div>
                                <div class="flex justify-between text-[10px] font-black uppercase tracking-tight mt-1 pt-1 border-t border-primary/10">
                                    <span class="text-gray-500">Remaining</span>
                                    <span class="text-primary" id="summary-balance">Rs. 0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Method & Reference -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Payment Method</label>
                            <select name="payment_method" required
                                class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4">
                                <option value="UPI / PhonePe / GPay">UPI / PhonePe / GPay</option>
                                <option value="Bank Transfer">Bank Transfer (IMPS/NEFT)</option>
                                <option value="Cash">Cash</option>
                                <option value="Cheque">Cheque</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Reference / TRX ID</label>
                            <input type="text" name="reference_number" placeholder="Optional reference #"
                                class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4">
                        </div>
                    </div>

                    <!-- Business Selection -->
                    <div class="border-t border-gray-50 pt-6">
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Issuing Business (Receipt From)</label>
                        <select name="business_id" id="business_id" required
                            class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4">
                            <option value="" disabled {{ !isset($selectedInvoice) ? 'selected' : '' }}>-- Select Issuing Business --</option>
                            @foreach($businesses as $business)
                                <option value="{{ $business->id }}" {{ (isset($selectedInvoice) && $selectedInvoice->business_profile == $business->id) ? 'selected' : '' }}>
                                    {{ $business->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-[10px] text-gray-400 italic ml-1">You must select a business profile to issue this receipt.</p>
                    </div>

                    <div class="border-t border-gray-50 pt-6">
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Client Selection (Received From)</label>
                        
                        <!-- Credit Note Section -->
                        <div id="credit-note-section" class="hidden mb-6 bg-indigo-50/30 p-6 rounded-[2rem] border-2 border-dashed border-indigo-100">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-[10px]">
                                        <i class="fas fa-undo-alt"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-black text-gray-900 leading-none">Available Credit</h3>
                                        <p class="text-[10px] text-indigo-600 font-bold mt-1">Client has ₹<span id="available-credit-display">0.00</span> in credit notes</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="use_credit" id="use_credit_checkbox" value="1" class="sr-only peer" onchange="toggleCreditApplication()">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                    </label>
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Apply Credit</span>
                                </div>
                            </div>

                            <div id="credit-amount-input-div" class="hidden animate-slide-up">
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Amount to Deduct from Credit</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-xs">₹</span>
                                    <input type="number" name="credit_amount" id="credit_amount" step="0.01" 
                                        class="w-full border-gray-100 bg-white rounded-xl text-xs font-black focus:ring-primary focus:border-primary p-4 pl-8"
                                        placeholder="0.00" oninput="updateBreakage()">
                                </div>
                                <p class="mt-2 text-[9px] text-gray-400 italic">This amount will be deducted from client's credit balance and applied to this invoice.</p>
                                
                                <div id="credit-breakage-summary" class="hidden mt-4 p-4 bg-indigo-600 rounded-2xl text-white shadow-lg shadow-indigo-200">
                                    <div class="flex justify-between items-center">
                                        <span class="text-[10px] font-black uppercase tracking-widest opacity-70">Net Cash Payable</span>
                                        <span class="text-xl font-black" id="net-cash-display">₹ 0.00</span>
                                    </div>
                                    <p class="text-[9px] font-bold opacity-60 mt-1">This is the actual cash/bank amount you should collect.</p>
                                </div>
                            </div>
                        </div>
                        <select name="client_id" id="client_select" onchange="populateFromClient()"
                            class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4 mb-4">
                            <option value="">-- Manual Entry / No Client --</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" data-name="{{ $client->name }}" data-logo="{{ $client->logo }}" data-credit="{{ $client->available_credit }}">
                                    {{ $client->name }}
                                </option>
                            @endforeach
                        </select>

                        <div class="grid grid-cols-1 gap-6">
                            <input type="hidden" name="client_logo" id="client_logo">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Client Name (For Receipt)</label>
                                <input type="text" name="client_name" id="client_name" placeholder="Enter name if not selected" 
                                    class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Internal Notes</label>
                        <textarea name="notes" rows="3" placeholder="Any additional internal details..."
                            class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-medium focus:ring-primary focus:border-primary p-4"></textarea>
                    </div>
                </div>

                <div class="flex justify-center sm:justify-end gap-4 pt-8 border-t border-gray-100">
                    <a href="{{ route('payments.index') }}" class="px-8 py-4 text-gray-400 font-bold hover:text-gray-600 transition text-xs">Cancel</a>
                    <button type="submit"
                        class="bg-primary text-white px-12 py-4 rounded-xl font-black hover:bg-primary-dark transition shadow-xl shadow-primary/20 uppercase tracking-widest text-xs">
                        Generate Receipt
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function updateFromInvoice() {
            const select = document.getElementById('invoice_id');
            const amountInput = document.getElementById('amount');
            const clientNameInput = document.getElementById('client_name');
            const clientLogoInput = document.getElementById('client_logo');
            const clientSelect = document.getElementById('client_select');
            const businessSelect = document.getElementById('business_id');
            
            const summaryDiv = document.getElementById('balance-summary');
            const summaryTotal = document.getElementById('summary-total');
            const summaryPaid = document.getElementById('summary-paid');
            const summaryBalance = document.getElementById('summary-balance');

            const selectedOption = select.options[select.selectedIndex];
            
            if (selectedOption.value) {
                // Auto-fill from Invoice
                const total = parseFloat(selectedOption.getAttribute('data-total')) || 0;
                const balance = parseFloat(selectedOption.getAttribute('data-balance')) || 0;
                const clientName = selectedOption.getAttribute('data-client-name');
                const clientLogo = selectedOption.getAttribute('data-client-logo');
                const businessId = selectedOption.getAttribute('data-business-id');

                amountInput.value = balance.toFixed(2);
                if (clientName) clientNameInput.value = clientName;
                if (clientLogo) clientLogoInput.value = clientLogo;
                
                if (businessId) {
                    businessSelect.value = businessId;
                }
                
                // Show Summary
                summaryDiv.classList.remove('hidden');
                summaryTotal.innerText = 'Rs. ' + total.toLocaleString(undefined, {minimumFractionDigits: 2});
                summaryPaid.innerText = 'Rs. ' + (total - balance).toLocaleString(undefined, {minimumFractionDigits: 2});
                summaryBalance.innerText = 'Rs. ' + balance.toLocaleString(undefined, {minimumFractionDigits: 2});

                if (clientSelect) clientSelect.value = "";
            } else {
                // Clear fields if Manual Entry is selected
                amountInput.value = '';
                clientNameInput.value = '';
                clientLogoInput.value = '';
                if (businessSelect) businessSelect.value = '';
                summaryDiv.classList.add('hidden');
            }
        }

        function populateFromClient() {
            const select = document.getElementById('client_select');
            const invoiceSelect = document.getElementById('invoice_id');
            const selectedOption = select.options[select.selectedIndex];
            
            const creditSection = document.getElementById('credit-note-section');
            const creditDisplay = document.getElementById('available-credit-display');
            const creditInput = document.getElementById('credit_amount');
            const checkbox = document.getElementById('use_credit_checkbox');

            if (selectedOption.value) {
                document.getElementById('client_name').value = selectedOption.getAttribute('data-name');
                document.getElementById('client_logo').value = selectedOption.getAttribute('data-logo');
                
                const credit = parseFloat(selectedOption.getAttribute('data-credit')) || 0;
                if (credit > 0) {
                    creditSection.classList.remove('hidden');
                    creditDisplay.innerText = credit.toLocaleString(undefined, {minimumFractionDigits: 2});
                    creditInput.max = credit;
                } else {
                    creditSection.classList.add('hidden');
                    checkbox.checked = false;
                    toggleCreditApplication();
                }

                // Clear invoice selection if manual client is picked
                invoiceSelect.value = "";
                document.getElementById('amount').value = '';
            } else {
                document.getElementById('client_name').value = '';
                document.getElementById('client_logo').value = '';
                creditSection.classList.add('hidden');
                checkbox.checked = false;
                toggleCreditApplication();
            }
        }

        function toggleCreditApplication() {
            const checkbox = document.getElementById('use_credit_checkbox');
            const inputDiv = document.getElementById('credit-amount-input-div');
            const creditInput = document.getElementById('credit_amount');
            
            if (checkbox.checked) {
                inputDiv.classList.remove('hidden');
            } else {
                inputDiv.classList.add('hidden');
                creditInput.value = '';
            }
            updateBreakage();
        }

        function updateBreakage() {
            const amountInput = document.getElementById('amount');
            const creditInput = document.getElementById('credit_amount');
            const checkbox = document.getElementById('use_credit_checkbox');
            const invoiceSelect = document.getElementById('invoice_id');
            const selectedInvoice = invoiceSelect.options[invoiceSelect.selectedIndex];
            
            const breakageDiv = document.getElementById('credit-breakage-summary');
            const netCashDisplay = document.getElementById('net-cash-display');
            
            if (checkbox.checked && creditInput.value > 0) {
                const amount = parseFloat(amountInput.value) || 0;
                const credit = parseFloat(creditInput.value) || 0;
                
                if (amount > 0) {
                    breakageDiv.classList.remove('hidden');
                    const net = Math.max(0, amount - credit);
                    netCashDisplay.innerText = '₹ ' + net.toLocaleString(undefined, {minimumFractionDigits: 2});
                } else {
                    breakageDiv.classList.add('hidden');
                }
            } else {
                breakageDiv.classList.add('hidden');
            }
        }

        // Extend updateFromInvoice to also check credit for the client linked to invoice
        const originalUpdateFromInvoice = updateFromInvoice;
        updateFromInvoice = function() {
            originalUpdateFromInvoice();
            
            const select = document.getElementById('invoice_id');
            const selectedOption = select.options[select.selectedIndex];
            const clientSelect = document.getElementById('client_select');
            const creditSection = document.getElementById('credit-note-section');
            const creditDisplay = document.getElementById('available-credit-display');
            const creditInput = document.getElementById('credit_amount');
            const checkbox = document.getElementById('use_credit_checkbox');

            if (selectedOption.value) {
                const clientName = selectedOption.getAttribute('data-client-name');
                // Find client by name in clientSelect to get credit info
                let clientFound = false;
                for (let i = 0; i < clientSelect.options.length; i++) {
                    if (clientSelect.options[i].getAttribute('data-name') === clientName) {
                        const credit = parseFloat(clientSelect.options[i].getAttribute('data-credit')) || 0;
                        if (credit > 0) {
                            creditSection.classList.remove('hidden');
                            creditDisplay.innerText = credit.toLocaleString(undefined, {minimumFractionDigits: 2});
                            creditInput.max = credit;
                            clientFound = true;
                        }
                        break;
                    }
                }
                if (!clientFound) {
                    creditSection.classList.add('hidden');
                    checkbox.checked = false;
                    toggleCreditApplication();
                }
            }
        }
    </script>
@endsection
