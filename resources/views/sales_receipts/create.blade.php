@extends('layout')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('sales-receipts.index') }}" class="p-2 bg-white rounded-xl border border-gray-100 shadow-sm text-gray-400 hover:text-primary transition">
                <i class="fas fa-chevron-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Add Sales Receipt</h1>
                <p class="text-sm text-gray-500 font-medium">Generate a new sales receipt manually</p>
            </div>
        </div>

        <div class="bg-white p-6 sm:p-10 rounded-2xl border border-gray-100 shadow-sm">
            <form action="{{ route('sales-receipts.store') }}" method="POST" class="space-y-8">
                @csrf

                <div class="space-y-6">
                    <!-- Receipt Number & Date -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Receipt Number</label>
                            <input type="text" name="receipt_number" value="SREC-{{ strtoupper(Str::random(6)) }}" required
                                class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-black focus:ring-primary focus:border-primary p-4">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Receipt Date</label>
                            <input type="date" name="receipt_date" value="{{ date('Y-m-d') }}" required
                                class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4">
                        </div>
                    </div>

                    <!-- Item Description -->
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Item Description</label>
                        <textarea name="item_description" rows="3" required placeholder="What are you selling? (e.g. Consulting Services, Product X...)"
                            class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4"></textarea>
                    </div>

                    <!-- Amount -->
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Amount Received (Rs.)</label>
                        <input type="number" name="amount" id="amount" step="0.01" required
                            placeholder="0.00"
                            class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-black focus:ring-primary focus:border-primary p-4 text-primary text-lg">
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
                            <option value="" disabled selected>-- Select Issuing Business --</option>
                            @foreach($businesses as $business)
                                <option value="{{ $business->id }}">
                                    {{ $business->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="border-t border-gray-50 pt-6">
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Client Selection (Received From)</label>
                        <select id="client_select" onchange="populateFromClient()"
                            class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4 mb-4">
                            <option value="">-- Manual Entry / No Client --</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" data-name="{{ $client->name }}" data-logo="{{ $client->logo }}">
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
                    <a href="{{ route('sales-receipts.index') }}" class="px-8 py-4 text-gray-400 font-bold hover:text-gray-600 transition text-xs">Cancel</a>
                    <button type="submit"
                        class="bg-primary text-white px-12 py-4 rounded-xl font-black hover:bg-primary-dark transition shadow-xl shadow-primary/20 uppercase tracking-widest text-xs">
                        Generate Receipt
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function populateFromClient() {
            const select = document.getElementById('client_select');
            const selectedOption = select.options[select.selectedIndex];
            
            if (selectedOption.value) {
                document.getElementById('client_name').value = selectedOption.getAttribute('data-name');
                document.getElementById('client_logo').value = selectedOption.getAttribute('data-logo');
            } else {
                document.getElementById('client_name').value = '';
                document.getElementById('client_logo').value = '';
            }
        }
    </script>
@endsection
