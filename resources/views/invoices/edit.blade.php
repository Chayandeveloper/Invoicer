@extends('layout')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('invoices.show', $invoice->id) }}"
                class="p-2 bg-white rounded-xl border border-gray-100 shadow-sm text-gray-400 hover:text-primary transition">
                <i class="fas fa-chevron-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Edit Invoice</h1>
                <p class="text-sm text-gray-500 font-medium">Update professional invoice #{{ $invoice->invoice_number }}</p>
            </div>
        </div>

        <form action="{{ route('invoices.update', $invoice->id) }}" method="POST" class="space-y-8" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Sender Selection -->
            <div class="bg-white p-6 sm:p-8 rounded-2xl border border-gray-100 shadow-sm space-y-6">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-building text-sm"></i>
                    </div>
                    <h2 class="text-lg font-black text-gray-800 uppercase tracking-widest text-[11px]">From (Your Business)</h2>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Select Business Profile</label>
                        <select id="business_select" onchange="populateBusiness()"
                            class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4">
                            <option value="">-- Manual Entry --</option>
                            @foreach($businesses as $business)
                                <option value="{{ $business->id }}" data-name="{{ $business->name }}"
                                    data-address="{{ $business->address }}" data-website="{{ $business->website }}"
                                    data-phone="{{ $business->phone }}" data-bank="{{ $business->bank_details }}"
                                    data-logo="{{ $business->logo }}"
                                    {{ $invoice->sender_name == $business->name ? 'selected' : '' }}>
                                    {{ $business->name }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="logo" id="logo" value="{{ $invoice->logo }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Business Name</label>
                        <input type="text" name="sender_name" id="sender_name" value="{{ $invoice->sender_name }}" required
                            class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Website</label>
                        <input type="text" name="sender_website" id="sender_website" value="{{ $invoice->sender_website }}"
                            class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Phone Number</label>
                        <input type="text" name="sender_phone" id="sender_phone" value="{{ $invoice->sender_phone }}"
                            class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Address</label>
                        <textarea name="sender_address" id="sender_address" rows="2"
                            class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4">{{ $invoice->sender_address }}</textarea>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Bank Details</label>
                        <textarea name="bank_details" id="bank_details" rows="3"
                            class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4">{{ $invoice->bank_details }}</textarea>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Payment Link (Optional)</label>
                    <input type="text" name="payment_qr_link" id="payment_qr_link" value="{{ $invoice->payment_qr_link }}"
                        class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4"
                        placeholder="UPI ID or Payment URL">
                    
                    <div class="mt-6">
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Payment QR Image (Optional)</label>
                        <input type="file" name="payment_qr_image" id="payment_qr_image" accept="image/*"
                            class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4">
                        <p class="text-[9px] text-gray-400 mt-2 ml-1 font-bold italic uppercase tracking-tighter">Upload a pre-generated QR code image to replace the link.</p>
                        
                        @if($invoice->payment_qr_image)
                            <div class="mt-4">
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Current QR Image</label>
                                <img src="{{ asset('storage/' . $invoice->payment_qr_image) }}" class="w-32 h-32 object-contain rounded-lg border border-gray-100 bg-gray-50 p-2">
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Client Details -->
                <div class="bg-white p-6 sm:p-8 rounded-2xl border border-gray-100 shadow-sm space-y-6">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 bg-amber-50 text-amber-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user-tie text-sm"></i>
                        </div>
                        <h2 class="text-lg font-black text-gray-800 uppercase tracking-widest text-[11px]">Bill To (Client)</h2>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Select Client Type</label>
                        <select id="client_select" onchange="populateClient()"
                            class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4">
                            <option value="">-- Select Client --</option>
                            <option value="manual">-- Manual Entry --</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" data-name="{{ $client->name }}"
                                    data-address="{{ $client->address }}" data-phone="{{ $client->phone }}"
                                    data-logo="{{ $client->logo }}"
                                    {{ $invoice->client_name == $client->name ? 'selected' : '' }}>
                                    {{ $client->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-4" id="client_details_fields" style="{{ $invoice->client_name ? 'display: block;' : 'display: none;' }}">
                        <input type="hidden" name="client_logo" id="client_logo" value="{{ $invoice->client_logo }}">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Client Name</label>
                            <input type="text" name="client_name" id="client_name" value="{{ $invoice->client_name }}" required
                                class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Phone Number</label>
                            <input type="text" name="client_phone" id="client_phone" value="{{ $invoice->client_phone }}"
                                class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Address</label>
                            <textarea name="client_address" id="client_address" rows="3"
                                class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4">{{ $invoice->client_address }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Invoice Details -->
                <div class="bg-white p-6 sm:p-8 rounded-2xl border border-gray-100 shadow-sm space-y-6 text-gray-800">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 bg-purple-50 text-purple-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-file-invoice text-sm"></i>
                        </div>
                        <h2 class="text-lg font-black text-gray-800 uppercase tracking-widest text-[11px]">Invoice Details</h2>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Invoice Number</label>
                        <input type="text" name="invoice_number" value="{{ $invoice->invoice_number }}" required
                            class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-black tracking-widest focus:ring-primary focus:border-primary p-4 text-primary italic">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Issue Date</label>
                            <input type="date" name="invoice_date" value="{{ $invoice->invoice_date }}" required
                                class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Due Date</label>
                            <input type="date" name="due_date" value="{{ $invoice->due_date }}"
                                class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Global Tax Rate (%)</label>
                        <input type="number" name="tax_rate" id="tax_rate" value="{{ $invoice->tax_rate }}" min="0" step="0.01"
                            oninput="calculateTotal()"
                            class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4">
                    </div>
                </div>
            </div>

            <!-- Items -->
            <div class="bg-white p-6 sm:p-8 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-red-50 text-red-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-list text-sm"></i>
                        </div>
                        <h2 class="text-lg font-black text-gray-800 uppercase tracking-widest text-[11px]">Line Items</h2>
                    </div>
                    <button type="button" onclick="addItem()"
                        class="w-full sm:w-auto bg-gray-900 text-white px-6 py-3 rounded-xl font-bold hover:bg-black transition text-xs flex items-center justify-center gap-2">
                        <i class="fas fa-plus"></i> Add Item
                    </button>
                </div>

                <div id="items-container" class="space-y-4">
                    <!-- Column headers for desktop -->
                    <div class="hidden lg:grid grid-cols-12 gap-4 px-4 py-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                        <div class="col-span-4">Description</div>
                        <div class="col-span-2">Quantity</div>
                        <div class="col-span-2">Price</div>
                        <div class="col-span-1">Tax (%)</div>
                        <div class="col-span-2 text-right">Amount</div>
                        <div class="col-span-1"></div>
                    </div>

                    <div id="items-list" class="space-y-4">
                        @foreach($invoice->items as $index => $item)
                        <div class="item-row bg-gray-50/50 p-4 rounded-2xl border border-gray-100 lg:bg-transparent lg:p-0 lg:border-0 lg:grid lg:grid-cols-12 lg:gap-4 lg:items-center">
                            <div class="lg:col-span-4 mb-4 lg:mb-0">
                                <label class="lg:hidden block text-[9px] font-black text-gray-400 uppercase mb-1 tracking-widest">Description</label>
                                <input type="text" name="items[{{ $index }}][description]" value="{{ $item->description }}" required
                                    class="w-full border-gray-100 bg-white lg:bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-3" placeholder="Item description">
                            </div>
                            <div class="lg:col-span-2 mb-4 lg:mb-0">
                                <label class="lg:hidden block text-[9px] font-black text-gray-400 uppercase mb-1 tracking-widest">Qty</label>
                                <input type="number" name="items[{{ $index }}][quantity]" value="{{ $item->quantity }}" required min="1"
                                    class="qty w-full border-gray-100 bg-white lg:bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-3" oninput="calculateTotal()">
                            </div>
                            <div class="lg:col-span-2 mb-4 lg:mb-0">
                                <label class="lg:hidden block text-[9px] font-black text-gray-400 uppercase mb-1 tracking-widest">Price</label>
                                <input type="number" name="items[{{ $index }}][unit_price]" value="{{ $item->unit_price }}" required min="0" step="0.01"
                                    class="price w-full border-gray-100 bg-white lg:bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-3 text-right" oninput="calculateTotal()">
                            </div>
                            <div class="lg:col-span-1 mb-4 lg:mb-0">
                                <label class="lg:hidden block text-[9px] font-black text-gray-400 uppercase mb-1 tracking-widest">Tax%</label>
                                <input type="number" name="items[{{ $index }}][tax_rate]" value="{{ $item->tax_rate }}" min="0" step="0.01"
                                    class="tax-rate w-full border-gray-100 bg-white lg:bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-3 text-right" oninput="calculateTotal()">
                            </div>
                            <div class="lg:col-span-2 mt-4 lg:mt-0 pt-4 lg:pt-0 border-t border-gray-100 lg:border-0 flex justify-between lg:justify-end items-center text-right">
                                <label class="lg:hidden block text-[9px] font-black text-gray-400 uppercase tracking-widest">Subtotal</label>
                                <span class="amount font-black text-gray-900 text-sm italic w-full">₹{{ number_format($item->amount, 2) }}</span>
                            </div>
                            <div class="lg:col-span-1 flex justify-end">
                                @if($index > 0)
                                <button type="button" onclick="removeItem(this)" class="p-2 text-red-400 hover:text-red-600 transition bg-white lg:bg-gray-50 rounded-xl">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Totals Display -->
            <div class="flex justify-end">
                <div class="w-full lg:w-1/2 bg-white p-8 rounded-2xl border border-gray-100 shadow-sm space-y-4">
                    <div class="flex justify-between items-center pb-2 border-b border-gray-50">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Subtotal</span>
                        <span id="subtotal-display" class="font-bold text-gray-700">₹{{ number_format($invoice->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-2 border-b border-gray-50">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Item Tax</span>
                        <span id="item-tax-display" class="font-bold text-gray-700">₹0.00</span>
                    </div>
                    <div class="flex justify-between items-center pb-2 border-b border-gray-50">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Global Tax</span>
                        <span id="global-tax-display" class="font-bold text-gray-700">₹0.00</span>
                    </div>
                    <div class="flex justify-between items-center pt-2">
                        <span class="text-xs font-black text-gray-900 uppercase tracking-widest">Grand Total</span>
                        <span id="total-display" class="text-3xl font-black text-primary italic tracking-tighter">₹{{ number_format($invoice->total, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="flex justify-center sm:justify-end gap-4 pt-10 border-t border-gray-100">
                <a href="{{ route('invoices.show', $invoice->id) }}"
                    class="px-8 py-4 text-gray-400 font-bold hover:text-gray-600 transition text-sm flex items-center">Cancel</a>
                <button type="submit" name="action" value="draft"
                    class="bg-gray-100 text-gray-700 px-8 py-4 rounded-xl font-black hover:bg-gray-200 transition shadow-sm uppercase tracking-widest text-xs">
                    Save as Draft
                </button>
                <button type="submit" name="action" value="generate"
                    class="bg-blue-600 text-white px-12 py-4 rounded-xl font-black hover:bg-blue-700 transition shadow-xl shadow-blue-200 uppercase tracking-widest text-xs">
                    Update Invoice
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            calculateTotal();

            // Auto-fill client_logo from the pre-selected client if invoice has no logo yet
            const clientSelect = document.getElementById('client_select');
            const clientLogoInput = document.getElementById('client_logo');
            if (clientSelect && clientLogoInput && !clientLogoInput.value) {
                const selectedOption = clientSelect.options[clientSelect.selectedIndex];
                if (selectedOption && selectedOption.value) {
                    const logo = selectedOption.getAttribute('data-logo');
                    if (logo) {
                        clientLogoInput.value = logo;
                    }
                }
            }
        });

        function populateBusiness() {
            const select = document.getElementById('business_select');
            const selectedOption = select.options[select.selectedIndex];

            if (selectedOption.value) {
                document.getElementById('sender_name').value = selectedOption.getAttribute('data-name');
                document.getElementById('sender_address').value = selectedOption.getAttribute('data-address');
                document.getElementById('sender_website').value = selectedOption.getAttribute('data-website');
                document.getElementById('sender_phone').value = selectedOption.getAttribute('data-phone');
                document.getElementById('bank_details').value = selectedOption.getAttribute('data-bank');
                document.getElementById('logo').value = selectedOption.getAttribute('data-logo');
            }
        }

        function populateClient() {
            const select = document.getElementById('client_select');
            const selectedOption = select.options[select.selectedIndex];
            const detailsFields = document.getElementById('client_details_fields');
            const clientNameInput = document.getElementById('client_name');

            if (selectedOption.value === "") {
                detailsFields.style.display = 'none';
                clientNameInput.required = false;
            } else {
                detailsFields.style.display = 'block';
                clientNameInput.required = true;

                if (selectedOption.value === "manual") {
                    document.getElementById('client_name').value = '';
                    document.getElementById('client_address').value = '';
                    document.getElementById('client_phone').value = '';
                    document.getElementById('client_logo').value = '';
                } else {
                    document.getElementById('client_name').value = selectedOption.getAttribute('data-name');
                    document.getElementById('client_address').value = selectedOption.getAttribute('data-address');
                    document.getElementById('client_phone').value = selectedOption.getAttribute('data-phone');
                    document.getElementById('client_logo').value = selectedOption.getAttribute('data-logo');
                }
            }
        }

        let itemCount = {{ $invoice->items->count() }};

        function addItem() {
            const row = `
                    <div class="item-row bg-gray-50/50 p-4 rounded-2xl border border-gray-100 lg:bg-transparent lg:p-0 lg:border-0 lg:grid lg:grid-cols-12 lg:gap-4 lg:items-center">
                        <div class="lg:col-span-4 mb-4 lg:mb-0">
                            <label class="lg:hidden block text-[9px] font-black text-gray-400 uppercase mb-1 tracking-widest">Description</label>
                            <input type="text" name="items[${itemCount}][description]" required
                                class="w-full border-gray-100 bg-white lg:bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-3" placeholder="Item description">
                        </div>
                        <div class="lg:col-span-2 mb-4 lg:mb-0">
                            <label class="lg:hidden block text-[9px] font-black text-gray-400 uppercase mb-1 tracking-widest">Qty</label>
                            <input type="number" name="items[${itemCount}][quantity]" required min="1" value="1"
                                class="qty w-full border-gray-100 bg-white lg:bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-3" oninput="calculateTotal()">
                        </div>
                        <div class="lg:col-span-2 mb-4 lg:mb-0">
                            <label class="lg:hidden block text-[9px] font-black text-gray-400 uppercase mb-1 tracking-widest">Price</label>
                            <input type="number" name="items[${itemCount}][unit_price]" required min="0" step="0.01"
                                class="price w-full border-gray-100 bg-white lg:bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-3 text-right" oninput="calculateTotal()">
                        </div>
                        <div class="lg:col-span-1 mb-4 lg:mb-0">
                            <label class="lg:hidden block text-[9px] font-black text-gray-400 uppercase mb-1 tracking-widest">Tax%</label>
                            <input type="number" name="items[${itemCount}][tax_rate]" min="0" step="0.01" value="0"
                                class="tax-rate w-full border-gray-100 bg-white lg:bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-3 text-right" oninput="calculateTotal()">
                        </div>
                        <div class="lg:col-span-2 mt-4 lg:mt-0 pt-4 lg:pt-0 border-t border-gray-100 lg:border-0 flex justify-between lg:justify-end items-center text-right">
                            <label class="lg:hidden block text-[9px] font-black text-gray-400 uppercase tracking-widest">Subtotal</label>
                            <span class="amount font-black text-gray-900 text-sm italic w-full">₹0.00</span>
                        </div>
                        <div class="lg:col-span-1 flex justify-end">
                            <button type="button" onclick="removeItem(this)" class="p-2 text-red-400 hover:text-red-600 transition bg-white lg:bg-gray-50 rounded-xl">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                `;
            document.getElementById('items-list').insertAdjacentHTML('beforeend', row);
            itemCount++;
        }

        function removeItem(btn) {
            btn.closest('.item-row').remove();
            calculateTotal();
        }

        function calculateTotal() {
            let subtotal = 0;
            let totalItemTax = 0;

            document.querySelectorAll('.item-row').forEach(row => {
                const qty = parseFloat(row.querySelector('.qty').value) || 0;
                const price = parseFloat(row.querySelector('.price').value) || 0;
                const itemTaxRate = parseFloat(row.querySelector('.tax-rate').value) || 0;

                const lineAmount = qty * price;
                const lineTax = lineAmount * (itemTaxRate / 100);

                subtotal += lineAmount;
                totalItemTax += lineTax;

                row.querySelector('.amount').textContent = '₹' + lineAmount.toLocaleString('en-IN', { minimumFractionDigits: 2 });
            });

            const globalTaxRate = parseFloat(document.getElementById('tax_rate').value) || 0;
            const globalTax = subtotal * (globalTaxRate / 100);

            const total = subtotal + totalItemTax + globalTax;

            document.getElementById('subtotal-display').textContent = '₹' + subtotal.toLocaleString('en-IN', { minimumFractionDigits: 2 });
            document.getElementById('item-tax-display').textContent = '₹' + totalItemTax.toLocaleString('en-IN', { minimumFractionDigits: 2 });
            document.getElementById('global-tax-display').textContent = '₹' + globalTax.toLocaleString('en-IN', { minimumFractionDigits: 2 });
            document.getElementById('total-display').textContent = '₹' + total.toLocaleString('en-IN', { minimumFractionDigits: 2 });
        }
    </script>
@endsection
