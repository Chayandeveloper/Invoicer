@extends('layout')

@section('content')
    <div class="py-12">
        <div class="text-center mb-16 animate-fade-in">
            <h1 class="text-4xl md:text-6xl font-black text-gray-900 mb-4 tracking-tight">
                Every billing tool you need,<br><span class="text-primary">in one place</span>
            </h1>
            <p class="text-xl text-gray-500 font-medium max-w-2xl mx-auto">
                Professional, secure, and easy-to-use invoicing and financial management tools for your business.
            </p>
        </div>

        <!-- Tool Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
            <!-- Invoices -->
            <a href="{{ route('invoices.index') }}"
                class="group bg-white p-8 rounded-[2rem] border border-gray-100 shadow-xl shadow-gray-200/50 hover:shadow-primary/10 hover:border-primary/20 transition-all duration-300 transform hover:-translate-y-2">
                <div
                    class="bg-blue-50 text-blue-600 w-16 h-16 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <i class="fas fa-file-invoice text-2xl"></i>
                </div>
                <h3 class="text-xl font-black text-gray-900 mb-2">Invoices</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Create and manage professional invoices, track payments,
                    and send to clients.</p>
            </a>

            <!-- Quotations -->
            <a href="{{ route('quotations.index') }}"
                class="group bg-white p-8 rounded-[2rem] border border-gray-100 shadow-xl shadow-gray-200/50 hover:shadow-primary/10 hover:border-primary/20 transition-all duration-300 transform hover:-translate-y-2">
                <div
                    class="bg-indigo-50 text-indigo-600 w-16 h-16 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <i class="fas fa-file-contract text-2xl"></i>
                </div>
                <h3 class="text-xl font-black text-gray-900 mb-2">Quotations</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Generate professional quotes and convert them to invoices
                    with a single click.</p>
            </a>

            <!-- Expenses -->
            <a href="{{ route('expenses.index') }}"
                class="group bg-white p-8 rounded-[2rem] border border-gray-100 shadow-xl shadow-gray-200/50 hover:shadow-primary/10 hover:border-primary/20 transition-all duration-300 transform hover:-translate-y-2">
                <div
                    class="bg-red-50 text-red-600 w-16 h-16 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <i class="fas fa-receipt text-2xl"></i>
                </div>
                <h3 class="text-xl font-black text-gray-900 mb-2">Expenses</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Track your business spending, categorize expenses, and
                    monitor your outflow.</p>
            </a>

            <!-- Payments -->
            <a href="{{ route('payments.index') }}"
                class="group bg-white p-8 rounded-[2rem] border border-gray-100 shadow-xl shadow-gray-200/50 hover:shadow-primary/10 hover:border-primary/20 transition-all duration-300 transform hover:-translate-y-2">
                <div
                    class="bg-green-50 text-green-600 w-16 h-16 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <i class="fas fa-hand-holding-dollar text-2xl"></i>
                </div>
                <h3 class="text-xl font-black text-gray-900 mb-2">Payments</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Record customer payments, issue receipts, and keep track of
                    your income.</p>
            </a>

            <!-- Businesses -->
            <a href="{{ route('businesses.index') }}"
                class="group bg-white p-8 rounded-[2rem] border border-gray-100 shadow-xl shadow-gray-200/50 hover:shadow-primary/10 hover:border-primary/20 transition-all duration-300 transform hover:-translate-y-2">
                <div
                    class="bg-amber-50 text-amber-600 w-16 h-16 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <i class="fas fa-building text-2xl"></i>
                </div>
                <h3 class="text-xl font-black text-gray-900 mb-2">Businesses</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Manage multiple business profiles, each with its own logo
                    and contact details.</p>
            </a>

            <!-- Clients -->
            <a href="{{ route('clients.index') }}"
                class="group bg-white p-8 rounded-[2rem] border border-gray-100 shadow-xl shadow-gray-200/50 hover:shadow-primary/10 hover:border-primary/20 transition-all duration-300 transform hover:-translate-y-2">
                <div
                    class="bg-purple-50 text-purple-600 w-16 h-16 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <h3 class="text-xl font-black text-gray-900 mb-2">Clients</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Maintain a database of your clients and their contact
                    information for quick billing.</p>
            </a>

            <!-- Dashboard (Secondary) -->
            <a href="{{ route('dashboard') }}"
                class="group bg-gray-50 p-8 rounded-[2rem] border border-dashed border-gray-200 hover:border-primary/40 transition-all duration-300 flex flex-col items-center justify-center text-center">
                <div
                    class="bg-white text-gray-400 w-12 h-12 rounded-xl flex items-center justify-center mb-4 group-hover:text-primary transition-colors shadow-sm">
                    <i class="fas fa-chart-pie text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-600 group-hover:text-primary transition-colors">View Analytics</h3>
                <p class="text-gray-400 text-xs mt-1">Detailed statistics and charts</p>
            </a>
        </div>
    </div>

    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.6s ease-out forwards;
        }
    </style>
@endsection