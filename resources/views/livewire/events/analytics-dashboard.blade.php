<div class="space-y-8 py-4" x-data="{ 
    initCharts() {
        const rsvpData = @js($data['rsvp']);
        const budgetData = @js($data['budget']['by_category']);
        
        // RSVP Donut Chart
        new ApexCharts(this.$refs.rsvpChart, {
            chart: { type: 'donut', height: 350, fontFamily: 'inherit' },
            labels: rsvpData.labels.map(l => l.charAt(0).toUpperCase() + l.slice(1)),
            series: rsvpData.series,
            colors: ['#6366f1', '#10b981', '#f59e0b', '#ef4444'],
            dataLabels: { enabled: false },
            legend: { position: 'bottom', labels: { colors: '#9ca3af' } },
            stroke: { show: false },
            plotOptions: {
                pie: {
                    donut: {
                        size: '75%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total RSVPs',
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
            chart: { type: 'bar', height: 350, fontFamily: 'inherit', toolbar: { show: false } },
            series: [{ name: 'Spent', data: budgetData.series }],
            plotOptions: { 
                bar: { 
                    horizontal: true, 
                    borderRadius: 8,
                    barHeight: '60%'
                } 
            },
            colors: ['#6366f1'],
            xaxis: {
                categories: budgetData.labels,
                labels: { style: { colors: '#9ca3af' } }
            },
            yaxis: {
                labels: { style: { colors: '#9ca3af' } }
            },
            grid: { borderColor: '#334155', strokeDashArray: 4 },
            tooltip: { theme: 'dark' }
        }).render();
    }
}" x-init="initCharts()">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Guest Stat -->
        <div class="p-8 bg-white dark:bg-[#1e293b] rounded-[2.5rem] border border-gray-100 dark:border-white/5 shadow-xl relative overflow-hidden group">
            <div class="flex flex-col gap-4">
                <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 flex items-center justify-center text-indigo-500">
                    <span class="material-symbols-outlined">group</span>
                </div>
                <div>
                    <p class="text-[10px] text-gray-400 uppercase tracking-widest font-black">Guest Engagement</p>
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-1">{{ array_sum($data['rsvp']['series']) }}</h3>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-emerald-500">{{ $data['rsvp']['series'][array_search('attending', $data['rsvp']['labels'])] ?? 0 }} Attending</span>
                </div>
            </div>
        </div>

        <!-- Budget Stat -->
        <div class="p-8 bg-white dark:bg-[#1e293b] rounded-[2.5rem] border border-gray-100 dark:border-white/5 shadow-xl relative overflow-hidden group">
            <div class="flex flex-col gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                    <span class="material-symbols-outlined">payments</span>
                </div>
                <div>
                    <p class="text-[10px] text-gray-400 uppercase tracking-widest font-black">Total Spent</p>
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-1">${{ number_format($data['budget']['actual'], 2) }}</h3>
                </div>
                <div class="w-full bg-gray-100 dark:bg-white/5 rounded-full h-1.5 mt-1">
                    @php
                        $budgetPerc = $data['budget']['estimated'] > 0 ? min(($data['budget']['actual'] / $data['budget']['estimated']) * 100, 100) : 0;
                    @endphp
                    <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $budgetPerc }}%"></div>
                </div>
                <p class="text-[10px] font-bold text-gray-500 uppercase">Of ${{ number_format($data['budget']['estimated'], 2) }} budget</p>
            </div>
        </div>

        <!-- Task Progress -->
        <div class="p-8 bg-white dark:bg-[#1e293b] rounded-[2.5rem] border border-gray-100 dark:border-white/5 shadow-xl relative overflow-hidden group">
            <div class="flex flex-col gap-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/10 flex items-center justify-center text-amber-500">
                    <span class="material-symbols-outlined">checklist</span>
                </div>
                <div>
                    <p class="text-[10px] text-gray-400 uppercase tracking-widest font-black">Tasks Completed</p>
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-1">{{ $data['tasks']['percentage'] }}%</h3>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-amber-500">{{ $data['tasks']['completed'] }} / {{ $data['tasks']['total'] }} Tasks</span>
                </div>
            </div>
        </div>

        <!-- Payments -->
        <div class="p-8 bg-white dark:bg-[#1e293b] rounded-[2.5rem] border border-gray-100 dark:border-white/5 shadow-xl relative overflow-hidden group">
            <div class="flex flex-col gap-4">
                <div class="w-12 h-12 rounded-2xl bg-rose-500/10 flex items-center justify-center text-rose-500">
                    <span class="material-symbols-outlined">account_balance_wallet</span>
                </div>
                <div>
                    <p class="text-[10px] text-gray-400 uppercase tracking-widest font-black">Amount Paid</p>
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-1">${{ number_format($data['budget']['paid'], 2) }}</h3>
                </div>
                <div class="flex items-center gap-2">
                    @php
                        $paidPerc = $data['budget']['actual'] > 0 ? round(($data['budget']['paid'] / $data['budget']['actual']) * 100) : 0;
                    @endphp
                    <span class="text-xs font-bold text-rose-500">{{ $paidPerc }}% Collected</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- RSVP Chart -->
        <div class="p-8 bg-white dark:bg-[#1e293b] rounded-[2.5rem] border border-gray-100 dark:border-white/5 shadow-xl">
            <div class="flex items-center justify-between mb-8">
                <h4 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight">RSVP Distribution</h4>
                <div class="w-8 h-8 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-500">
                    <span class="material-symbols-outlined text-sm">pie_chart</span>
                </div>
            </div>
            <div x-ref="rsvpChart"></div>
        </div>

        <!-- Budget Chart -->
        <div class="p-8 bg-white dark:bg-[#1e293b] rounded-[2.5rem] border border-gray-100 dark:border-white/5 shadow-xl">
            <div class="flex items-center justify-between mb-8">
                <h4 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight">Spending by Category</h4>
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                    <span class="material-symbols-outlined text-sm">bar_chart</span>
                </div>
            </div>
            <div x-ref="budgetChart"></div>
        </div>
    </div>
</div>
