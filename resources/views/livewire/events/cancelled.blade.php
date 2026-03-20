<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6">
            <div>
                <h2 class="text-4xl font-extrabold text-gray-900 dark:text-slate-100 tracking-tight text-red-600 dark:text-red-500">Cancelled Events</h2>
                <p class="text-gray-500 dark:text-slate-400 mt-2 font-medium">History of events that were cancelled by you or your organizers.</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('events.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 font-bold flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    Back to My Events
                </a>
            </div>
        </div>

        <!-- Events Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse ($events as $event)
                <div class="bg-gray-50/50 dark:bg-white/5 backdrop-blur-[12px] border border-red-200/50 dark:border-red-900/20 rounded-xl overflow-hidden group grayscale hover:grayscale-0 transition-all duration-500 shadow-xl relative opacity-80 hover:opacity-100">
                    <div class="relative h-48 overflow-hidden">
                        <div class="absolute inset-0 bg-cover bg-center group-hover:scale-105 transition-transform duration-700" 
                             style="background-image: url('https://images.unsplash.com/photo-1511795409834-ef04bbd61622?q=80&w=1000&auto=format&fit=crop')"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                        
                        <!-- Status Badge -->
                        <div class="absolute top-4 left-4 border backdrop-blur-md text-[10px] font-black px-2.5 py-1 rounded-lg uppercase tracking-widest bg-red-600 text-white border-red-500 shadow-lg shadow-red-500/20">
                            {{ $event->status }}
                        </div>

                        <div class="absolute bottom-4 left-6 right-6">
                            <h3 class="text-xl font-black text-white uppercase tracking-tighter line-clamp-1">
                                {{ $event->title }}
                            </h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="mb-4">
                            <p class="text-[10px] font-black uppercase tracking-widest text-red-500 dark:text-red-400 mb-1">Reason for Cancellation</p>
                            <p class="text-gray-600 dark:text-slate-400 text-sm italic line-clamp-2">
                                "{{ $event->cancellation_reason ?: 'No reason provided.' }}"
                            </p>
                        </div>
                        
                        <div class="space-y-3 mt-4">
                            <div class="flex items-center gap-3 text-gray-500 dark:text-slate-500 text-xs font-bold uppercase tracking-widest">
                                <span class="material-symbols-outlined text-sm">calendar_month</span>
                                <span>{{ \Carbon\Carbon::parse($event->date)->format('M d, Y') }}</span>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end border-t border-gray-100 dark:border-white/5 pt-6">
                            <a href="{{ route('events.show', $event) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 font-black text-[10px] uppercase tracking-widest flex items-center gap-1 group/btn">
                                View Details
                                <span class="material-symbols-outlined text-sm group-hover/btn:translate-x-1 transition-transform">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-24 bg-white/40 dark:bg-white/5 backdrop-blur-[12px] border-2 border-dashed border-gray-200 dark:border-white/10 rounded-[3rem] flex flex-col items-center justify-center text-center">
                    <div class="w-24 h-24 rounded-[2rem] bg-gray-100 dark:bg-white/5 flex items-center justify-center mb-8 border border-white/10 shadow-inner">
                        <span class="material-symbols-outlined text-gray-300 dark:text-slate-600 text-5xl">event_busy</span>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 dark:text-slate-100 uppercase tracking-tighter">No Cancelled Events</h3>
                    <p class="text-gray-500 dark:text-slate-400 mt-3 max-w-xs font-medium">All your events are currently active or archived.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
