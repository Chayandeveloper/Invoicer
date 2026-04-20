@extends('layout')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('invoices.index') }}"
                class="p-2 bg-white rounded-xl border border-gray-100 shadow-sm text-gray-400 hover:text-primary transition">
                <i class="fas fa-chevron-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Create Invoice</h1>
                <p class="text-sm text-gray-500 font-medium">Issue a new professional invoice</p>
            </div>
        </div>

        <form action="{{ route('invoices.store') }}" method="POST" class="space-y-8" enctype="multipart/form-data">
            @csrf

            <!-- Sender Selection -->
            <div class="bg-white p-6 sm:p-8 rounded-2xl border border-gray-100 shadow-sm space-y-6">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-building text-sm"></i>
                    </div>
                    <h2 class="text-lg font-black text-gray-800 uppercase tracking-widest text-[11px]">From (Your Business)
                    </h2>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Select
                            Business Profile</label>
                        <select id="business_select" onchange="populateBusiness()"
                            class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4">
                            <option value="">-- Manual Entry --</option>
                            @foreach($businesses as $business)
                                <option value="{{ $business->id }}" data-name="{{ $business->name }}"
                                    data-address="{{ $business->address }}" data-website="{{ $business->website }}"
                                    data-phone="{{ $business->phone }}" data-bank="{{ $business->bank_details }}"
                                    data-logo="{{ $business->logo }}">
                                    {{ $business->name }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="logo" id="logo">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label
                            class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Business
                            Name</label>
                        <input type="text" name="sender_name" id="sender_name" required
                            class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4">
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Website</label>
                        <input type="text" name="sender_website" id="sender_website"
                            class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Phone
                            Number</label>
                        <input type="text" name="sender_phone" id="sender_phone"
                            class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4">
                    </div>
                    <div class="sm:col-span-2">
                        <label
                            class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Address</label>
                        <textarea name="sender_address" id="sender_address" rows="2"
                            class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4"></textarea>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Bank
                            Details</label>
                        <textarea name="bank_details" id="bank_details" rows="3"
                            class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4"></textarea>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Payment
                        Link (Optional)</label>
                    <input type="text" name="payment_qr_link" id="payment_qr_link"
                        class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4"
                        placeholder="UPI ID or Payment URL">
                    <p class="text-[9px] text-gray-400 mt-2 ml-1 font-bold italic uppercase tracking-tighter">This will be
                        converted to a QR code on the invoice.</p>
                    <div>
                        <label
                            class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Payment
                            QR Image (Optional)</label>
                        <input type="file" name="payment_qr_image" id="payment_qr_image" accept="image/*"
                            class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4">
                        <p class="text-[9px] text-gray-400 mt-2 ml-1 font-bold italic uppercase tracking-tighter">Upload a
                            pre-generated QR code image to display on the invoice.</p>
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
                        <h2 class="text-lg font-black text-gray-800 uppercase tracking-widest text-[11px]">Bill To (Client)
                        </h2>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Select
                            Saved Client</label>
                        <select id="client_select" onchange="populateClient()"
                            class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4">
                            <option value="">-- Manual Entry --</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" data-name="{{ $client->name }}"
                                    data-address="{{ $client->address }}" data-phone="{{ $client->phone }}"
                                    data-logo="{{ $client->logo }}">
                                    {{ $client->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-4">
                        <input type="hidden" name="client_logo" id="client_logo">
                        <div>
                            <label
                                class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Client
                                Name</label>
                            <input type="text" name="client_name" id="client_name" required
                                class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4">
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Phone
                                Number</label>
                            <input type="text" name="client_phone" id="client_phone"
                                class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4">
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Address</label>
                            <textarea name="client_address" id="client_address" rows="3"
                                class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Invoice Details -->
                <div class="bg-white p-6 sm:p-8 rounded-2xl border border-gray-100 shadow-sm space-y-6 text-gray-800">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 bg-purple-50 text-purple-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-file-invoice text-sm"></i>
                        </div>
                        <h2 class="text-lg font-black text-gray-800 uppercase tracking-widest text-[11px]">Invoice Details
                        </h2>
                    </div>

                    <div>
                        <label
                            class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Invoice
                            Number</label>
                        <input type="text" name="invoice_number" value="INV-{{ rand(1000, 9999) }}" required
                            class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-black tracking-widest focus:ring-primary focus:border-primary p-4 text-primary italic">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Issue
                                Date</label>
                            <input type="date" name="invoice_date" value="{{ date('Y-m-d') }}" required
                                class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4">
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Due
                                Date</label>
                            <input type="date" name="due_date"
                                class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Global
                            Tax Rate (%)</label>
                        <input type="number" name="tax_rate" id="tax_rate" value="0" min="0" step="0.01"
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
                    <div
                        class="hidden lg:grid grid-cols-12 gap-4 px-4 py-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                        <div class="col-span-4">Description</div>
                        <div class="col-span-2">Quantity</div>
                        <div class="col-span-2">Price</div>
                        <div class="col-span-1">Tax (%)</div>
                        <div class="col-span-2 text-right">Amount</div>
                        <div class="col-span-1"></div>
                    </div>

                    <div id="items-list" class="space-y-4">
                        <div
                            class="item-row bg-gray-50/50 p-4 rounded-2xl border border-gray-100 lg:bg-transparent lg:p-0 lg:border-0 lg:grid lg:grid-cols-12 lg:gap-4 lg:items-center">
                            <div class="lg:col-span-4 mb-4 lg:mb-0">
                                <label
                                    class="lg:hidden block text-[9px] font-black text-gray-400 uppercase mb-1 tracking-widest">Description</label>
                                <input type="text" name="items[0][description]" required
                                    class="w-full border-gray-100 bg-white lg:bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-3"
                                    placeholder="Item description">
                            </div>
                            <div class="lg:col-span-2 mb-4 lg:mb-0">
                                <label
                                    class="lg:hidden block text-[9px] font-black text-gray-400 uppercase mb-1 tracking-widest">Qty</label>
                                <input type="number" name="items[0][quantity]" required min="1" value="1"
                                    class="qty w-full border-gray-100 bg-white lg:bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-3"
                                    oninput="calculateTotal()">
                            </div>
                            <div class="lg:col-span-2 mb-4 lg:mb-0">
                                <label
                                    class="lg:hidden block text-[9px] font-black text-gray-400 uppercase mb-1 tracking-widest">Price</label>
                                <input type="number" name="items[0][unit_price]" required min="0" step="0.01"
                                    class="price w-full border-gray-100 bg-white lg:bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-3 text-right"
                                    oninput="calculateTotal()">
                            </div>
                            <div class="lg:col-span-1 mb-4 lg:mb-0">
                                <label
                                    class="lg:hidden block text-[9px] font-black text-gray-400 uppercase mb-1 tracking-widest">Tax%</label>
                                <input type="number" name="items[0][tax_rate]" min="0" step="0.01" value="0"
                                    class="tax-rate w-full border-gray-100 bg-white lg:bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-3 text-right"
                                    oninput="calculateTotal()">
                            </div>
                            <div
                                class="lg:col-span-2 mt-4 lg:mt-0 pt-4 lg:pt-0 border-t border-gray-100 lg:border-0 flex justify-between lg:justify-end items-center text-right">
                                <label
                                    class="lg:hidden block text-[9px] font-black text-gray-400 uppercase tracking-widest">Subtotal</label>
                                <span class="amount font-black text-gray-900 text-sm italic w-full">₹0.00</span>
                            </div>
                            <div class="lg:col-span-1 hidden lg:flex justify-end">
                                <!-- No delete for first row -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Totals Display -->
            <div class="flex justify-end">
                <div class="w-full lg:w-1/2 bg-white p-8 rounded-2xl border border-gray-100 shadow-sm space-y-4">
                    <div class="flex justify-between items-center pb-2 border-b border-gray-50">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Subtotal</span>
                        <span id="subtotal-display" class="font-bold text-gray-700">₹0.00</span>
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
                        <span id="total-display"
                            class="text-3xl font-black text-primary italic tracking-tighter">₹0.00</span>
                    </div>
                </div>
            </div>

            <div class="flex justify-center sm:justify-end gap-4 pt-10 border-t border-gray-100">
                <a href="{{ route('invoices.index') }}"
                    class="px-8 py-4 text-gray-400 font-bold hover:text-gray-600 transition text-sm">Cancel</a>
                <button type="submit"
                    class="bg-primary text-white px-12 py-4 rounded-xl font-black hover:bg-primary-dark transition shadow-xl shadow-primary/20 uppercase tracking-widest text-xs">
                    Generate Invoice
                </button>
            </div>
        </form>
    </div>

    <script>
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

            if (selectedOption.value) {
                document.getElementById('client_name').value = selectedOption.getAttribute('data-name');
                document.getElementById('client_address').value = selectedOption.getAttribute('data-address');
                document.getElementById('client_phone').value = selectedOption.getAttribute('data-phone');
                document.getElementById('client_logo').value = selectedOption.getAttribute('data-logo');
            } else {
                document.getElementById('client_logo').value = '';
            }
        }

        let itemCount = 1;

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