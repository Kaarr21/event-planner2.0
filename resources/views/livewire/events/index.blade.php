<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if (session('message'))
            <div class="mb-8 bg-emerald-500/10 border border-emerald-500/20 backdrop-blur-xl p-4 rounded-xl flex items-center justify-between gap-4 animate-in fade-in slide-in-from-top-4 duration-500">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-500/20 flex items-center justify-center">
                        <span class="material-symbols-outlined text-emerald-500">check_circle</span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ session('message') }}</p>
                        @if (session('show_invite_reminder'))
                            <p class="text-xs text-emerald-600/80 dark:text-emerald-400/80 font-medium">Your event is live! Now, let's bring in the guests.</p>
                        @endif
                    </div>
                </div>
                @if (session('show_invite_reminder'))
                    <a href="{{ route('events.show', session('new_event_id')) }}" class="bg-emerald-500 hover:bg-emerald-600 text-white text-[10px] font-black uppercase tracking-widest px-4 py-2 rounded-lg transition-all shadow-lg shadow-emerald-500/20">
                        Invite Guests Now
                    </a>
                @endif
            </div>
        @endif
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
            <div>
                <h2 class="text-5xl font-black text-gray-900 dark:text-white tracking-tighter uppercase italic">My <span class="text-brand-orange">Events</span></h2>
                <p class="text-gray-500 dark:text-slate-400 mt-3 font-medium text-lg italic">Premium planning for unforgettable experiences.</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('events.create') }}" class="btn-brand pamoja-gradient flex items-center gap-2 shadow-xl shadow-brand-orange/20">
                    <span class="material-symbols-outlined font-bold">add</span>
                    <span>Create New Event</span>
                </a>
            </div>
        </div>

        <!-- Events Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            @forelse ($events as $event)
                <div class="glass-card dark:glass-card-dark rounded-[2.5rem] overflow-hidden group hover:scale-[1.02] transition-all duration-500 border-none">
                    <div class="relative h-64 overflow-hidden">
                        <div class="absolute inset-0 bg-cover bg-center group-hover:scale-110 transition-transform duration-700" 
                             style="background-image: url('{{ $event->banner_image_path ? Storage::disk('public')->url($event->banner_image_path) : 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?q=80&w=1000&auto=format&fit=crop' }}')"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-brand-dark/90 via-brand-dark/20 to-transparent"></div>
                        
                        <!-- Status Badge -->
                        <div class="absolute top-6 left-6 flex items-center gap-2">
                             <div class="w-2 h-2 rounded-full animate-pulse
                                {{ $event->status === 'published' ? 'bg-brand-teal shadow-[0_0_12px_rgba(30,167,166,0.8)]' : '' }}
                                {{ $event->status === 'draft' ? 'bg-brand-orange shadow-[0_0_12px_rgba(242,139,36,0.8)]' : '' }}
                                {{ $event->status === 'cancelled' ? 'bg-brand-red shadow-[0_0_12px_rgba(233,78,51,0.8)]' : '' }}
                             "></div>
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-white/90 drop-shadow-md">
                                {{ $event->status }}
                            </span>
                        </div>

                        <div class="absolute bottom-6 left-6 right-6">
                            <span class="text-brand-orange text-[10px] font-black uppercase tracking-[0.3em] mb-2 block">{{ $event->category?->name ?? 'MEMORABLE EVENT' }}</span>
                            <h3 class="text-2xl font-black text-white italic tracking-tight leading-none group-hover:text-brand-orange transition-colors">
                                {{ $event->title }}
                            </h3>
                        </div>
                    </div>
                    
                    <div class="p-8">
                        @if($event->user_id !== auth()->id())
                            <div class="mb-6 flex flex-wrap gap-2">
                                @if($event->organizers->contains('id', auth()->id()))
                                    <span class="bg-brand-orange/10 text-brand-orange text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-wider flex items-center gap-1">
                                        Organizer
                                    </span>
                                @endif

                                @php
                                    $userRSVP = $event->rsvps->where('user_id', auth()->id())->first();
                                @endphp

                                @if($userRSVP && ($userRSVP->status === 'attending' || $userRSVP->status === 'maybe'))
                                    <span class="bg-brand-teal/10 text-brand-teal text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-wider flex items-center gap-1">
                                        Confirmed Guest
                                    </span>
                                @endif
                            </div>
                        @endif
                        
                        <div class="space-y-4 mb-8">
                            <div class="flex items-center gap-4 text-gray-500 dark:text-slate-400 text-sm font-bold italic">
                                <span class="material-symbols-outlined text-brand-orange !text-xl opacity-80">calendar_today</span>
                                <span>{{ $event->start_at ? $event->start_at->format('D, M d, Y') : ($event->date ? $event->date->format('D, M d, Y') : 'Date Pending') }}</span>
                            </div>
                            <div class="flex items-center gap-4 text-gray-500 dark:text-slate-400 text-sm font-bold italic">
                                <span class="material-symbols-outlined text-brand-teal !text-xl opacity-80">near_me</span>
                                <span class="line-clamp-1">{{ $event->location ?: 'Location Pending' }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex -space-x-3">
                                    @for($i = 0; $i < min(3, $event->rsvps_count); $i++)
                                        <div class="w-8 h-8 rounded-full bg-brand-orange/20 border-2 border-white dark:border-brand-dark flex items-center justify-center text-[10px] font-black text-brand-orange">
                                            <span class="material-symbols-outlined !text-xs">person</span>
                                        </div>
                                    @endfor
                                </div>
                                <span class="text-gray-400 dark:text-slate-500 text-[10px] font-black uppercase tracking-widest">{{ $event->rsvps_count ?? 0 }} Attendees</span>
                            </div>
                            <a href="{{ route('events.show', $event) }}" class="w-12 h-12 rounded-full border border-brand-orange/30 flex items-center justify-center text-brand-orange hover:bg-brand-orange hover:text-white transition-all shadow-lg hover:shadow-brand-orange/40">
                                <span class="material-symbols-outlined font-bold">arrow_outward</span>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-24 glass-card dark:glass-card-dark rounded-[3rem] flex flex-col items-center justify-center text-center border-none">
                    <div class="w-24 h-24 rounded-full bg-brand-orange/10 flex items-center justify-center mb-8">
                        <span class="material-symbols-outlined text-brand-orange text-5xl">event</span>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 dark:text-white uppercase italic tracking-tighter">Your Stage is Empty</h3>
                    <p class="text-gray-500 dark:text-slate-400 mt-3 max-w-sm text-lg italic">The world awaits your next remarkable event. Start creating today.</p>
                    <div class="mt-10">
                        <a href="{{ route('events.create') }}" class="btn-brand pamoja-gradient shadow-xl shadow-brand-orange/30">
                            Plan Your First Masterpiece
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
