<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoicer</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .mobile-menu-enter {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            transition: all 0.3s ease-in-out;
        }

        .mobile-menu-enter.active {
            max-height: 500px;
            opacity: 1;
        }
    </style>
</head>

<body class="bg-gray-50 flex flex-col min-h-screen">
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-[100]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo & Brand -->
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
                        <div class="bg-primary text-white p-2 rounded-lg group-hover:bg-primary-dark transition-colors">
                            <i class="fas fa-file-invoice-dollar text-xl"></i>
                        </div>
                        <span
                            class="font-black text-xl tracking-tight text-gray-900 group-hover:text-primary transition-colors">Invoicer</span>
                    </a>
                </div>

                <!-- Desktop Navigation Links -->
                <div class="hidden md:flex items-center gap-x-1 lg:gap-x-4">
                    <a href="{{ url('/') }}"
                        class="px-3 py-2 rounded-lg text-sm font-bold flex items-center gap-x-2 {{ request()->is('/') ? 'text-primary bg-primary/5' : 'text-gray-500 hover:text-primary hover:bg-gray-50' }} transition-all">
                        <i class="fas fa-home text-base"></i> Home
                    </a>
                    <a href="{{ route('dashboard') }}"
                        class="px-3 py-2 rounded-lg text-sm font-bold flex items-center gap-x-2 {{ request()->routeIs('dashboard') ? 'text-primary bg-primary/5' : 'text-gray-500 hover:text-primary hover:bg-gray-50' }} transition-all">
                        <i class="fas fa-chart-line"></i> Dashboard
                    </a>
                    <a href="{{ route('invoices.index') }}"
                        class="px-3 py-2 rounded-lg text-sm font-bold flex items-center gap-x-2 {{ request()->routeIs('invoices.*') ? 'text-primary bg-primary/5' : 'text-gray-500 hover:text-primary hover:bg-gray-50' }} transition-all">
                        <i class="fas fa-file-invoice"></i> Invoices
                    </a>
                    <a href="{{ route('quotations.index') }}"
                        class="px-3 py-2 rounded-lg text-sm font-bold flex items-center gap-x-2 {{ request()->routeIs('quotations.*') ? 'text-primary bg-primary/5' : 'text-gray-500 hover:text-primary hover:bg-gray-50' }} transition-all">
                        <i class="fas fa-file-contract"></i> Quotations
                    </a>
                    <a href="{{ route('expenses.index') }}"
                        class="px-3 py-2 rounded-lg text-sm font-bold flex items-center gap-x-2 {{ request()->routeIs('expenses.*') ? 'text-primary bg-primary/5' : 'text-gray-500 hover:text-primary hover:bg-gray-50' }} transition-all">
                        <i class="fas fa-receipt"></i> Expenses
                    </a>
                    <a href="{{ route('payments.index') }}"
                        class="px-3 py-2 rounded-lg text-sm font-bold flex items-center gap-x-2 {{ request()->routeIs('payments.*') ? 'text-primary bg-primary/5' : 'text-gray-500 hover:text-primary hover:bg-gray-50' }} transition-all">
                        <i class="fas fa-hand-holding-dollar"></i> Payments
                    </a>
                    <a href="{{ route('businesses.index') }}"
                        class="px-3 py-2 rounded-lg text-sm font-bold flex items-center gap-x-2 {{ request()->routeIs('businesses.*') ? 'text-primary bg-primary/5' : 'text-gray-500 hover:text-primary hover:bg-gray-50' }} transition-all">
                        <i class="fas fa-building"></i> Businesses
                    </a>
                    <a href="{{ route('clients.index') }}"
                        class="px-3 py-2 rounded-lg text-sm font-bold flex items-center gap-x-2 {{ request()->routeIs('clients.*') ? 'text-primary bg-primary/5' : 'text-gray-500 hover:text-primary hover:bg-gray-50' }} transition-all">
                        <i class="fas fa-users"></i> Clients
                    </a>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center gap-2">
                    <a href="{{ route('invoices.create') }}"
                        class="hidden sm:flex bg-primary text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-primary-dark transition-all transform hover:scale-105 active:scale-95 shadow-lg shadow-primary/20 items-center gap-2">
                        <i class="fas fa-plus"></i> New Invoice
                    </a>

                    <!-- Mobile Menu Toggle -->
                    <button id="mobile-menu-btn"
                        class="md:hidden p-2 rounded-xl text-gray-500 hover:bg-gray-100 hover:text-primary transition-all">
                        <i class="fas fa-bars text-xl" id="menu-icon"></i>
                    </button>

                    <!-- User Profile / Logout (Desktop) -->
                    @auth
                        <div class="hidden md:flex items-center gap-4 ml-4 pl-4 border-l border-gray-100">
                            <div class="text-right">
                                <p class="text-xs font-black text-gray-900 leading-tight">{{ Auth::user()->name }}</p>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="text-[10px] font-bold text-gray-400 hover:text-red-500 transition-colors uppercase tracking-widest">Logout</button>
                                </form>
                            </div>
                            <div
                                class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-black">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        </div>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div id="mobile-menu" class="md:hidden mobile-menu-enter bg-white border-t border-gray-100 px-4 py-2 shadow-xl">
            <div class="space-y-1 pb-4">
                <a href="{{ url('/') }}"
                    class="block px-4 py-2.5 rounded-xl text-sm font-black {{ request()->is('/') ? 'text-primary bg-primary/5' : 'text-gray-500 hover:text-primary hover:bg-gray-50' }} transition-all">
                    <i class="fas fa-home w-6"></i> Home
                </a>
                <a href="{{ route('dashboard') }}"
                    class="block px-4 py-2.5 rounded-xl text-sm font-black {{ request()->routeIs('dashboard') ? 'text-primary bg-primary/5' : 'text-gray-500 hover:text-primary hover:bg-gray-50' }} transition-all">
                    <i class="fas fa-chart-line w-6"></i> Dashboard
                </a>
                <a href="{{ route('invoices.index') }}"
                    class="block px-4 py-2.5 rounded-xl text-sm font-black {{ request()->routeIs('invoices.*') ? 'text-primary bg-primary/5' : 'text-gray-500 hover:text-primary hover:bg-gray-50' }} transition-all">
                    <i class="fas fa-file-invoice w-6"></i> Invoices
                </a>
                <a href="{{ route('quotations.index') }}"
                    class="block px-4 py-2.5 rounded-xl text-sm font-black {{ request()->routeIs('quotations.*') ? 'text-primary bg-primary/5' : 'text-gray-500 hover:text-primary hover:bg-gray-50' }} transition-all">
                    <i class="fas fa-file-contract w-6"></i> Quotations
                </a>
                <a href="{{ route('expenses.index') }}"
                    class="block px-4 py-2.5 rounded-xl text-sm font-black {{ request()->routeIs('expenses.*') ? 'text-primary bg-primary/5' : 'text-gray-500 hover:text-primary hover:bg-gray-50' }} transition-all">
                    <i class="fas fa-receipt w-6"></i> Expenses
                </a>
                <a href="{{ route('payments.index') }}"
                    class="block px-4 py-2.5 rounded-xl text-sm font-black {{ request()->routeIs('payments.*') ? 'text-primary bg-primary/5' : 'text-gray-500 hover:text-primary hover:bg-gray-50' }} transition-all">
                    <i class="fas fa-hand-holding-dollar w-6"></i> Payments
                </a>
                <a href="{{ route('businesses.index') }}"
                    class="block px-4 py-2.5 rounded-xl text-sm font-black {{ request()->routeIs('businesses.*') ? 'text-primary bg-primary/5' : 'text-gray-500 hover:text-primary hover:bg-gray-50' }} transition-all">
                    <i class="fas fa-building w-6"></i> Businesses
                </a>
                <a href="{{ route('clients.index') }}"
                    class="block px-4 py-2.5 rounded-xl text-sm font-black {{ request()->routeIs('clients.*') ? 'text-primary bg-primary/5' : 'text-gray-500 hover:text-primary hover:bg-gray-50' }} transition-all">
                    <i class="fas fa-users w-6"></i> Clients
                </a>

                @auth
                    <div class="pt-4 mt-4 border-t border-gray-100 px-4 flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-black">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="flex-grow">
                            <p class="text-xs font-black text-gray-900">{{ Auth::user()->name }}</p>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Logout</button>
                            </form>
                        </div>
                    </div>
                @endauth

                <div class="pt-4 mt-4 border-t border-gray-100 px-4">
                    <a href="{{ route('invoices.create') }}"
                        class="w-full bg-primary text-white py-3 rounded-xl text-center font-black uppercase tracking-widest text-xs flex items-center justify-center gap-2 shadow-lg shadow-primary/20">
                        <i class="fas fa-plus"></i> New Invoice
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow overflow-x-hidden">
        <div class="max-w-6xl mx-auto p-4 sm:p-6 lg:p-8">
            @if(session('success'))
                <div
                    class="mb-6 bg-green-50 border-l-4 border-primary p-4 rounded-r-xl shadow-sm flex items-center gap-3 animate-slide-in">
                    <i class="fas fa-check-circle text-primary text-lg"></i>
                    <p class="text-primary font-bold text-sm">{{ session('success') }}</p>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <footer class="bg-white border-t border-gray-100 pt-16 pb-8 mt-auto">
        <div class="max-w-6xl mx-auto px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-12 mb-16">
                <!-- Solutions -->
                <div>
                    <h3 class="text-[11px] font-black text-gray-900 uppercase tracking-[0.2em] mb-6">Financial Tools
                    </h3>
                    <ul class="space-y-4">
                        <li><a href="{{ route('invoices.index') }}"
                                class="text-xs font-bold text-gray-400 hover:text-primary transition-colors">Invoice
                                Generator</a></li>
                        <li><a href="{{ route('quotations.index') }}"
                                class="text-xs font-bold text-gray-400 hover:text-primary transition-colors">Quotation
                                Maker</a></li>
                        <li><a href="{{ route('expenses.index') }}"
                                class="text-xs font-bold text-gray-400 hover:text-primary transition-colors">Expense
                                Tracker</a></li>
                    </ul>
                </div>

                <!-- Productivity -->
                <div>
                    <h3 class="text-[11px] font-black text-gray-900 uppercase tracking-[0.2em] mb-6">Productivity</h3>
                    <ul class="space-y-4">
                        <li><a href="{{ route('payments.index') }}"
                                class="text-xs font-bold text-gray-400 hover:text-primary transition-colors">Payment
                                Receipts</a></li>
                        <li><a href="{{ route('businesses.index') }}"
                                class="text-xs font-bold text-gray-400 hover:text-primary transition-colors">Business
                                Profiles</a></li>
                        <li><a href="{{ route('clients.index') }}"
                                class="text-xs font-bold text-gray-400 hover:text-primary transition-colors">Client
                                Management</a></li>
                    </ul>
                </div>

                <!-- Company -->
                <div>
                    <h3 class="text-[11px] font-black text-gray-900 uppercase tracking-[0.2em] mb-6">Invoicer</h3>
                    <ul class="space-y-4">
                        <li><a href="#"
                                class="text-xs font-bold text-gray-400 hover:text-primary transition-colors">Desktop
                                App</a></li>
                        <li><a href="#"
                                class="text-xs font-bold text-gray-400 hover:text-primary transition-colors">Mobile
                                Preview</a></li>
                        <li><a href="{{ route('dashboard') }}"
                                class="text-xs font-bold text-gray-400 hover:text-primary transition-colors">Analytics
                                Portal</a></li>
                    </ul>
                </div>

                <!-- Branding -->
                <div class="flex flex-col items-start md:items-end">
                    <div class="flex items-center gap-2 mb-6">
                        <i class="fas fa-file-invoice-dollar text-primary text-2xl"></i>
                        <span class="text-xl font-black text-gray-900 tracking-tighter">Invoicer</span>
                    </div>
                    <div class="flex gap-4 mb-6">
                        <a href="#" class="text-gray-300 hover:text-primary transition-all"><i
                                class="fab fa-facebook-f text-sm"></i></a>
                        <a href="#" class="text-gray-300 hover:text-primary transition-all"><i
                                class="fab fa-twitter text-sm"></i></a>
                        <a href="#" class="text-gray-300 hover:text-primary transition-all"><i
                                class="fab fa-linkedin-in text-sm"></i></a>
                    </div>
                </div>
            </div>

            <!-- Copyright Area -->
            <div class="pt-8 border-t border-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                    &copy; {{ date('Y') }} Invoicer &middot; All Rights Reserved
                </p>
                <div class="flex items-center gap-2">
                    <span class="text-[9px] text-gray-300 font-black uppercase tracking-[0.3em]">Crafted by</span>
                    <span class="text-[10px] text-primary font-black uppercase tracking-widest">Fillosoft
                        Technologies</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to top button -->
    <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" id="back-to-top"
        class="fixed bottom-6 right-6 w-12 h-12 bg-white text-gray-400 rounded-full shadow-2xl border border-gray-100 flex items-center justify-center hover:text-primary transition-all opacity-0 translate-y-10 z-50">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script>
        // Mobile Menu Toggle
        const menuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const menuIcon = document.getElementById('menu-icon');

        menuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('active');
            if (mobileMenu.classList.contains('active')) {
                menuIcon.classList.remove('fa-bars');
                menuIcon.classList.add('fa-times');
            } else {
                menuIcon.classList.remove('fa-times');
                menuIcon.classList.add('fa-bars');
            }
        });

        // Close menu on resize to desktop
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) {
                mobileMenu.classList.remove('active');
                menuIcon.classList.remove('fa-times');
                menuIcon.classList.add('fa-bars');
            }
        });

        // Back to top Visibility
        window.addEventListener('scroll', () => {
            const btn = document.getElementById('back-to-top');
            if (window.scrollY > 300) {
                btn.classList.remove('opacity-0', 'translate-y-10');
                btn.classList.add('opacity-100', 'translate-y-0');
            } else {
                btn.classList.add('opacity-0', 'translate-y-10');
                btn.classList.remove('opacity-100', 'translate-y-0');
            }
        });
    </script>
    <style>
        @keyframes slide-in {
            from {
                transform: translateX(-10px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .animate-slide-in {
            animation: slide-in 0.3s ease-out forwards;
        }
    </style>
</body>

</html>