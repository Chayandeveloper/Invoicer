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

        /* Sidebar transitions */
        .sidebar-transition {
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Dropdown transition */
        .dropdown-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }

        .dropdown-content.active {
            max-height: 500px;
        }

        .rotate-icon {
            transition: transform 0.3s ease;
        }

        .rotate-icon.active {
            transform: rotate(180deg);
        }

        /* Mobile sidebar overlay */
        .sidebar-overlay {
            background-color: rgba(0, 0, 0, 0.5);
            transition: opacity 0.3s ease;
        }

        @media (max-width: 1024px) {
            .sidebar-closed {
                transform: translateX(-100%);
            }
        }

        /* Animations */
        @keyframes slide-in {
            from { transform: translateX(-10px); opacity: 0; }
            to   { transform: translateX(0);     opacity: 1; }
        }
        .animate-slide-in { animation: slide-in 0.3s ease-out forwards; }

        @keyframes slide-up {
            from { transform: translateY(10px); opacity: 0; }
            to   { transform: translateY(0);     opacity: 1; }
        }
        .animate-slide-up { animation: slide-up 0.2s ease-out forwards; }

        /* Prevent horizontal overflow globally */
        html, body { overflow-x: hidden; }

        /* Sidebar Mini Styles */
        .sidebar-transition {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @media (min-width: 1024px) {
            #sidebar.mini-mode {
                width: 88px;
            }
            #sidebar.mini-mode .sidebar-text,
            #sidebar.mini-mode .sidebar-header-text,
            #sidebar.mini-mode .sidebar-section-title,
            #sidebar.mini-mode .rotate-icon,
            #sidebar.mini-mode .user-info-text {
                opacity: 0;
                width: 0;
                display: none;
            }
            
            #sidebar.mini-mode .sidebar-mini-icon {
                display: block !important;
            }
            #sidebar.mini-mode .user-account-card {
                flex-direction: column;
                gap: 0.75rem;
                padding-left: 0;
                padding-right: 0;
                background: transparent;
            }
            #sidebar.mini-mode .user-account-card div:first-child {
                margin: 0 auto;
            }
            #sidebar.mini-mode .sidebar-item {
                justify-content: center;
                padding-left: 0;
                padding-right: 0;
                margin-left: 0.75rem;
                margin-right: 0.75rem;
            }
            #sidebar.mini-mode .sidebar-item i {
                margin: 0;
                width: auto;
                font-size: 1.1rem;
            }
            #sidebar.mini-mode .sidebar-header {
                justify-content: center;
                padding: 0;
            }
            
            #main-content-wrapper.mini-sidebar {
                padding-left: 88px;
            }
        }

    </style>
    <!-- Clerk JS SDK -->
    <script async crossorigin="anonymous" data-clerk-publishable-key="{{ env('CLERK_PUBLISHABLE_KEY') }}" src="{{ env('CLERK_FRONTEND_API') }}/npm/@clerk/clerk-js@latest/dist/clerk.browser.js" type="text/javascript"></script>
</head>

<body class="bg-gray-50 flex flex-col min-h-screen">

    <!-- ===================== TOP BAR (Full Width) ===================== -->
    <header class="h-20 bg-primary border-b border-primary-dark/20 flex items-center justify-between px-4 sm:px-8 fixed top-0 left-0 right-0 z-[200] shadow-lg shadow-primary/10">
        <div class="flex items-center gap-4">
            <button onclick="toggleSidebar()" class="p-3 rounded-2xl text-white hover:bg-primary-dark transition-all">
                <i class="fas fa-bars-staggered text-xl"></i>
            </button>
            <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                <div class="bg-white text-primary p-2 rounded-xl shadow-lg shadow-black/5 shrink-0">
                    <i class="fas fa-file-invoice-dollar text-sm"></i>
                </div>
                <span class="font-black text-xl tracking-tighter text-white sidebar-header-text hidden sm:inline">Invoicer</span>
            </a>
        </div>

        <div class="flex items-center gap-4">
            @auth
                <a href="{{ route('profile') }}" class="flex items-center gap-3 px-4 py-2 rounded-2xl hover:bg-primary-dark transition-all border border-transparent hover:border-primary-dark/20 group">
                    <span class="text-xs font-black text-white hidden sm:inline">{{ Auth::user()->name }}</span>
                    <div class="w-9 h-9 rounded-xl bg-white/10 border border-white/20 flex items-center justify-center text-white font-black text-xs group-hover:scale-105 transition-transform">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                </a>
            @endauth
        </div>
    </header>

    <div class="flex flex-grow h-full">
        <!-- ===================== SIDEBAR (Below Header) ===================== -->
        <aside id="sidebar" class="sidebar-transition fixed top-20 bottom-0 left-0 z-[150] w-72 bg-white border-r border-gray-100 flex flex-col sidebar-closed lg:translate-x-0 shadow-2xl shadow-gray-200/50">
            <!-- User Account Section (Top) -->
            <div class="p-6 border-b border-gray-50 bg-white sticky top-0 z-10">
                @auth
                    <div class="user-account-card bg-gray-50 rounded-[2rem] p-4 flex items-center gap-3 group relative">
                        <div class="w-10 h-10 rounded-2xl bg-primary text-white flex-shrink-0 flex items-center justify-center font-black shadow-lg shadow-primary/20">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="flex-grow min-w-0 user-info-text">
                            <p class="text-xs font-black text-gray-900 truncate uppercase tracking-widest leading-none mb-1">{{ Auth::user()->name }}</p>
                            <button type="button" onclick="handleGlobalLogout('logout-form-sidebar')"
                                    class="text-[9px] font-black text-gray-400 hover:text-rose-500 transition-colors uppercase tracking-[0.2em]">Sign Out</button>
                        </div>
                        <button type="button" onclick="handleGlobalLogout('logout-form-sidebar')"
                                class="sidebar-mini-icon hidden p-2 text-gray-400 hover:text-rose-500 transition-colors">
                            <i class="fas fa-power-off"></i>
                        </button>
                    </div>
                @endauth
            </div>

            <!-- Sidebar Content -->
            <div class="flex-grow overflow-y-auto py-4 px-4 space-y-2">
            <!-- General Section -->
            <p class="px-4 text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 sidebar-section-title">General</p>
            
            <a href="{{ route('dashboard') }}" 
               class="sidebar-item flex items-center gap-3 px-4 py-3.5 rounded-2xl text-sm font-bold transition-all {{ request()->routeIs('dashboard') ? 'bg-primary text-white shadow-xl shadow-primary/20' : 'text-gray-500 hover:bg-gray-50 hover:text-primary' }}">
                <i class="fas fa-tachometer-alt w-5 shrink-0"></i>
                <span class="sidebar-text">Dashboard</span>
            </a>

            <!-- Sales Collapsible -->
            <div class="pt-4 sales-collapsible">
                <button onclick="toggleDropdown('sales-dropdown', 'sales-arrow')" 
                        class="sidebar-item w-full flex items-center justify-between px-4 py-3.5 rounded-2xl text-sm font-bold text-gray-500 hover:bg-gray-50 hover:text-primary transition-all group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-chart-line w-5 shrink-0"></i>
                        <span class="sidebar-text">Sales & Invoices</span>
                    </div>
                    <i id="sales-arrow" class="fas fa-chevron-down text-[10px] rotate-icon {{ request()->routeIs('sales.*') ? 'active' : '' }}"></i>
                </button>
                <div id="sales-dropdown" class="dropdown-content mt-1 space-y-1 pl-4 {{ request()->routeIs('sales.*') ? 'active' : '' }}">
                    <a href="{{ route('sales.clients') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-[13px] font-bold transition-all {{ request()->routeIs('sales.clients') ? 'text-primary' : 'text-gray-400 hover:text-primary' }}">
                        <i class="fas fa-users w-5 text-[10px] shrink-0"></i> <span class="sidebar-text">Clients</span>
                    </a>
                    <a href="{{ route('sales.prospects') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-[13px] font-bold transition-all {{ request()->routeIs('sales.prospects') ? 'text-primary' : 'text-gray-400 hover:text-primary' }}">
                        <i class="fas fa-user-plus w-5 text-[10px] shrink-0"></i> <span class="sidebar-text">Prospects</span>
                    </a>
                </div>
            </div>

            <!-- Operations Section -->
            <div class="pt-6">
                <p class="px-4 text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 sidebar-section-title">Operations</p>
                <a href="{{ route('clients.index') }}" 
                   class="sidebar-item flex items-center gap-3 px-4 py-3.5 rounded-2xl text-sm font-bold transition-all {{ request()->routeIs('clients.*') ? 'bg-primary/5 text-primary' : 'text-gray-500 hover:bg-gray-50 hover:text-primary' }}">
                    <i class="fas fa-users w-5 shrink-0"></i>
                    <span class="sidebar-text">Clients</span>
                </a>
                <a href="{{ route('expenses.index') }}" 
                   class="sidebar-item flex items-center gap-3 px-4 py-3.5 rounded-2xl text-sm font-bold transition-all {{ request()->routeIs('expenses.*') ? 'bg-primary/5 text-primary' : 'text-gray-500 hover:bg-gray-50 hover:text-primary' }}">
                    <i class="fas fa-receipt w-5 shrink-0"></i>
                    <span class="sidebar-text">Expenses</span>
                </a>
                   <a href="{{ route('invoices.index') }}" 
                       class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-[13px] font-bold transition-all {{ request()->routeIs('invoices.*') ? 'text-primary' : 'text-gray-400 hover:text-primary' }}">
                        <i class="fas fa-file-invoice w-5 text-[10px] shrink-0"></i> <span class="sidebar-text">Invoices</span>
                    </a>
                    <a href="{{ route('quotations.index') }}" 
                       class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-[13px] font-bold transition-all {{ request()->routeIs('quotations.*') ? 'text-primary' : 'text-gray-400 hover:text-primary' }}">
                        <i class="fas fa-file-contract w-5 text-[10px] shrink-0"></i> <span class="sidebar-text">Quotes</span>
                    </a>
                <a href="{{ route('payments.index') }}" 
                   class="sidebar-item flex items-center gap-3 px-4 py-3.5 rounded-2xl text-sm font-bold transition-all {{ request()->routeIs('payments.*') ? 'bg-primary/5 text-primary' : 'text-gray-500 hover:bg-gray-50 hover:text-primary' }}">
                    <i class="fas fa-hand-holding-dollar w-5 shrink-0"></i>
                    <span class="sidebar-text">Payments</span>
                </a>
                <a href="{{ route('businesses.index') }}" 
                   class="sidebar-item flex items-center gap-3 px-4 py-3.5 rounded-2xl text-sm font-bold transition-all {{ request()->routeIs('businesses.*') ? 'bg-primary/5 text-primary' : 'text-gray-500 hover:bg-gray-50 hover:text-primary' }}">
                    <i class="fas fa-building w-5 shrink-0"></i>
                    <span class="sidebar-text">Businesses</span>
                </a>
            </div>
        </div>

        </div>
    </aside>

    <!-- Sidebar Mobile Overlay -->
    <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/40 z-[140] hidden lg:hidden"></div>

        <!-- ===================== MAIN CONTENT ===================== -->
        <div id="main-content-wrapper" class="sidebar-transition flex-grow flex flex-col lg:pl-72 pt-20 transition-all duration-300">
            <main class="flex-grow p-4 sm:p-8 lg:p-12">
            @if(session('success'))
                <div class="mb-8 bg-emerald-50 border border-emerald-100 p-5 rounded-3xl shadow-sm flex items-center gap-4 animate-slide-in">
                    <div class="bg-emerald-500 text-white p-2 rounded-xl"><i class="fas fa-check text-xs"></i></div>
                    <p class="text-emerald-800 font-bold text-sm">{{ session('success') }}</p>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="p-8 lg:p-12 border-t border-gray-50 bg-white">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <p class="text-[9px] text-gray-300 font-black uppercase tracking-[0.3em]">&copy; {{ date('Y') }} INVOICER &middot; ALL RIGHTS RESERVED</p>
                <div class="flex items-center gap-3">
                    <span class="text-[9px] text-gray-300 font-black uppercase tracking-[0.3em]">CRAFTED BY</span>
                    <span class="text-[10px] text-indigo-600 font-black tracking-widest">FILLOSOFT TECHNOLOGIES</span>
                </div>
            </div>
        </footer>
    </div>

    <form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const wrapper = document.getElementById('main-content-wrapper');
            
            if (window.innerWidth >= 1024) {
                // Desktop: Toggle mini mode
                sidebar.classList.toggle('mini-mode');
                wrapper.classList.toggle('mini-sidebar');
                
                // If closing mini mode, close all active dropdowns too
                if (sidebar.classList.contains('mini-mode')) {
                    document.querySelectorAll('.dropdown-content').forEach(d => d.classList.remove('active'));
                    document.querySelectorAll('.rotate-icon').forEach(i => i.classList.remove('active'));
                }
            } else {
                // Mobile: Toggle drawer
                sidebar.classList.toggle('sidebar-closed');
                overlay.classList.toggle('hidden');
            }
        }

        function toggleDropdown(id, arrowId) {
            const dropdown = document.getElementById(id);
            const arrow = document.getElementById(arrowId);
            dropdown.classList.toggle('active');
            arrow.classList.toggle('active');
        }

        async function handleGlobalLogout(formId) {
            try { if (window.Clerk && Clerk.user) await Clerk.signOut(); } 
            catch (e) { console.error(e); } 
            finally { document.getElementById(formId).submit(); }
        }

        // Global click listener to close dropdowns
        window.addEventListener('click', function(e) {
            if (!e.target.closest('.group\\/more')) {
                document.querySelectorAll('.group\\/more div:not(.hidden)').forEach(dropdown => {
                    dropdown.classList.add('hidden');
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
