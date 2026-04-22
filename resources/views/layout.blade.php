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

        /* Mobile menu animation */
        .mobile-menu-enter {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            transition: max-height 0.35s ease-in-out, opacity 0.25s ease-in-out;
        }

        .mobile-menu-enter.active {
            max-height: 600px;
            opacity: 1;
        }

        /* Slide-in flash message */
        @keyframes slide-in {
            from { transform: translateX(-10px); opacity: 0; }
            to   { transform: translateX(0);     opacity: 1; }
        }
        .animate-slide-in { animation: slide-in 0.3s ease-out forwards; }

        /* Back-to-top transition */
        #back-to-top { transition: opacity 0.3s ease, transform 0.3s ease; }

        /* Prevent horizontal overflow globally */
        html, body { overflow-x: hidden; }

        /* Nav link active / hover states handled via Tailwind classes below */
    </style>
</head>

<body class="bg-gray-50 flex flex-col min-h-screen">

    <!-- ===================== NAVBAR ===================== -->
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-[100]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">

                <!-- Logo & Brand -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group shrink-0">
                    <div class="bg-primary text-white p-2 rounded-lg group-hover:bg-primary-dark transition-colors">
                        <i class="fas fa-file-invoice-dollar text-lg"></i>
                    </div>
                    <span class="font-black text-xl tracking-tight text-gray-900 group-hover:text-primary transition-colors">
                        Invoicer
                    </span>
                </a>

                <!-- Desktop Navigation Links (hidden below lg) -->
                <div class="hidden lg:flex items-center gap-x-1">
                    <a href="{{ url('/') }}"
                        class="px-3 py-2 rounded-lg text-sm font-bold flex items-center gap-x-1.5 whitespace-nowrap
                               {{ request()->is('/') ? 'text-primary bg-primary/5' : 'text-gray-500 hover:text-primary hover:bg-gray-50' }} transition-all">
                        <i class="fas fa-home text-sm"></i> Home
                    </a>
                    <a href="{{ route('dashboard') }}"
                        class="px-3 py-2 rounded-lg text-sm font-bold flex items-center gap-x-1.5 whitespace-nowrap
                               {{ request()->routeIs('dashboard') ? 'text-primary bg-primary/5' : 'text-gray-500 hover:text-primary hover:bg-gray-50' }} transition-all">
                        <i class="fas fa-chart-line text-sm"></i> Dashboard
                    </a>
                    <a href="{{ route('invoices.index') }}"
                        class="px-3 py-2 rounded-lg text-sm font-bold flex items-center gap-x-1.5 whitespace-nowrap
                               {{ request()->routeIs('invoices.*') ? 'text-primary bg-primary/5' : 'text-gray-500 hover:text-primary hover:bg-gray-50' }} transition-all">
                        <i class="fas fa-file-invoice text-sm"></i> Invoices
                    </a>
                    <a href="{{ route('quotations.index') }}"
                        class="px-3 py-2 rounded-lg text-sm font-bold flex items-center gap-x-1.5 whitespace-nowrap
                               {{ request()->routeIs('quotations.*') ? 'text-primary bg-primary/5' : 'text-gray-500 hover:text-primary hover:bg-gray-50' }} transition-all">
                        <i class="fas fa-file-contract text-sm"></i> Quotes
                    </a>
                    <a href="{{ route('expenses.index') }}"
                        class="px-3 py-2 rounded-lg text-sm font-bold flex items-center gap-x-1.5 whitespace-nowrap
                               {{ request()->routeIs('expenses.*') ? 'text-primary bg-primary/5' : 'text-gray-500 hover:text-primary hover:bg-gray-50' }} transition-all">
                        <i class="fas fa-receipt text-sm"></i> Expenses
                    </a>
                    <a href="{{ route('payments.index') }}"
                        class="px-3 py-2 rounded-lg text-sm font-bold flex items-center gap-x-1.5 whitespace-nowrap
                               {{ request()->routeIs('payments.*') ? 'text-primary bg-primary/5' : 'text-gray-500 hover:text-primary hover:bg-gray-50' }} transition-all">
                        <i class="fas fa-hand-holding-dollar text-sm"></i> Payments
                    </a>
                    <a href="{{ route('businesses.index') }}"
                        class="px-3 py-2 rounded-lg text-sm font-bold flex items-center gap-x-1.5 whitespace-nowrap
                               {{ request()->routeIs('businesses.*') ? 'text-primary bg-primary/5' : 'text-gray-500 hover:text-primary hover:bg-gray-50' }} transition-all">
                        <i class="fas fa-building text-sm"></i> Businesses
                    </a>
                    <a href="{{ route('clients.index') }}"
                        class="px-3 py-2 rounded-lg text-sm font-bold flex items-center gap-x-1.5 whitespace-nowrap
                               {{ request()->routeIs('clients.*') ? 'text-primary bg-primary/5' : 'text-gray-500 hover:text-primary hover:bg-gray-50' }} transition-all">
                        <i class="fas fa-users text-sm"></i> Clients
                    </a>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center gap-2 shrink-0">
                    <!-- New Invoice button – visible sm+ but hidden on lg where it's in the user block -->
                        <a href="{{ route('invoices.create') }}"
                            class="hidden sm:flex lg:hidden bg-primary text-white px-4 py-2 rounded-xl text-sm font-bold
                                   hover:bg-primary-dark transition-all transform hover:scale-105 active:scale-95
                                   shadow-lg shadow-primary/20 items-center gap-1.5 whitespace-nowrap">
                            <i class="fas fa-plus text-xs"></i> New Invoice
                        </a>

                    <!-- Desktop: New Invoice + user block (lg+) -->
                    @auth
                        <div class="hidden lg:flex items-center gap-3 ml-2 pl-4 border-l border-gray-100">
                                <a href="{{ route('invoices.create') }}"
                                    class="bg-primary text-white px-4 py-2 rounded-xl text-sm font-bold
                                           hover:bg-primary-dark transition-all transform hover:scale-105 active:scale-95
                                           shadow-lg shadow-primary/20 flex items-center gap-1.5 whitespace-nowrap">
                                    <i class="fas fa-plus text-xs"></i> New Invoice
                                </a>
                            <div class="text-right">
                                <p class="text-xs font-black text-gray-900 leading-tight">{{ Auth::user()->name }}</p>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="text-[10px] font-bold text-gray-400 hover:text-red-500 transition-colors uppercase tracking-widest">
                                        Logout
                                    </button>
                                </form>
                            </div>
                            <div class="w-9 h-9 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-black text-sm shrink-0">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        </div>
                    @endauth

                    <!-- Mobile / Tablet Menu Toggle (hidden lg+) -->
                    <button id="mobile-menu-btn"
                        class="lg:hidden p-2 rounded-xl text-gray-500 hover:bg-gray-100 hover:text-primary transition-all">
                        <i class="fas fa-bars text-xl" id="menu-icon"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- ===== Mobile / Tablet Navigation Menu (hidden lg+) ===== -->
        <div id="mobile-menu" class="lg:hidden mobile-menu-enter bg-white border-t border-gray-100 shadow-xl">
            <div class="max-w-6xl mx-auto px-4 py-2 space-y-1 pb-4">

                <a href="{{ url('/') }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-bold
                           {{ request()->is('/') ? 'text-primary bg-primary/5' : 'text-gray-500 hover:text-primary hover:bg-gray-50' }} transition-all">
                    <i class="fas fa-home w-5 text-center"></i> Home
                </a>
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-bold
                           {{ request()->routeIs('dashboard') ? 'text-primary bg-primary/5' : 'text-gray-500 hover:text-primary hover:bg-gray-50' }} transition-all">
                    <i class="fas fa-chart-line w-5 text-center"></i> Dashboard
                </a>
                <a href="{{ route('invoices.index') }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-bold
                           {{ request()->routeIs('invoices.*') ? 'text-primary bg-primary/5' : 'text-gray-500 hover:text-primary hover:bg-gray-50' }} transition-all">
                    <i class="fas fa-file-invoice w-5 text-center"></i> Invoices
                </a>
                <a href="{{ route('quotations.index') }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-bold
                           {{ request()->routeIs('quotations.*') ? 'text-primary bg-primary/5' : 'text-gray-500 hover:text-primary hover:bg-gray-50' }} transition-all">
                    <i class="fas fa-file-contract w-5 text-center"></i> Quotations
                </a>
                <a href="{{ route('expenses.index') }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-bold
                           {{ request()->routeIs('expenses.*') ? 'text-primary bg-primary/5' : 'text-gray-500 hover:text-primary hover:bg-gray-50' }} transition-all">
                    <i class="fas fa-receipt w-5 text-center"></i> Expenses
                </a>
                <a href="{{ route('payments.index') }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-bold
                           {{ request()->routeIs('payments.*') ? 'text-primary bg-primary/5' : 'text-gray-500 hover:text-primary hover:bg-gray-50' }} transition-all">
                    <i class="fas fa-hand-holding-dollar w-5 text-center"></i> Payments
                </a>
                <a href="{{ route('businesses.index') }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-bold
                           {{ request()->routeIs('businesses.*') ? 'text-primary bg-primary/5' : 'text-gray-500 hover:text-primary hover:bg-gray-50' }} transition-all">
                    <i class="fas fa-building w-5 text-center"></i> Businesses
                </a>
                <a href="{{ route('clients.index') }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-bold
                           {{ request()->routeIs('clients.*') ? 'text-primary bg-primary/5' : 'text-gray-500 hover:text-primary hover:bg-gray-50' }} transition-all">
                    <i class="fas fa-users w-5 text-center"></i> Clients
                </a>

                @auth
                    <div class="pt-4 mt-2 border-t border-gray-100 flex items-center gap-3 px-4">
                        <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-black shrink-0">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="flex-grow min-w-0">
                            <p class="text-xs font-black text-gray-900 truncate">{{ Auth::user()->name }}</p>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="text-[10px] font-bold text-gray-400 hover:text-red-500 transition-colors uppercase tracking-widest">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth

                <div class="pt-4 mt-2 border-t border-gray-100 px-4">
                    <a href="{{ route('invoices.create') }}"
                        class="w-full bg-primary text-white py-3 rounded-xl text-center font-black uppercase tracking-widest text-xs flex items-center justify-center gap-2 shadow-lg shadow-primary/20">
                        <i class="fas fa-plus"></i> New Invoice
                    </a>
                </div>
            </div>
        </div>
    </nav>
    <!-- ==================== END NAVBAR ==================== -->

    <!-- ===================== MAIN ===================== -->
    <main class="flex-grow overflow-x-hidden">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8">

            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-primary p-4 rounded-r-xl shadow-sm flex items-center gap-3 animate-slide-in">
                    <i class="fas fa-check-circle text-primary text-lg shrink-0"></i>
                    <p class="text-primary font-bold text-sm">{{ session('success') }}</p>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- ===================== FOOTER ===================== -->
    <footer class="bg-white border-t border-gray-100 pt-12 pb-8 mt-auto">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-8 sm:gap-10 mb-12">

                <!-- Financial Tools -->
                <div>
                    <h3 class="text-[11px] font-black text-gray-900 uppercase tracking-[0.2em] mb-5">Financial Tools</h3>
                    <ul class="space-y-3">
                        <li><a href="{{ route('invoices.index') }}"
                                class="text-xs font-bold text-gray-400 hover:text-primary transition-colors">Invoice Generator</a></li>
                        <li><a href="{{ route('quotations.index') }}"
                                class="text-xs font-bold text-gray-400 hover:text-primary transition-colors">Quotation Maker</a></li>
                        <li><a href="{{ route('expenses.index') }}"
                                class="text-xs font-bold text-gray-400 hover:text-primary transition-colors">Expense Tracker</a></li>
                    </ul>
                </div>

                <!-- Productivity -->
                <div>
                    <h3 class="text-[11px] font-black text-gray-900 uppercase tracking-[0.2em] mb-5">Productivity</h3>
                    <ul class="space-y-3">
                        <li><a href="{{ route('payments.index') }}"
                                class="text-xs font-bold text-gray-400 hover:text-primary transition-colors">Payment Receipts</a></li>
                        <li><a href="{{ route('businesses.index') }}"
                                class="text-xs font-bold text-gray-400 hover:text-primary transition-colors">Business Profiles</a></li>
                        <li><a href="{{ route('clients.index') }}"
                                class="text-xs font-bold text-gray-400 hover:text-primary transition-colors">Client Management</a></li>
                    </ul>
                </div>

                <!-- Invoicer -->
                <div>
                    <h3 class="text-[11px] font-black text-gray-900 uppercase tracking-[0.2em] mb-5">Invoicer</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-xs font-bold text-gray-400 hover:text-primary transition-colors">Desktop App</a></li>
                        <li><a href="#" class="text-xs font-bold text-gray-400 hover:text-primary transition-colors">Mobile Preview</a></li>
                        <li><a href="{{ route('dashboard') }}"
                                class="text-xs font-bold text-gray-400 hover:text-primary transition-colors">Analytics Portal</a></li>
                    </ul>
                </div>

                <!-- Branding + Social -->
                <div class="col-span-2 sm:col-span-2 md:col-span-1 flex flex-row md:flex-col items-start md:items-end gap-6 md:gap-0">
                    <div class="flex items-center gap-2 md:mb-5">
                        <i class="fas fa-file-invoice-dollar text-primary text-2xl"></i>
                        <span class="text-xl font-black text-gray-900 tracking-tighter">Invoicer</span>
                    </div>
                    <div class="flex gap-4 md:mt-0">
                        <a href="#" class="text-gray-300 hover:text-primary transition-all" aria-label="Facebook">
                            <i class="fab fa-facebook-f text-sm"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-primary transition-all" aria-label="Twitter">
                            <i class="fab fa-twitter text-sm"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-primary transition-all" aria-label="LinkedIn">
                            <i class="fab fa-linkedin-in text-sm"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="pt-6 border-t border-gray-50 flex flex-col sm:flex-row justify-between items-center gap-3">
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest text-center sm:text-left">
                    &copy; {{ date('Y') }} Invoicer &middot; All Rights Reserved
                </p>
                <div class="flex items-center gap-2">
                    <span class="text-[9px] text-gray-300 font-black uppercase tracking-[0.3em]">Crafted by</span>
                    <span class="text-[10px] text-primary font-black uppercase tracking-widest">Fillosoft Technologies</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top -->
    <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" id="back-to-top"
        class="fixed bottom-6 right-6 w-12 h-12 bg-white text-gray-400 rounded-full shadow-2xl border border-gray-100
               flex items-center justify-center hover:text-primary opacity-0 translate-y-10 z-50">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script>
        // ── Mobile Menu Toggle ──────────────────────────────────────
        const menuBtn    = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const menuIcon   = document.getElementById('menu-icon');

        menuBtn.addEventListener('click', () => {
            const isOpen = mobileMenu.classList.toggle('active');
            menuIcon.classList.toggle('fa-bars',  !isOpen);
            menuIcon.classList.toggle('fa-times',  isOpen);
        });

        // Close on resize to desktop breakpoint
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                mobileMenu.classList.remove('active');
                menuIcon.classList.replace('fa-times', 'fa-bars');
            }
        });

        // ── Back-to-Top Visibility ──────────────────────────────────
        const backToTop = document.getElementById('back-to-top');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                backToTop.classList.remove('opacity-0', 'translate-y-10');
                backToTop.classList.add('opacity-100', 'translate-y-0');
            } else {
                backToTop.classList.add('opacity-0', 'translate-y-10');
                backToTop.classList.remove('opacity-100', 'translate-y-0');
            }
        });
    </script>
</body>

</html>
