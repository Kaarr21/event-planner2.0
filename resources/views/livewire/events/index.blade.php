<div class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if (session('message'))
            <div class="mb-12 bg-emerald-50 border border-emerald-100 p-6 rounded-[2rem] flex items-center justify-between gap-6 animate-in fade-in slide-in-from-top-6 duration-700 shadow-lux">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 flex items-center justify-center">
                        <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                    </div>
                    <div>
                        <p class="text-base font-semibold text-slate-900">{{ session('message') }}</p>
                        @if (session('show_invite_reminder'))
                            <p class="text-sm text-emerald-600/80 font-medium tracking-tight">Your event is live! Let's bring in the guests.</p>
                        @endif
                    </div>
                </div>
                @if (session('show_invite_reminder'))
                    <a href="{{ route('events.show', session('new_event_id')) }}" class="btn-lux bg-emerald-600 hover:bg-emerald-700 shadow-emerald-100">
                        Invite Guests
                    </a>
                @endif
            </div>
        @endif

        <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-8">
            <div class="max-w-2xl">
                <h1 class="text-6xl font-display font-extrabold text-slate-900 tracking-tight leading-tight">
                    Your <span class="lux-gradient-text">Portfolio</span>
                </h1>
                <p class="text-slate-500 mt-4 text-xl font-medium tracking-tight max-w-lg">
                    Manage and curate your world-class event experiences with precision and grace.
                </p>
            </div>
            <div class="flex items-center">
                <a href="{{ route('events.create') }}" class="btn-lux group">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined font-bold transition-transform group-hover:rotate-90 duration-300">add</span>
                        <span>Create Masterpiece</span>
                    </div>
                </a>
            </div>
        </div>

        <!-- Events Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
            @forelse ($events as $event)
                <div class="glass-card glass-card-hover group border-none">
                    <div class="relative h-72 overflow-hidden">
                        <div class="absolute inset-0 bg-cover bg-center group-hover:scale-105 transition-transform duration-1000 ease-out" 
                             style="background-image: url('{{ $event->banner_image_path ? Storage::url($event->banner_image_path) : 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?q=80&w=1000&auto=format&fit=crop' }}')"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/20 to-transparent opacity-80"></div>
                        
                        <!-- Status Badge -->
                        <div class="absolute top-8 left-8">
                            <div class="flex items-center gap-2.5 px-3 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 shadow-sm">
                                 <div class="w-2 h-2 rounded-full animate-pulse
                                    {{ $event->status === 'published' ? 'bg-emerald-400 shadow-[0_0_12px_rgba(52,211,153,0.8)]' : '' }}
                                    {{ $event->status === 'draft' ? 'bg-indigo-400 shadow-[0_0_12px_rgba(129,140,248,0.8)]' : '' }}
                                    {{ $event->status === 'cancelled' ? 'bg-rose-400 shadow-[0_0_12px_rgba(251,113,133,0.8)]' : '' }}
                                 "></div>
                                <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-white">
                                    {{ $event->status }}
                                </span>
                            </div>
                        </div>

                        <!-- Countdown Timer -->
                        <div x-data="{
                            target: new Date('{{ $event->start_at ? $event->start_at->toIso8601String() : ($event->date ? $event->date->toIso8601String() : '') }}').getTime(),
                            timeLeft: '',
                            update() {
                                const now = new Date().getTime();
                                const diff = this.target - now;
                                if (isNaN(this.target)) return;
                                if (diff <= 0) {
                                    this.timeLeft = 'Ongoing';
                                    return;
                                }
                                const hoursTotal = diff / (1000 * 60 * 60);
                                if (hoursTotal >= 24) {
                                    const days = Math.ceil(diff / (1000 * 60 * 60 * 24));
                                    this.timeLeft = `Live in ${days} Days`;
                                } else {
                                    const hours = Math.floor(diff / (1000 * 60 * 60));
                                    const mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                                    this.timeLeft = `${hours}h ${mins}m`;
                                }
                            }
                        }" 
                        x-init="update(); setInterval(() => update(), 60000)"
                        class="absolute top-8 right-8"
                        >
                            <div x-show="timeLeft" 
                                 class="px-4 py-1.5 rounded-full bg-white shadow-lux text-slate-900 text-[10px] font-bold uppercase tracking-[0.1em] flex items-center gap-2 border border-slate-100">
                                <span class="material-symbols-outlined text-[16px] text-indigo-600">timer</span>
                                <span x-text="timeLeft"></span>
                            </div>
                        </div>

                        <div class="absolute bottom-8 left-8 right-8">
                            <span class="text-indigo-300 text-[10px] font-bold uppercase tracking-[0.2em] mb-3 block">{{ $event->category?->name ?? 'EXCLUSIVE EVENT' }}</span>
                            <h3 class="text-2xl font-display font-bold text-white tracking-tight leading-tight group-hover:text-indigo-200 transition-colors">
                                {{ $event->title }}
                            </h3>
                        </div>
                    </div>
                    
                    <div class="p-10">
                        @if($event->user_id !== auth()->id())
                            <div class="mb-8 flex flex-wrap gap-2">
                                @if($event->organizers->contains('id', auth()->id()))
                                    <span class="bg-indigo-50 text-indigo-600 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                                        Organizer
                                    </span>
                                @endif

                                @php
                                    $userRSVP = $event->rsvps->where('user_id', auth()->id())->first();
                                @endphp

                                @if($userRSVP && ($userRSVP->status === 'attending' || $userRSVP->status === 'maybe'))
                                    <span class="bg-emerald-50 text-emerald-600 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                                        Confirmed Guest
                                    </span>
                                @endif
                            </div>
                        @endif
                        
                        <div class="space-y-4 mb-10">
                            <div class="flex items-center gap-4 text-slate-500 text-sm font-medium">
                                <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-indigo-600 !text-xl opacity-80">calendar_today</span>
                                </div>
                                <span>{{ $event->start_at ? $event->start_at->format('M d, Y') : ($event->date ? $event->date->format('M d, Y') : 'Date Pending') }}</span>
                            </div>
                            <div class="flex items-center gap-4 text-slate-500 text-sm font-medium">
                                <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-emerald-600 !text-xl opacity-80">near_me</span>
                                </div>
                                <span class="line-clamp-1">{{ $event->location ?: 'Location Pending' }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-6 border-t border-slate-50">
                            <div class="flex items-center gap-4">
                                <div class="flex -space-x-3">
                                    @for($i = 0; $i < min(3, $event->rsvps_count); $i++)
                                        <div class="w-9 h-9 rounded-full bg-slate-100 border-2 border-white flex items-center justify-center text-[10px] font-bold text-slate-400">
                                            <span class="material-symbols-outlined !text-xs">person</span>
                                        </div>
                                    @endfor
                                </div>
                                <span class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">{{ $event->rsvps_count ?? 0 }} Guests</span>
                            </div>
                            <a href="{{ route('events.show', $event) }}" class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all duration-300 shadow-sm">
                                <span class="material-symbols-outlined font-bold">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-32 glass-card rounded-[3rem] flex flex-col items-center justify-center text-center border-none">
                    <div class="w-24 h-24 rounded-3xl bg-indigo-50 flex items-center justify-center mb-8 shadow-inner shadow-indigo-100/50">
                        <span class="material-symbols-outlined text-indigo-600 text-5xl">event</span>
                    </div>
                    <h3 class="text-3xl font-display font-bold text-slate-900 tracking-tight">Your Stage is Empty</h3>
                    <p class="text-slate-500 mt-4 max-w-sm text-lg font-medium tracking-tight">The world awaits your next remarkable event. Begin your journey today.</p>
                    <div class="mt-12">
                        <a href="{{ route('events.create') }}" class="btn-lux shadow-indigo-100">
                            Start Planning
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

