<div class="space-y-12 py-4" x-data="{ 
    initCharts() {
        const rsvpData = @js($data['rsvp']);
        const budgetData = @js($data['budget']['by_category']);
        
        // RSVP Donut Chart
        new ApexCharts(this.$refs.rsvpChart, {
            chart: { type: 'donut', height: 350, fontFamily: 'inherit', background: 'transparent' },
            labels: rsvpData.labels.map(l => l.charAt(0).toUpperCase() + l.slice(1)),
            series: rsvpData.series,
            colors: ['#30D5C8', '#F28B24', '#FFD700', '#FF3131'], // Teal, Orange, Yellow, Red
            dataLabels: { enabled: false },
            legend: { 
                position: 'bottom', 
                labels: { colors: '#9ca3af', useSeriesColors: false },
                fontFamily: 'inherit',
                fontWeight: 900,
                markers: { radius: 12 }
            },
            stroke: { show: false },
            plotOptions: {
                pie: {
                    donut: {
                        size: '80%',
                        labels: {
                            show: true,
                            name: { show: true, fontSize: '12px', fontWeight: 900, color: '#9ca3af', offsetY: -10 },
                            value: { show: true, fontSize: '32px', fontWeight: 900, color: '#ffffff', offsetY: 10, fontStyle: 'italic' },
                            total: {
                                show: true,
                                label: 'TALENTS',
                                color: '#9ca3af',
                                formatter: function (w) {
                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                                }
                            }
                        }
                    }
                }
            }
        }).render();

        // Budget Horizontal Bar Chart
        new ApexCharts(this.$refs.budgetChart, {
            chart: { type: 'bar', height: 350, fontFamily: 'inherit', toolbar: { show: false }, background: 'transparent' },
            series: [{ name: 'Investment', data: budgetData.series }],
            plotOptions: { 
                bar: { 
                    horizontal: true, 
                    borderRadius: 12,
                    barHeight: '50%',
                    distributed: true
                } 
            },
            colors: ['#F28B24', '#30D5C8', '#FFD700', '#FF3131', '#6366f1'],
            xaxis: {
                categories: budgetData.labels,
                labels: { style: { colors: '#9ca3af', fontWeight: 900, fontSize: '10px' } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: { style: { colors: '#9ca3af', fontWeight: 900, fontSize: '10px' } }
            },
            grid: { borderColor: 'rgba(255,255,255,0.05)', strokeDashArray: 4, padding: { left: 20 } },
            tooltip: { theme: 'dark' },
            dataLabels: { enabled: false }
        }).render();
    }
}" x-init="initCharts()">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <!-- Guest Stat -->
        <div class="glass-card dark:glass-card-dark p-10 rounded-[3rem] border-none shadow-3xl group hover:-translate-y-1 transition-all duration-500 overflow-hidden relative">
            <div class="absolute top-0 right-0 p-8 opacity-5">
                <span class="material-symbols-outlined text-7xl">group</span>
            </div>
            <div class="flex flex-col gap-6 relative z-10">
                <div class="w-14 h-14 rounded-2xl bg-brand-teal/10 flex items-center justify-center text-brand-teal group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined font-black text-2xl">group</span>
                </div>
                <div>
                    <h3 class="text-4xl font-black text-gray-900 dark:text-white italic tracking-tighter leading-none mb-2">{{ array_sum($data['rsvp']['series']) }}</h3>
                    <p class="text-[10px] text-gray-400 uppercase tracking-[0.3em] font-black opacity-60">Guest Engagement</p>
                </div>
                <div class="flex items-center gap-2">
                    <div class="px-3 py-1 rounded-full bg-brand-teal/10 border border-brand-teal/20 text-[8px] font-black text-brand-teal uppercase tracking-widest">
                        {{ $data['rsvp']['series'][array_search('attending', $data['rsvp']['labels'])] ?? 0 }} Attending
                    </div>
                </div>
            </div>
        </div>

        <!-- Budget Stat -->
        <div class="glass-card dark:glass-card-dark p-10 rounded-[3rem] border-none shadow-3xl group hover:-translate-y-1 transition-all duration-500 overflow-hidden relative">
            <div class="absolute top-0 right-0 p-8 opacity-5">
                <span class="material-symbols-outlined text-7xl">payments</span>
            </div>
            <div class="flex flex-col gap-6 relative z-10">
                <div class="w-14 h-14 rounded-2xl bg-brand-orange/10 flex items-center justify-center text-brand-orange group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined font-black text-2xl">payments</span>
                </div>
                <div>
                    <h3 class="text-4xl font-black text-gray-900 dark:text-white italic tracking-tighter leading-none mb-2">${{ number_format($data['budget']['actual'], 0) }}</h3>
                    <p class="text-[10px] text-gray-400 uppercase tracking-[0.3em] font-black opacity-60">Total Expenditure</p>
                </div>
                <div class="space-y-3">
                    <div class="w-full bg-white/5 rounded-full h-2 border border-white/5 overflow-hidden">
                        @php
                            $budgetPerc = $data['budget']['estimated'] > 0 ? min(($data['budget']['actual'] / $data['budget']['estimated']) * 100, 100) : 0;
                        @endphp
                        <div class="bg-brand-orange h-full shadow-[0_0_15px_rgba(242,139,36,0.4)] transition-all duration-1000" style="width: {{ $budgetPerc }}%"></div>
                    </div>
                    <p class="text-[8px] font-black text-gray-500 uppercase tracking-widest italic text-right">Of ${{ number_format($data['budget']['estimated'], 0) }} limit</p>
                </div>
            </div>
        </div>

        <!-- Task Progress -->
        <div class="glass-card dark:glass-card-dark p-10 rounded-[3rem] border-none shadow-3xl group hover:-translate-y-1 transition-all duration-500 overflow-hidden relative">
            <div class="absolute top-0 right-0 p-8 opacity-5">
                <span class="material-symbols-outlined text-7xl">checklist</span>
            </div>
            <div class="flex flex-col gap-6 relative z-10">
                <div class="w-14 h-14 rounded-2xl bg-brand-yellow/10 flex items-center justify-center text-brand-yellow group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined font-black text-2xl">checklist</span>
                </div>
                <div>
                    <h3 class="text-4xl font-black text-gray-900 dark:text-white italic tracking-tighter leading-none mb-2">{{ $data['tasks']['percentage'] }}%</h3>
                    <p class="text-[10px] text-gray-400 uppercase tracking-[0.3em] font-black opacity-60">Mission Completion</p>
                </div>
                <div class="flex items-center gap-2">
                    <div class="px-3 py-1 rounded-full bg-brand-yellow/10 border border-brand-yellow/20 text-[8px] font-black text-brand-yellow uppercase tracking-widest">
                        {{ $data['tasks']['completed'] }} / {{ $data['tasks']['total'] }} Verified
                    </div>
                </div>
            </div>
        </div>

        <!-- Payments -->
        <div class="glass-card dark:glass-card-dark p-10 rounded-[3rem] border-none shadow-3xl group hover:-translate-y-1 transition-all duration-500 overflow-hidden relative">
            <div class="absolute top-0 right-0 p-8 opacity-5">
                <span class="material-symbols-outlined text-7xl">account_balance_wallet</span>
            </div>
            <div class="flex flex-col gap-6 relative z-10">
                <div class="w-14 h-14 rounded-2xl bg-brand-red/10 flex items-center justify-center text-brand-red group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined font-black text-2xl">account_balance_wallet</span>
                </div>
                <div>
                    <h3 class="text-4xl font-black text-gray-900 dark:text-white italic tracking-tighter leading-none mb-2">${{ number_format($data['budget']['paid'], 0) }}</h3>
                    <p class="text-[10px] text-gray-400 uppercase tracking-[0.3em] font-black opacity-60">Capital Realized</p>
                </div>
                <div class="flex items-center gap-2">
                    @php
                        $paidPerc = $data['budget']['actual'] > 0 ? round(($data['budget']['paid'] / $data['budget']['actual']) * 100) : 0;
                    @endphp
                    <div class="px-3 py-1 rounded-full bg-brand-red/10 border border-brand-red/20 text-[8px] font-black text-brand-red uppercase tracking-widest">
                        {{ $paidPerc }}% Cleared
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        <!-- RSVP Chart -->
        <div class="glass-card dark:glass-card-dark p-10 rounded-[3.5rem] border-none shadow-3xl">
            <div class="flex items-center justify-between mb-12">
                <div>
                    <h4 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter italic leading-none mb-1">Response Spectrum</h4>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest opacity-60">Real-time attendance metrics.</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-brand-teal/10 flex items-center justify-center text-brand-teal">
                    <span class="material-symbols-outlined font-black">pie_chart</span>
                </div>
            </div>
            <div x-ref="rsvpChart"></div>
        </div>

        <!-- Budget Chart -->
        <div class="glass-card dark:glass-card-dark p-10 rounded-[3.5rem] border-none shadow-3xl">
            <div class="flex items-center justify-between mb-12">
                <div>
                    <h4 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter italic leading-none mb-1">Fiscal Allocation</h4>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest opacity-60">Departmental spending analysis.</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-brand-orange/10 flex items-center justify-center text-brand-orange">
                    <span class="material-symbols-outlined font-black">bar_chart</span>
                </div>
            </div>
            <div x-ref="budgetChart"></div>
        </div>
    </div>
</div>
