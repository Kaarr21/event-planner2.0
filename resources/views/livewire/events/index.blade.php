<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6">
            <div>
                <h2 class="text-4xl font-extrabold text-gray-900 dark:text-slate-100 tracking-tight">My Events</h2>
                <p class="text-gray-500 dark:text-slate-400 mt-2 font-medium">Manage and monitor all your planned experiences.</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('events.create') }}" class="bg-[#257bf4] hover:bg-[#257bf4]/90 text-white font-bold py-3 px-8 rounded-xl flex items-center gap-2 transition-all shadow-lg shadow-[#257bf4]/20 transform hover:-translate-y-0.5">
                    <span class="material-symbols-outlined">add</span>
                    <span>Create Event</span>
                </a>
            </div>
        </div>

        <!-- Events Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse ($events as $event)
                <div class="bg-white/40 dark:bg-white/5 backdrop-blur-[12px] border border-gray-200 dark:border-white/10 rounded-xl overflow-hidden group hover:border-[#257bf4]/50 transition-all duration-300 shadow-xl">
                    <div class="relative h-48 overflow-hidden">
                        <div class="absolute inset-0 bg-cover bg-center group-hover:scale-105 transition-transform duration-500" 
                             style="background-image: url('https://images.unsplash.com/photo-1511795409834-ef04bbd61622?q=80&w=1000&auto=format&fit=crop')"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        
                        <!-- Status Badge -->
                        <div class="absolute top-4 left-4 border backdrop-blur-md text-[10px] font-bold px-2.5 py-1 rounded-lg uppercase tracking-wider
                            {{ $event->status === 'published' ? 'bg-emerald-500/80 text-white border-emerald-400/30' : '' }}
                            {{ $event->status === 'draft' ? 'bg-amber-500/80 text-white border-amber-400/30' : '' }}
                            {{ $event->status === 'archived' ? 'bg-rose-500/80 text-white border-rose-400/30' : '' }}
                            {{ $event->status === 'cancelled' ? 'bg-red-600/90 text-white border-red-500/30' : '' }}
                         shadow-lg">
                            {{ $event->status }}
                        </div>

                        <div class="absolute top-4 right-4 bg-white/20 text-white text-[10px] font-bold px-2.5 py-1 rounded-lg backdrop-blur-md uppercase tracking-wider border border-white/10">
                            {{ $event->category ?? 'EVENT' }}
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-slate-100 group-hover:text-[#257bf4] transition-colors line-clamp-1">
                                {{ $event->title }}
                            </h3>
                        </div>
                        
                        @if($event->user_id !== auth()->id())
                            <div class="mb-4 flex flex-wrap gap-2">
                                @if($event->organizers->contains('id', auth()->id()))
                                    <span class="bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-wider flex items-center gap-1 w-fit">
                                        <span class="material-symbols-outlined text-[14px]">id_card</span>
                                        Organizer
                                    </span>
                                @endif

                                @php
                                    $userRSVP = $event->rsvps->where('user_id', auth()->id())->first();
                                @endphp

                                @if($userRSVP && ($userRSVP->status === 'attending' || $userRSVP->status === 'maybe'))
                                    <span class="bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-wider flex items-center gap-1 w-fit border border-emerald-200 dark:border-emerald-800">
                                        <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                        Guest
                                    </span>
                                @endif

                                @php
                                    $pendingInvite = $event->receivedInvites->where('status', 'pending')->first();
                                @endphp

                                @if($pendingInvite)
                                    <span class="bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-wider flex items-center gap-1 w-fit border border-amber-200 dark:border-amber-800 animate-pulse">
                                        <span class="material-symbols-outlined text-[14px]">mail</span>
                                        Invited
                                    </span>
                                @endif
                            </div>
                        @endif
                        
                        <div class="space-y-3 mt-4">
                            <div class="flex items-center gap-3 text-gray-600 dark:text-slate-400 text-sm font-medium">
                                <span class="material-symbols-outlined text-[#257bf4] text-xl">calendar_month</span>
                                <span>{{ \Carbon\Carbon::parse($event->date)->format('M d, Y @ H:i') }}</span>
                            </div>
                            <div class="flex items-center gap-3 text-gray-600 dark:text-slate-400 text-sm font-medium">
                                <span class="material-symbols-outlined text-[#257bf4] text-xl">location_on</span>
                                <span class="line-clamp-1">{{ $event->location ?: 'Online / TBD' }}</span>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-between border-t border-gray-100 dark:border-white/5 pt-6">
                            <div class="flex items-center gap-2">
                                <div class="flex -space-x-2">
                                    <div class="w-7 h-7 rounded-full bg-gray-200 dark:bg-gray-700 border-2 border-white dark:border-[#101722] flex items-center justify-center text-[10px] font-bold text-gray-500">
                                        {{ $event->rsvps_count > 0 ? '+' . ($event->rsvps_count > 3 ? 3 : $event->rsvps_count) : '0' }}
                                    </div>
                                </div>
                                <span class="text-gray-500 dark:text-slate-500 text-xs font-semibold">{{ $event->rsvps_count ?? 0 }} RSVPs</span>
                            </div>
                            <a href="{{ route('events.show', $event) }}" class="text-[#257bf4] hover:underline font-bold text-sm flex items-center gap-1">
                                Details
                                <span class="material-symbols-outlined text-sm">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 bg-white/40 dark:bg-white/5 backdrop-blur-[12px] border-2 border-dashed border-gray-200 dark:border-white/10 rounded-xl flex flex-col items-center justify-center text-center">
                    <div class="w-20 h-20 rounded-full bg-gray-100 dark:bg-white/5 flex items-center justify-center mb-6">
                        <span class="material-symbols-outlined text-gray-400 dark:text-slate-500 text-4xl">event_busy</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-slate-100">No events found</h3>
                    <p class="text-gray-500 dark:text-slate-400 mt-2 max-w-xs">You haven't created any events yet. Start planning your first experience!</p>
                    <div class="mt-8">
                        <a href="{{ route('events.create') }}" class="bg-[#257bf4] hover:bg-[#257bf4]/90 text-white font-bold py-3 px-8 rounded-xl transition-all shadow-lg shadow-[#257bf4]/20">
                            Create First Event
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
