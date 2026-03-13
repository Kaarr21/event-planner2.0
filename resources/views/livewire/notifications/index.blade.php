<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6">
            <div>
                <h2 class="text-4xl font-black tracking-tight text-gray-900 dark:text-slate-100">Notifications</h2>
                <p class="text-gray-500 dark:text-slate-400 mt-2 font-medium">Stay updated with your latest event activities and alerts.</p>
            </div>
            @if($unreadCount > 0)
                <button wire:click="markAllAsRead" class="px-5 py-2.5 bg-white/40 dark:bg-white/5 backdrop-blur-[12px] rounded-xl text-sm font-semibold text-gray-700 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-white/10 transition-all border border-gray-200 dark:border-white/10 shadow-sm">
                    Mark all as read
                </button>
            @endif
        </div>

        <!-- Notifications List -->
        <div class="flex flex-col gap-4">
            @forelse ($notifications as $notification)
                <div class="bg-white/40 dark:bg-white/5 backdrop-blur-[12px] rounded-xl p-5 flex gap-5 border {{ $notification->read ? 'border-gray-200 dark:border-white/5 opacity-70' : 'border-[#257bf4]/30 dark:border-[#257bf4]/30 shadow-lg shadow-[#257bf4]/5' }} transition-all group hover:border-[#257bf4]/50">
                    <div class="size-12 rounded-full {{ $notification->type === 'invite' ? 'bg-[#257bf4]/10 text-[#257bf4]' : 'bg-amber-500/10 text-amber-500' }} flex items-center justify-center border {{ $notification->type === 'invite' ? 'border-[#257bf4]/20' : 'border-amber-500/20' }} shrink-0">
                        <span class="material-symbols-outlined">
                            {{ $notification->type === 'invite' ? 'mail' : 'notifications' }}
                        </span>
                    </div>
                    
                    <div class="flex-grow">
                        <div class="flex justify-between items-start mb-1 gap-4">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-slate-100">{{ $notification->title }}</h3>
                            <span class="text-[10px] font-bold text-gray-400 dark:text-slate-500 bg-gray-100 dark:bg-slate-800/50 px-2 py-1 rounded uppercase tracking-wider">
                                {{ $notification->created_at?->diffForHumans() }}
                            </span>
                        </div>
                        <p class="text-gray-600 dark:text-slate-400 text-sm leading-relaxed mb-4">
                            {{ $notification->message }}
                        </p>
                        
                        <div class="flex gap-3">
                            @if($notification->type === 'invite' && !$notification->read)
                                <button wire:click="acceptInvite({{ $notification->id }})" class="px-6 py-2 bg-[#257bf4] text-white text-xs font-bold rounded-lg hover:brightness-110 transition-all shadow-md shadow-[#257bf4]/10 flex items-center gap-2 uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-sm">check</span>
                                    Accept
                                </button>
                                <button wire:click="declineInvite({{ $notification->id }})" class="px-6 py-2 bg-white/50 dark:bg-white/5 text-gray-700 dark:text-slate-300 text-xs font-bold rounded-lg hover:bg-gray-100 dark:hover:bg-white/10 transition-all border border-gray-200 dark:border-white/10 uppercase tracking-wider">
                                    Decline
                                </button>
                            @endif

                            @if(!$notification->read)
                                <button wire:click="markAsRead({{ $notification->id }})" class="text-xs font-bold text-gray-400 dark:text-slate-500 hover:text-[#257bf4] transition-colors uppercase tracking-widest">
                                    Mark as read
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 bg-white/40 dark:bg-white/5 backdrop-blur-[12px] border-2 border-dashed border-gray-200 dark:border-white/10 rounded-xl flex flex-col items-center justify-center text-center">
                    <div class="w-20 h-20 rounded-full bg-gray-100 dark:bg-white/5 flex items-center justify-center mb-6">
                        <span class="material-symbols-outlined text-gray-400 dark:text-slate-500 text-4xl">notifications_off</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-slate-100">All clear!</h3>
                    <p class="text-gray-500 dark:text-slate-400 mt-2 max-w-xs">You don't have any new notifications at the moment.</p>
                </div>
            @endforelse
        </div>
        
        @if(count($notifications) > 0)
            <div class="mt-12 flex justify-center">
                <button class="text-gray-400 dark:text-slate-500 hover:text-[#257bf4] transition-colors text-xs font-bold flex items-center gap-2 uppercase tracking-widest">
                    <span>View older notifications</span>
                    <span class="material-symbols-outlined text-lg">expand_more</span>
                </button>
            </div>
        @endif
    </div>
</div>
