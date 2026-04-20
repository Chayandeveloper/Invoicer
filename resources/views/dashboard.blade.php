@extends('layout')

@section('content')
    <div class="space-y-8 animate-fade-in pb-12">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <h1 class="text-3xl md:text-4xl font-black text-gray-900 tracking-tight">Executive <span
                        class="text-primary">Intelligence</span></h1>
                <p class="text-gray-500 font-medium text-sm mt-1">Real-time financial performance and client insights.</p>
            </div>
            <div class="flex items-center gap-3 w-full md:w-auto">
                <button onclick="window.print()"
                    class="flex-1 md:flex-none justify-center bg-white border border-gray-200 text-gray-700 px-6 py-3 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-gray-50 transition shadow-sm flex items-center gap-3">
                    <i class="fas fa-print"></i> Export Report
                </button>
                <a href="{{ route('invoices.create') }}"
                    class="flex-1 md:flex-none justify-center bg-primary text-white px-6 py-3 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-primary-dark transition shadow-xl shadow-primary/20 flex items-center gap-3">
                    <i class="fas fa-plus"></i> New Invoice
                </a>
            </div>
        </div>

        <!-- Quick Metrics -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Revenue -->
            <div
                class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-xl shadow-gray-200/50 group hover:border-primary/20 transition-all">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-4 bg-primary/10 text-primary rounded-2xl group-hover:scale-110 transition-transform">
                        <i class="fas fa-wallet text-xl"></i>
                    </div>
                    @if($revenueGrowth != 0)
                        <div
                            class="flex items-center gap-1 {{ $revenueGrowth > 0 ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50' }} px-2 py-1 rounded-lg text-[10px] font-black uppercase tracking-tighter">
                            <i class="fas fa-arrow-{{ $revenueGrowth > 0 ? 'up' : 'down' }}"></i>
                            {{ number_format(abs($revenueGrowth), 1) }}%
                        </div>
                    @endif
                </div>
                <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] mb-1">Total Revenue</p>
                <h2 class="text-2xl font-black text-gray-900">Rs. {{ number_format($totalRevenue, 2) }}</h2>
            </div>

            <!-- Liquid Profit -->
            <div
                class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-xl shadow-gray-200/50 group hover:border-blue-500/20 transition-all">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-4 bg-blue-50 text-blue-600 rounded-2xl group-hover:scale-110 transition-transform">
                        <i class="fas fa-chart-line text-xl"></i>
                    </div>
                    <div
                        class="text-[10px] font-black text-blue-600 bg-blue-50 px-2 py-1 rounded-lg uppercase tracking-tighter">
                        Liquid</div>
                </div>
                <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] mb-1">Net Profit</p>
                <h2 class="text-2xl font-black text-gray-900">Rs. {{ number_format($netProfit, 2) }}</h2>
            </div>

            <!-- Total Expenses -->
            <div
                class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-xl shadow-gray-200/50 group hover:border-red-500/20 transition-all">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-4 bg-red-50 text-red-600 rounded-2xl group-hover:scale-110 transition-transform">
                        <i class="fas fa-receipt text-xl"></i>
                    </div>
                    <div
                        class="text-[10px] font-black text-red-600 bg-red-50 px-2 py-1 rounded-lg uppercase tracking-tighter">
                        Outflow</div>
                </div>
                <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] mb-1">Total Expenses</p>
                <h2 class="text-2xl font-black text-gray-900">Rs. {{ number_format($totalExpenses, 2) }}</h2>
            </div>

            <!-- Outstanding -->
            <div
                class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-xl shadow-gray-200/50 group hover:border-amber-500/20 transition-all">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-4 bg-amber-50 text-amber-600 rounded-2xl group-hover:scale-110 transition-transform">
                        <i class="fas fa-hourglass-half text-xl"></i>
                    </div>
                    <div
                        class="text-[10px] font-black text-amber-600 bg-amber-50 px-2 py-1 rounded-lg uppercase tracking-tighter">
                        Pending</div>
                </div>
                <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] mb-1">Pending Invoices</p>
                <h2 class="text-2xl font-black text-gray-900">Rs. {{ number_format($pendingRevenue, 2) }}</h2>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Performance Chart -->
            <div
                class="lg:col-span-2 bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/50 relative overflow-hidden">
                <div class="flex justify-between items-center mb-10">
                    <h3 class="text-xs font-black text-gray-900 uppercase tracking-[0.3em]">Revenue vs Expenses</h3>
                    <div class="flex gap-4">
                        <span class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-primary">
                            <div class="w-2 h-2 bg-primary rounded-full"></div> Revenue
                        </span>
                        <span class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-red-500">
                            <div class="w-2 h-2 bg-red-500 rounded-full"></div> Expenses
                        </span>
                    </div>
                </div>
                <div class="h-[350px]">
                    @if($revenueValues->sum() == 0 && $expenseValues->sum() == 0)
                        <div class="h-full flex flex-col items-center justify-center text-gray-400">
                            <i class="fas fa-chart-bar text-4xl mb-4 text-gray-200"></i>
                            <p class="text-sm font-bold tracking-widest uppercase">No Financial Data Available</p>
                            <p class="text-[10px] font-medium mt-1">Record invoices and expenses to see charts here.</p>
                        </div>
                    @else
                        <canvas id="performanceChart"></canvas>
                    @endif
                </div>
            </div>

            <!-- Expense Breakdown -->
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/50">
                <h3 class="text-xs font-black text-gray-900 uppercase tracking-[0.3em] mb-8 text-center">Expense Composition
                </h3>
                <div class="h-[250px] relative mb-10">
                    @if($expenseBreakdown->isEmpty() || $expenseBreakdown->sum('total') == 0)
                        <div class="h-full flex flex-col items-center justify-center text-gray-400">
                            <i class="fas fa-chart-pie text-4xl mb-4 text-gray-200"></i>
                            <p class="text-xs font-bold tracking-widest uppercase">No Expenses Found</p>
                        </div>
                    @else
                        <canvas id="expenseBreakdownChart"></canvas>
                    @endif
                </div>
                <div class="space-y-4">
                    @foreach($expenseBreakdown->take(3) as $expense)
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full"
                                    style="background-color: var(--chart-color-{{ $loop->index }})"></div>
                                <span
                                    class="text-[10px] font-black text-gray-500 uppercase tracking-widest">{{ $expense->category }}</span>
                            </div>
                            <span class="text-xs font-black text-gray-900">Rs. {{ number_format($expense->total, 0) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Bottom Intelligence -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Top Clients -->
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/50">
                <h3 class="text-xs font-black text-gray-900 uppercase tracking-[0.3em] mb-8">Top Contributors</h3>
                <div class="space-y-6">
                    @foreach($topClients as $client)
                        <div class="flex items-center gap-4">
                            <div
                                class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center font-black text-gray-400 text-xs">
                                {{ substr($client->client_name, 0, 1) }}
                            </div>
                            <div class="flex-grow">
                                <p class="text-sm font-black text-gray-900 tracking-tight">
                                    {{ Str::limit($client->client_name, 20) }}</p>
                                <div class="w-full bg-gray-50 h-1.5 rounded-full mt-2 overflow-hidden">
                                    <div class="bg-primary h-full rounded-full opacity-60"
                                        style="width: {{ ($client->revenue / ($totalRevenue ?: 1)) * 100 }}%"></div>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-black text-gray-900">Rs. {{ number_format($client->revenue, 0) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Pipeline & Recent Activity -->
            <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-gray-900 p-8 rounded-[2.5rem] shadow-2xl text-white relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-8 opacity-10">
                        <i class="fas fa-rocket text-8xl"></i>
                    </div>
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-white/40 mb-8 relative z-10">Sales
                        Pipeline</h3>
                    <div class="space-y-6 relative z-10">
                        @foreach($statusCounts as $status)
                            <div>
                                <div
                                    class="flex justify-between text-[10px] font-black uppercase tracking-widest mb-3 text-white/60">
                                    <span>{{ $status->status }}</span>
                                    <span>{{ $status->count }}</span>
                                </div>
                                <div class="w-full bg-white/5 rounded-full h-2 border border-white/5 overflow-hidden">
                                    @php
                                        $color = match (strtolower($status->status)) {
                                            'paid' => 'bg-emerald-400',
                                            'sent' => 'bg-blue-400',
                                            'pending' => 'bg-amber-400',
                                            'overdue' => 'bg-rose-400',
                                            default => 'bg-gray-400'
                                        };
                                    @endphp
                                    <div class="{{ $color }} h-full rounded-full transition-all duration-1000"
                                        style="width: {{ ($status->count / ($invoiceCount ?: 1)) * 100 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/50">
                    <div class="flex justify-between items-center mb-8">
                        <h3 class="text-xs font-black text-gray-900 uppercase tracking-[0.3em]">Live Feed</h3>
                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    </div>
                    <div class="space-y-6">
                        @foreach($recentPayments->take(4) as $payment)
                            <div class="flex items-center gap-4 border-l-2 border-primary/20 pl-4 py-1">
                                <div class="flex-grow">
                                    <p class="text-xs font-black text-gray-900 tracking-tight">Payment Received</p>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">
                                        #{{ $payment->receipt_number }} &middot; {{ $payment->payment_method }}</p>
                                </div>
                                <p class="text-xs font-black text-primary">+Rs. {{ number_format($payment->amount, 0) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        Chart.defaults.font.family = "'Outfit', sans-serif";
        Chart.defaults.color = '#94a3b8';

        // Performance Chart
        const perfCtx = document.getElementById('performanceChart').getContext('2d');
        new Chart(perfCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [
                    {
                        label: 'Revenue',
                        data: {!! json_encode($revenueValues) !!},
                        borderColor: '#0C8D5D',
                        backgroundColor: 'rgba(12, 141, 93, 0.05)',
                        borderWidth: 4,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#0C8D5D',
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 3
                    },
                    {
                        label: 'Expenses',
                        data: {!! json_encode($expenseValues) !!},
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.05)',
                        borderWidth: 4,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#ef4444',
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        padding: 12,
                        backgroundColor: '#0f172a',
                        titleFont: { weight: 'bold', size: 10 },
                        bodyFont: { weight: 'bold', size: 11 },
                        bodySpacing: 8,
                        usePointStyle: true
                    }
                },
                scales: {
                    y: {
                        grid: { color: 'rgba(0, 0, 0, 0.03)', drawBorder: false },
                        ticks: { font: { weight: 'bold', size: 9 }, callback: v => 'Rs. ' + v.toLocaleString() }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { weight: 'bold', size: 9 } }
                    }
                }
            }
        });

        // Expense Breakdown Doughnut
        const breakdownCtx = document.getElementById('expenseBreakdownChart').getContext('2d');
        const chartColors = ['#0C8D5D', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];

        // Set colors for the legend elements in the HTML
        chartColors.forEach((color, i) => {
            document.documentElement.style.setProperty(`--chart-color-${i}`, color);
        });

        new Chart(breakdownCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($expenseBreakdown->pluck('category')) !!},
                datasets: [{
                    data: {!! json_encode($expenseBreakdown->pluck('total')) !!},
                    backgroundColor: chartColors,
                    borderWidth: 0,
                    cutout: '80%',
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        padding: 12,
                        backgroundColor: '#0f172a',
                        bodyFont: { weight: 'bold', size: 11 },
                        usePointStyle: true
                    }
                }
            }
        });

    </script>

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