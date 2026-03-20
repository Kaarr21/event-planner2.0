<x-slot name="header">
    <div class="flex justify-between items-center">
        <div class="flex items-center gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $event->title }}
            </h2>
            
            @if($userRole !== 'invited')
                <!-- Role Badge -->
                <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest shadow-sm
                    {{ $userRole === 'owner' ? 'bg-amber-100 text-amber-700 border border-amber-200' : '' }}
                    {{ $userRole === 'organizer' ? 'bg-indigo-100 text-indigo-700 border border-indigo-200' : '' }}
                    {{ $userRole === 'guest' ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : '' }}
                ">
                    {{ $userRole }}
                </span>
            @endif

            <!-- Status Badge -->
            <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest shadow-sm border
                {{ $event->status === 'published' ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : '' }}
                {{ $event->status === 'draft' ? 'bg-gray-100 text-gray-700 border-gray-200' : '' }}
                {{ $event->status === 'archived' ? 'bg-rose-100 text-rose-700 border-rose-200' : '' }}
                {{ $event->status === 'cancelled' ? 'bg-red-500 text-white border-red-600' : '' }}
            ">
                {{ $event->status }}
            </span>
        </div>
        <a href="{{ route('dashboard') }}" class="text-sm text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
            &larr; Back to Dashboard
        </a>
    </div>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if($userRole === 'invited')
    <div class="mb-10 p-8 bg-gradient-to-br from-indigo-600/10 via-white/5 to-emerald-600/10 backdrop-blur-3xl border border-white/20 rounded-[3rem] shadow-2xl animate-in fade-in zoom-in duration-700 relative overflow-hidden">
        <!-- Decorative background glow -->
        <div class="absolute -top-24 -left-24 w-48 h-48 bg-indigo-500/20 rounded-full blur-[80px]"></div>
        <div class="absolute -bottom-24 -right-24 w-48 h-48 bg-emerald-500/20 rounded-full blur-[80px]"></div>

        <div class="relative flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="flex flex-col md:flex-row items-center gap-6">
                <div class="w-20 h-20 rounded-[2rem] bg-indigo-600 flex items-center justify-center border border-white/20 shadow-2xl shadow-indigo-500/40 transform -rotate-3 hover:rotate-0 transition-transform duration-500">
                    <span class="material-symbols-outlined text-4xl text-white">mail</span>
                </div>
                <div class="text-center md:text-left">
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tighter leading-none">You're Invited!</h3>
                    <p class="text-gray-500 dark:text-gray-400 mt-3 font-medium text-lg">
                        <span class="text-indigo-500 font-black">{{ $inviter?->name ?? 'Someone' }}</span> 
                        <span class="opacity-50 font-normal">({{ $inviter?->email ?? 'unknown' }})</span>
                        has invited you to join this event team.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-4 w-full md:w-auto">
                <button wire:click="updateRSVP('attending')" class="flex-1 md:flex-none px-10 py-4 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black uppercase tracking-widest rounded-2xl shadow-2xl shadow-indigo-500/40 transition-all hover:scale-105 active:scale-95 group">
                    Accept Invitation
                    <span class="material-symbols-outlined text-sm ml-2 group-hover:translate-x-1 transition-transform">celebration</span>
                </button>
                <button wire:click="updateRSVP('declined')" class="px-8 py-4 bg-white/5 hover:bg-white/10 text-gray-600 dark:text-white text-xs font-black uppercase tracking-widest rounded-2xl border border-white/10 transition-all active:scale-95">
                    Decline
                </button>
            </div>
        </div>
    </div>
@endif

        <!-- Tab Navigation -->
        <div class="mb-8 border-b border-gray-100 dark:border-white/5 overflow-x-auto no-scrollbar">
            <div class="flex space-x-8 min-w-max px-2">
                @php
                    $tabs = [
                        ['id' => 'overview', 'label' => 'Overview', 'icon' => 'info'],
                        ['id' => 'tasks', 'label' => 'Checklist', 'icon' => 'checklist'],
                        ['id' => 'guests', 'label' => 'Guests', 'icon' => 'group'],
                        ['id' => 'media', 'label' => 'Media', 'icon' => 'gallery_thumbnail'],
                    ];
                    if ($this->hasPermission('manage_invites') || $this->hasPermission('owner')) {
                        $tabs[] = ['id' => 'team', 'label' => 'Team', 'icon' => 'shield_person'];
                    }
                @endphp

                @foreach($tabs as $tab)
                    <button 
                        wire:click="setActiveTab('{{ $tab['id'] }}')"
                        class="flex items-center gap-2 py-4 px-1 border-b-2 font-black text-xs uppercase tracking-widest transition-all
                        {{ $activeTab === $tab['id'] 
                            ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' 
                            : 'border-transparent text-gray-400 hover:text-gray-600 dark:hover:text-gray-200' }}"
                    >
                        <span class="material-symbols-outlined text-lg">{{ $tab['icon'] }}</span>
                        {{ $tab['label'] }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Status Controls for Owner -->
        @if($userRole === 'owner')
            @if($event->status === 'draft')
                <div class="mb-8 p-6 bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-500/20 rounded-[2rem] flex flex-col md:flex-row items-center justify-between gap-6 animate-in fade-in slide-in-from-top-4 duration-500">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-amber-500/10 flex items-center justify-center text-amber-600">
                            <span class="material-symbols-outlined">edit_note</span>
                        </div>
                        <div>
                            <h4 class="text-sm font-black uppercase tracking-tight text-amber-800 dark:text-amber-400">Event is in Draft</h4>
                            <p class="text-xs text-amber-700/60 dark:text-amber-400/60">Guests and organizers can be added, but they won't be notified until you publish.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 w-full md:w-auto">
                        <button wire:click="publishEvent(true)" class="flex-1 md:flex-none px-6 py-3 bg-amber-600 hover:bg-amber-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-amber-600/20">
                            Publish & Notify All
                        </button>
                        <button wire:click="publishEvent(false)" class="flex-1 md:flex-none px-6 py-3 bg-white dark:bg-white/5 border border-amber-200 dark:border-white/10 text-amber-700 dark:text-amber-400 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all">
                            Publish Silently
                        </button>
                    </div>
                </div>
            @elseif($event->status === 'published')
                <div class="mb-8 p-6 bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-200 dark:border-emerald-500/20 rounded-[2rem] flex flex-col md:flex-row items-center justify-between gap-6 animate-in fade-in slide-in-from-top-4 duration-500">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-600">
                            <span class="material-symbols-outlined">check_circle</span>
                        </div>
                        <div>
                            <h4 class="text-sm font-black uppercase tracking-tight text-emerald-800 dark:text-emerald-400">Event is Live</h4>
                            <p class="text-xs text-emerald-700/60 dark:text-emerald-400/60">All notifications are being sent instantly to new guests and organizers.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 w-full md:w-auto">
                        <button wire:click="sendInvitations" class="flex-1 md:flex-none px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-indigo-600/20">
                            Re-send Pending Invites
                        </button>
                        <button wire:click="archiveEvent" class="flex-1 md:flex-none px-6 py-3 bg-white dark:bg-white/5 border border-emerald-200 dark:border-white/10 text-emerald-700 dark:text-emerald-400 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all">
                            Archive Event
                        </button>
                    </div>
                </div>
            @elseif($event->status === 'archived')
                <div class="mb-8 p-6 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-[2rem] flex items-center gap-4 animate-in fade-in slide-in-from-top-4 duration-500">
                    <div class="w-12 h-12 rounded-2xl bg-gray-200 dark:bg-white/5 flex items-center justify-center text-gray-500">
                        <span class="material-symbols-outlined">inventory_2</span>
                    </div>
                    <div>
                        <h4 class="text-sm font-black uppercase tracking-tight text-gray-800 dark:text-gray-200">Event Archived</h4>
                        <p class="text-xs text-gray-500">This event has concluded and is now in read-only mode for most features.</p>
                    </div>
                    <button wire:click="publishEvent(false)" class="ml-auto px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-indigo-600/20">
                        Restore to Live
                    </button>
                </div>
            @endif

            @if($event->status === 'cancelled')
                <div class="mb-8 p-8 bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-500/20 rounded-[3rem] animate-in fade-in slide-in-from-top-4 duration-500 relative overflow-hidden">
                    <div class="absolute -top-24 -right-24 w-48 h-48 bg-red-500/10 rounded-full blur-[80px]"></div>
                    <div class="relative flex flex-col md:flex-row items-center gap-6">
                        <div class="w-16 h-16 rounded-[1.5rem] bg-red-600 flex items-center justify-center text-white shadow-xl shadow-red-500/20">
                            <span class="material-symbols-outlined text-3xl">cancel</span>
                        </div>
                        <div class="flex-1 text-center md:text-left">
                            <h4 class="text-xl font-black uppercase tracking-tighter text-red-800 dark:text-red-400">This Event Has Been Cancelled</h4>
                            <p class="text-sm text-red-700/70 dark:text-red-400/70 mt-1">
                                {{ $event->cancellation_reason ?: 'No specific reason was provided for this cancellation.' }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            @if($this->hasPermission('edit_event') && $event->status !== 'cancelled')
                <div class="mb-8 flex justify-end">
                    <button wire:click="confirmCancellation" class="px-6 py-3 bg-white dark:bg-white/5 border border-red-200 dark:border-red-500/20 text-red-600 dark:text-red-400 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all hover:bg-red-50 dark:hover:bg-red-500/10 flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">cancel</span>
                        Cancel Event
                    </button>
                </div>
            @endif
        @endif

        <!-- Tab Content -->
        <div class="mt-6">
            
            <!-- Overview Tab -->
            @if($activeTab === 'overview')
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
                    <div class="lg:col-span-2 space-y-8">
                        <!-- Event Details Card -->
                        <div class="bg-white dark:bg-gray-800/50 dark:backdrop-blur-xl overflow-hidden shadow-sm sm:rounded-[2rem] border border-gray-100 dark:border-white/5">
                            <div class="p-8 md:p-10">
                                <div class="flex items-center gap-4 text-gray-500 dark:text-gray-400 mb-8">
                                    <span class="material-symbols-outlined text-indigo-500">info</span>
                                    <h3 class="text-xs font-black uppercase tracking-widest">About this Event</h3>
                                </div>
                                <p class="text-xl text-gray-700 dark:text-gray-200 leading-relaxed font-medium">
                                    {{ $event->description ?: 'No description provided for this event.' }}
                                </p>
                                
                                @if($userRole !== 'invited')
                                    <!-- Action Cards -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-12 pt-10 border-t border-gray-100 dark:border-white/5 no-print">
                                        <div class="flex items-center gap-5 group">
                                            <div class="w-14 h-14 rounded-2xl bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-600 group-hover:scale-110 transition-transform">
                                                <span class="material-symbols-outlined text-2xl">calendar_month</span>
                                            </div>
                                            <div>
                                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1.5">When</p>
                                                <p class="text-base font-bold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($event->date)->format('F d, Y') }}</p>
                                                <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($event->date)->format('h:i A') }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-5 group">
                                            <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center text-emerald-600 group-hover:scale-110 transition-transform">
                                                <span class="material-symbols-outlined text-2xl">location_on</span>
                                            </div>
                                            <div>
                                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1.5">Where</p>
                                                <p class="text-base font-bold text-gray-900 dark:text-white truncate max-w-[250px]">{{ $event->location ?: 'Online / TBD' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <!-- RSVP Status Card -->
                        @if($userRole !== 'invited')
                            <div class="bg-white dark:bg-gray-800/50 dark:backdrop-blur-xl overflow-hidden shadow-sm sm:rounded-[2rem] border border-gray-100 dark:border-white/5 no-print">
                                <div class="p-8">
                                    <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tighter mb-6">Your Attendance</h3>
                                    
                                    <div class="space-y-4">
                                        <div class="grid grid-cols-1 gap-3">
                                            <button wire:click="updateRSVP('attending')" class="flex items-center justify-between p-4 rounded-2xl border transition-all {{ $userRSVP?->status === 'attending' ? 'bg-emerald-500/10 border-emerald-500 text-emerald-600 shadow-lg shadow-emerald-500/10' : 'border-gray-100 dark:border-white/5 text-gray-500 hover:bg-gray-50 dark:hover:bg-white/5' }}">
                                                <span class="text-xs font-black uppercase tracking-widest">Attending</span>
                                                @if($userRSVP?->status === 'attending') <span class="material-symbols-outlined">check_circle</span> @endif
                                            </button>
                                            <button wire:click="updateRSVP('maybe')" class="flex items-center justify-between p-4 rounded-2xl border transition-all {{ $userRSVP?->status === 'maybe' ? 'bg-amber-500/10 border-amber-500 text-amber-600 shadow-lg shadow-amber-500/10' : 'border-gray-100 dark:border-white/5 text-gray-500 hover:bg-gray-50 dark:hover:bg-white/5' }}">
                                                <span class="text-xs font-black uppercase tracking-widest">Maybe</span>
                                                @if($userRSVP?->status === 'maybe') <span class="material-symbols-outlined">help</span> @endif
                                            </button>
                                            <button wire:click="updateRSVP('declined')" class="flex items-center justify-between p-4 rounded-2xl border transition-all {{ $userRSVP?->status === 'declined' ? 'bg-rose-500/10 border-rose-500 text-rose-600 shadow-lg shadow-rose-500/10' : 'border-gray-100 dark:border-white/5 text-gray-500 hover:bg-gray-50 dark:hover:bg-white/5' }}">
                                                <span class="text-xs font-black uppercase tracking-widest">Declined</span>
                                                @if($userRSVP?->status === 'declined') <span class="material-symbols-outlined">cancel</span> @endif
                                            </button>
                                        </div>
                                        
                                        @if (session()->has('rsvp_message'))
                                            <p class="text-[10px] font-bold text-center text-emerald-600 uppercase tracking-widest animate-pulse">{{ session('rsvp_message') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Location Map Card -->
                <div class="col-span-full mt-12 animate-in fade-in slide-in-from-bottom-6 duration-700 delay-200">
                    <div class="bg-white dark:bg-[#1e293b]/50 dark:backdrop-blur-xl overflow-hidden shadow-sm sm:rounded-[2.5rem] border border-gray-100 dark:border-white/5">
                        <div class="p-8 md:p-12">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10 no-print">
                                <div>
                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-500">
                                            <span class="material-symbols-outlined text-lg">map</span>
                                        </div>
                                        <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">Event Location</h3>
                                    </div>
                                    <p class="text-sm text-gray-500">View directions or sync the exact location pin for your guests.</p>
                                </div>
                                
                                <div class="flex flex-wrap items-center gap-3">
                                    @if($this->hasPermission('manage_invites'))
                                        <button wire:click="bulkShareLocation" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-xl shadow-emerald-500/20 transition-all hover:scale-105 active:scale-95 flex items-center group">
                                            <span class="material-symbols-outlined text-sm mr-2 group-hover:translate-x-1 transition-transform">send</span>
                                            Bulk Share Pin
                                        </button>
                                    @endif
                                    
                                    @if($latitude && $longitude)
                                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $latitude }},{{ $longitude }}&destination_place_id={{ $googlePlaceId }}" target="_blank" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-xl shadow-indigo-500/20 transition-all hover:scale-105 active:scale-95 flex items-center group">
                                            <span class="material-symbols-outlined text-sm mr-2 group-hover:-translate-y-1 transition-transform">directions</span>
                                            Get Directions
                                        </a>

                                        <button onclick="shareLocation('{{ $event->title }}', '{{ $latitude }}', '{{ $longitude }}', '{{ $googlePlaceId }}')" class="px-6 py-3 bg-white/10 hover:bg-white/20 text-white text-[10px] font-black uppercase tracking-widest rounded-xl border border-white/10 transition-all hover:scale-105 active:scale-95 flex items-center group">
                                            <span class="material-symbols-outlined text-sm mr-2 group-hover:rotate-12 transition-transform">share</span>
                                            Share Location
                                        </button>
                                    @endif
                                </div>
                            </div>

                            @if($this->hasPermission('edit_event'))
                                <div class="mb-8 no-print" wire:ignore>
                                    <div class="relative group">
                                        <span class="absolute left-5 top-1/2 -translate-y-1/2 material-symbols-outlined text-gray-400 group-focus-within:text-indigo-500 transition-colors">search</span>
                                        <input id="pac-input" type="text" placeholder="Search for a location to sync with Google Maps..." class="w-full bg-gray-50 dark:bg-white/5 border-0 ring-1 ring-gray-100 dark:ring-white/10 text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-indigo-500 pl-14 pr-6 py-5 transition-all" value="{{ $locationSearch }}">
                                    </div>
                                </div>
                            @endif

                            <div id="map" class="w-full h-[450px] rounded-[2.5rem] bg-gray-100 dark:bg-[#0f172a] border border-gray-100 dark:border-white/5 overflow-hidden shadow-inner" wire:ignore>
                                <div class="flex items-center justify-center h-full text-gray-400">
                                    <div class="text-center">
                                        <div class="relative inline-block mb-4">
                                            <span class="material-symbols-outlined text-5xl text-indigo-500 animate-bounce">location_on</span>
                                            <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-4 h-1 bg-indigo-500/20 rounded-full blur-sm"></div>
                                        </div>
                                        <p class="text-xs font-black uppercase tracking-widest">Initialising Map...</p>
                                    </div>
                                </div>
                            </div>
                            
                            @if(session()->has('location_message'))
                                <div class="mt-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl no-print">
                                    <p class="text-[10px] font-bold text-center text-emerald-600 uppercase tracking-widest animate-pulse">{{ session('location_message') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Tasks Tab -->
            @if($activeTab === 'tasks')
                <div class="max-w-4xl mx-auto animate-in fade-in slide-in-from-bottom-4 duration-500">
                    <div class="bg-white dark:bg-[#1e293b]/50 dark:backdrop-blur-xl overflow-hidden shadow-sm sm:rounded-[2.5rem] border border-gray-100 dark:border-white/5">
                        <div class="p-8 md:p-12">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-10">
                                <div>
                                    <h3 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">Tasks Checklist</h3>
                                    <p class="text-sm text-gray-500 mt-1">Manage your event to-dos and track progress.</p>
                                </div>
                                <div class="flex items-center gap-2 no-print">
                                    @if($this->hasPermission('manage_tasks'))
                                        <!-- Export Dropdown -->
                                        <div x-data="{ open: false }" class="relative">
                                            <button @click="open = !open" class="flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 rounded-xl transition-all text-[10px] font-black uppercase tracking-widest text-gray-600 dark:text-gray-400">
                                                <span class="material-symbols-outlined text-sm">download</span>
                                                Export
                                                <span class="material-symbols-outlined text-sm transition-transform" :class="open ? 'rotate-180' : ''">expand_more</span>
                                            </button>
                                            
                                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-gray-100 dark:border-white/5 z-50 overflow-hidden" x-transition>
                                                <button wire:click="exportTasksToExcel" class="w-full text-left px-5 py-3 text-[10px] font-black uppercase tracking-widest text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/5 flex items-center gap-3">
                                                    <span class="material-symbols-outlined text-sm text-emerald-500">table_chart</span>
                                                    Excel (.xlsx)
                                                </button>
                                                <button wire:click="exportTasksToCSV" class="w-full text-left px-5 py-3 text-[10px] font-black uppercase tracking-widest text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/5 flex items-center gap-3">
                                                    <span class="material-symbols-outlined text-sm text-indigo-500">csv</span>
                                                    CSV (.csv)
                                                </button>
                                                <button wire:click="exportTasksToPDF" class="w-full text-left px-5 py-3 text-[10px] font-black uppercase tracking-widest text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/5 flex items-center gap-3">
                                                    <span class="material-symbols-outlined text-sm text-rose-500">picture_as_pdf</span>
                                                    PDF (.pdf)
                                                </button>
                                            </div>
                                        </div>

                                        <button onclick="window.print()" class="flex items-center gap-2 px-4 py-2 bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 rounded-xl transition-all text-[10px] font-black uppercase tracking-widest">
                                            <span class="material-symbols-outlined text-sm">print</span>
                                            Print
                                        </button>

                                        <button type="button" wire:click="suggestAITasks" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-xl shadow-indigo-500/20 transition-all hover:scale-105 active:scale-95 flex items-center">
                                            <span class="material-symbols-outlined text-sm mr-2">magic_button</span>
                                            AI Suggest
                                        </button>
                                    @endif
                                </div>
                            </div>

                            @if($this->hasPermission('manage_tasks'))
                            <!-- Add Task Form -->
                            <form wire:submit.prevent="addTask" class="mb-12 bg-gray-50 dark:bg-white/5 p-6 rounded-[2rem] border border-gray-100 dark:border-white/5 no-print">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Task Description</label>
                                        <input type="text" wire:model="newTaskTitle" placeholder="What needs to be done?" class="w-full bg-white dark:bg-gray-800 border-0 ring-1 ring-gray-200 dark:ring-white/10 text-gray-900 text-sm rounded-2xl focus:ring-2 focus:ring-indigo-500 p-4 dark:text-white">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Due Date</label>
                                        <input type="date" wire:model="newTaskDueDate" class="w-full bg-white dark:bg-gray-800 border-0 ring-1 ring-gray-200 dark:ring-white/10 text-gray-900 text-sm rounded-2xl focus:ring-2 focus:ring-indigo-500 p-4 dark:text-white">
                                    </div>
                                </div>
                                <div class="flex flex-col md:flex-row gap-4 items-end">
                                    <div class="flex-1 w-full space-y-1">
                                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Assign To</label>
                                        <select wire:model="newTaskAssignedTo" class="w-full bg-white dark:bg-gray-800 border-0 ring-1 ring-gray-200 dark:ring-white/10 text-gray-900 text-sm rounded-2xl focus:ring-2 focus:ring-indigo-500 p-4 dark:text-white appearance-none">
                                            <option value="">Unassigned</option>
                                            @foreach($eligibleAssignees as $assignee)
                                                <option value="{{ $assignee->id }}">{{ $assignee->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="w-full md:w-auto px-10 py-4 bg-[#257bf4] hover:bg-[#257bf4]/90 text-white text-xs font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-blue-500/20 transition-all active:scale-95">
                                        Add Task
                                    </button>
                                </div>
                            </form>
                            @endif

                             <!-- AI Suggestions Section -->
                             @if (!empty($aiSuggestions))
                                <div class="mb-12 p-8 bg-indigo-600 text-white rounded-[2.5rem] shadow-2xl shadow-indigo-500/40 relative overflow-hidden no-print">
                                    <div class="absolute top-0 right-0 p-8 opacity-10">
                                        <span class="material-symbols-outlined text-8xl">auto_awesome</span>
                                    </div>
                                    <div class="relative">
                                        <div class="flex items-center justify-between mb-6">
                                            <div>
                                                <h4 class="text-xl font-black uppercase tracking-tighter">AI magic is ready</h4>
                                                <p class="text-white/70 text-xs font-bold uppercase tracking-widest mt-1">Review and add suggested tasks</p>
                                            </div>
                                            <button type="button" wire:click="suggestAITasks" class="p-2 bg-white/10 hover:bg-white/20 rounded-xl transition-colors">
                                                <span class="material-symbols-outlined">refresh</span>
                                            </button>
                                        </div>
                                        <div class="space-y-3 mb-8">
                                            @foreach($aiSuggestions as $index => $suggestion)
                                                <div class="flex items-center gap-4 bg-white/10 p-4 rounded-2xl backdrop-blur-md border border-white/10 group transition-all hover:bg-white/15">
                                                    <input type="checkbox" wire:model.live="aiSuggestions.{{ $index }}.selected" class="w-5 h-5 bg-transparent border-2 border-white/30 rounded text-white focus:ring-0 checked:bg-white checked:border-white">
                                                    <input type="text" wire:model="aiSuggestions.{{ $index }}.title" class="flex-1 bg-transparent border-0 text-white p-0 text-sm font-bold focus:ring-0" {{ !$suggestion['selected'] ? 'disabled' : '' }}>
                                                    <button wire:click="removeSuggestion({{ $index }})" class="text-white/40 hover:text-white transition-colors">
                                                        <span class="material-symbols-outlined text-lg">close</span>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="flex justify-end">
                                            <button wire:click="saveSuggestions" class="px-8 py-3 bg-white text-indigo-600 text-xs font-black uppercase tracking-widest rounded-xl shadow-xl transition-all hover:scale-105 active:scale-95">
                                                Save Selected Tasks
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Tasks List -->
                            <div class="space-y-4 print-container">
                                @forelse ($tasks as $task)
                                    <div class="group p-6 bg-gray-50 dark:bg-white/5 rounded-[2rem] border border-transparent hover:border-gray-200 dark:hover:border-white/10 transition-all">
                                        @if($editingTaskId === $task->id)
                                            <!-- Inline Edit (Keeping existing logic but updating UI) -->
                                            <div class="space-y-4">
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                    <input type="text" wire:model="editTaskTitle" class="w-full bg-white dark:bg-gray-800 border-0 ring-1 ring-gray-200 dark:ring-white/10 rounded-xl p-3 text-sm" placeholder="Task title">
                                                    <input type="date" wire:model="editTaskDueDate" class="w-full bg-white dark:bg-gray-800 border-0 ring-1 ring-gray-200 dark:ring-white/10 rounded-xl p-3 text-sm">
                                                </div>
                                                <textarea wire:model="editTaskDescription" rows="2" class="w-full bg-white dark:bg-gray-800 border-0 ring-1 ring-gray-200 dark:ring-white/10 rounded-xl p-3 text-sm" placeholder="Add description..."></textarea>
                                                <div class="flex items-center justify-between">
                                                    <select wire:model="editTaskAssignedTo" class="bg-white dark:bg-gray-800 border-0 ring-1 ring-gray-200 dark:ring-white/10 rounded-xl p-2 text-xs">
                                                        <option value="">Unassigned</option>
                                                        @foreach($eligibleAssignees as $assignee)
                                                            <option value="{{ $assignee->id }}">{{ $assignee->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="flex gap-2">
                                                        <button wire:click="cancelEditTask" class="text-xs font-bold uppercase tracking-widest text-gray-400">Cancel</button>
                                                        <button wire:click="saveTask" class="px-6 py-2 bg-indigo-600 text-white text-xs font-black uppercase tracking-widest rounded-xl">Save</button>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="flex items-start justify-between gap-4">
                                                <div class="flex items-start gap-4">
                                                    <div class="pt-1">
                                                        <button wire:click="toggleTask({{ $task->id }})" class="w-6 h-6 rounded-lg border-2 flex items-center justify-center transition-all {{ $task->completed ? 'bg-emerald-500 border-emerald-500 text-white' : 'border-gray-300 dark:border-white/20' }}">
                                                            @if($task->completed) <span class="material-symbols-outlined text-[16px]">check</span> @endif
                                                        </button>
                                                    </div>
                                                    <div>
                                                        <p class="text-base font-bold transition-all {{ $task->completed ? 'line-through text-gray-400' : 'text-gray-900 dark:text-white' }}">
                                                            {{ $task->title }}
                                                        </p>
                                                        @if ($task->description)
                                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $task->description }}</p>
                                                        @endif
                                                        
                                                        <div class="flex flex-wrap items-center gap-4 mt-3">
                                                            @if ($task->due_date)
                                                                <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                                                                    <span class="material-symbols-outlined text-sm">event</span>
                                                                    <span class="text-[10px] font-black uppercase tracking-wider">{{ $task->due_date->format('M d') }}</span>
                                                                </div>
                                                            @endif

                                                            @if($task->assignee)
                                                                <div class="flex items-center gap-2">
                                                                    <div class="h-6 w-6 rounded-full bg-gray-200 dark:bg-white/10 flex items-center justify-center text-[10px] font-black uppercase text-gray-500">
                                                                        {{ substr($task->assignee->name, 0, 1) }}
                                                                    </div>
                                                                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ $task->assignee->name }}</span>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <!-- Interaction for Assigned User -->
                                                        @if($task->assigned_to === auth()->id() && !$task->completed)
                                                            <div class="mt-4 flex gap-2">
                                                                @if($task->assignment_status === 'pending')
                                                                    <button wire:click="acceptTask({{ $task->id }})" class="bg-indigo-600 text-white text-[10px] font-black px-4 py-2 rounded-xl uppercase tracking-widest">Accept</button>
                                                                    <button wire:click="declineTask({{ $task->id }})" class="bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-300 text-[10px] font-black px-4 py-2 rounded-xl uppercase tracking-widest">Decline</button>
                                                                @elseif($task->assignment_status === 'accepted')
                                                                    @if($completingTaskId === $task->id)
                                                                        <div class="w-full space-y-3 mt-2 bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-white/5">
                                                                            <textarea wire:model="completionComment" placeholder="Add a comment..." class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-xl p-3 text-xs"></textarea>
                                                                            <div class="flex gap-2">
                                                                                <button wire:click="completeTask" class="bg-emerald-500 text-white text-[10px] font-black px-4 py-2 rounded-xl uppercase tracking-widest">Submit</button>
                                                                                <button wire:click="$set('completingTaskId', null)" class="text-xs font-bold text-gray-400">Cancel</button>
                                                                            </div>
                                                                        </div>
                                                                    @else
                                                                        <button wire:click="startCompletion({{ $task->id }})" class="text-emerald-500 text-[10px] font-black uppercase tracking-widest flex items-center gap-1.5 transition-all">
                                                                            <span class="material-symbols-outlined text-[16px]">task_alt</span>
                                                                            Mark Complete
                                                                        </button>
                                                                    @endif
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity no-print">
                                                    @if($this->hasPermission('manage_tasks'))
                                                        <button wire:click="startEditTask({{ $task->id }})" class="p-2 text-gray-400 hover:text-indigo-500 transition-colors">
                                                            <span class="material-symbols-outlined text-lg">edit</span>
                                                        </button>
                                                        <button wire:click="deleteTask({{ $task->id }})" class="p-2 text-gray-400 hover:text-rose-500 transition-colors">
                                                            <span class="material-symbols-outlined text-lg">delete</span>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <div class="text-center py-20 bg-gray-50 dark:bg-white/5 rounded-[2.5rem] border border-dashed border-gray-200 dark:border-white/10">
                                        <span class="material-symbols-outlined text-5xl text-gray-300 dark:text-white/10 mb-4">playlist_add_check</span>
                                        <p class="text-gray-500 font-medium">No tasks yet. Start planning!</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Guests Tab -->
            @if($activeTab === 'guests')
                <div class="max-w-4xl mx-auto animate-in fade-in slide-in-from-bottom-4 duration-500">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="lg:col-span-2 space-y-8">
                            <!-- Guest List Card -->
                            <div class="bg-white dark:bg-gray-800/50 dark:backdrop-blur-xl overflow-hidden shadow-sm sm:rounded-[2.5rem] border border-gray-100 dark:border-white/5 p-8 md:p-10">
                                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-12 no-print">
                                    <div class="space-y-1">
                                        <h3 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Guest List</h3>
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Manage your RSVPs and invitations</p>
                                    </div>
                                    
                                    <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                                        @if($this->hasPermission('manage_invites'))
                                            @php $draftCount = $event->invites()->where('status', 'draft')->count(); @endphp
                                            
                                            <div class="flex items-center gap-3 w-full sm:w-auto">
                                                <button wire:click="startImport" class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl shadow-2xl shadow-indigo-600/20 transition-all hover:scale-[1.02] active:scale-95 group">
                                                    <span class="material-symbols-outlined text-sm group-hover:rotate-12 transition-transform">upload_file</span>
                                                    Import Guests
                                                </button>

                                                @if($draftCount > 0)
                                                    <button wire:click="sendAllDrafts" class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-8 py-4 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl shadow-2xl shadow-emerald-500/20 transition-all hover:scale-[1.02] active:scale-95 group">
                                                        <span class="material-symbols-outlined text-sm group-hover:translate-x-1 transition-transform">send</span>
                                                        Send All ({{ $draftCount }})
                                                    </button>
                                                @endif
                                            </div>

                                            <div class="h-8 w-px bg-gray-100 dark:bg-white/10 mx-2 hidden sm:block"></div>

                                            <div class="flex items-center gap-2">
                                                <!-- Export Dropdown -->
                                                <div x-data="{ open: false }" class="relative">
                                                    <button @click="open = !open" class="flex items-center gap-2 px-5 py-3 bg-gray-50 dark:bg-white/5 hover:bg-gray-100 dark:hover:bg-white/10 rounded-2xl transition-all text-[10px] font-black uppercase tracking-widest text-gray-600 dark:text-gray-400">
                                                        <span class="material-symbols-outlined text-sm">download</span>
                                                        Export
                                                    </button>
                                                    
                                                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-3 w-56 bg-white dark:bg-[#0f172a] rounded-[2rem] shadow-[0_20px_50px_rgba(0,0,0,0.1)] border border-gray-100 dark:border-white/5 z-50 overflow-hidden" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                                                        <button wire:click="exportToExcel" class="w-full text-left px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/5 flex items-center gap-3 transition-colors">
                                                            <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600">
                                                                <span class="material-symbols-outlined text-sm">table_chart</span>
                                                            </div>
                                                            Excel (.xlsx)
                                                        </button>
                                                        <button wire:click="exportToCSV" class="w-full text-left px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/5 flex items-center gap-3 transition-colors">
                                                            <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600">
                                                                <span class="material-symbols-outlined text-sm">csv</span>
                                                            </div>
                                                            CSV (.csv)
                                                        </button>
                                                        <button wire:click="exportToPDF" class="w-full text-left px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/5 flex items-center gap-3 transition-colors">
                                                            <div class="w-8 h-8 rounded-xl bg-rose-50 dark:bg-rose-500/10 flex items-center justify-center text-rose-600">
                                                                <span class="material-symbols-outlined text-sm">picture_as_pdf</span>
                                                            </div>
                                                            PDF (.pdf)
                                                        </button>
                                                    </div>
                                                </div>

                                                <button onclick="window.print()" class="flex items-center justify-center w-12 h-12 bg-gray-50 dark:bg-white/5 hover:bg-gray-100 dark:hover:bg-white/10 text-gray-500 dark:text-gray-400 rounded-2xl transition-all">
                                                    <span class="material-symbols-outlined text-xl">print</span>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="print-only mb-8">
                                    <h1 class="text-3xl font-black uppercase tracking-tighter">{{ $event->title }}</h1>
                                    <p class="text-gray-500">Approved Guest List • Generated {{ now()->format('M d, Y') }}</p>
                                </div>
                                <div class="space-y-12">
                                    <div class="space-y-6 print-container">
                                        <h4 class="text-[10px] font-black uppercase tracking-widest text-gray-400 flex items-center gap-2 ml-4">
                                            <span class="material-symbols-outlined text-sm">how_to_reg</span>
                                            RSVP Responses
                                        </h4>
                                        @forelse ($rsvps as $rsvp)
                                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-white/5 rounded-2xl border border-transparent hover:border-gray-100 dark:hover:border-white/10 transition-all">
                                                <div class="flex items-center gap-4">
                                                    <div class="h-12 w-12 rounded-2xl bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center text-indigo-700 dark:text-indigo-400 font-black text-lg">
                                                        {{ substr($rsvp->user->name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <p class="text-base font-bold text-gray-900 dark:text-white leading-none mb-1">{{ $rsvp->user->name }}</p>
                                                        <p class="text-xs text-gray-500 font-medium">{{ $rsvp->user->email }}</p>
                                                    </div>
                                                </div>
                                                <span class="text-[10px] px-2.5 py-1 rounded-lg font-black uppercase tracking-widest border
                                                    {{ $rsvp->status === 'attending' ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : '' }}
                                                    {{ $rsvp->status === 'maybe' ? 'bg-amber-100 text-amber-700 border-amber-200' : '' }}
                                                    {{ $rsvp->status === 'declined' ? 'bg-rose-100 text-rose-700 border-rose-200' : '' }}
                                                ">
                                                    {{ $rsvp->status }}
                                                </span>
                                            </div>
                                        @empty
                                            <div class="text-center py-12 bg-gray-50 dark:bg-white/5 rounded-[2rem] border border-dashed border-gray-200 dark:border-white/10">
                                                <span class="material-symbols-outlined text-4xl text-gray-300 dark:text-white/10 mb-3">person_search</span>
                                                <p class="text-xs font-black text-gray-500 uppercase tracking-widest">No RSVPs yet.</p>
                                            </div>
                                        @endforelse
                                    </div>

                                    @if($this->hasPermission('manage_invites'))
                                        <div class="pt-10 border-t border-gray-100 dark:border-white/5 space-y-6">
                                            <h4 class="text-[10px] font-black uppercase tracking-widest text-gray-400 flex items-center gap-2 ml-4">
                                                <span class="material-symbols-outlined text-sm">mail</span>
                                                Invitations & Drafts
                                            </h4>
                                            <livewire:events.invited-list :event="$event" :selectedInviteIds="$selectedInviteIds" :canEditEvent="$this->hasPermission('edit_event')" />

                                            @if(count($selectedInviteIds) > 0 && $this->hasPermission('edit_event'))
                                                <div class="fixed bottom-8 right-8 z-50">
                                                    <button wire:click="openBulkNotificationModal" class="flex items-center gap-3 px-6 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full shadow-2xl transition-all transform hover:scale-105 active:scale-95 group border border-indigo-500/30 backdrop-blur-sm">
                                                        <span class="material-symbols-outlined group-hover:rotate-12 transition-transform">send</span>
                                                        <span class="font-bold tracking-wide">Notify {{ count($selectedInviteIds) }} Selected</span>
                                                    </button>
                                                </div>
                                            @endif

                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div>
                            <!-- Invite Form (Moved to Tab) -->
                            @if($this->hasPermission('manage_invites'))
                                <div class="bg-indigo-600 rounded-[2.5rem] shadow-2xl shadow-indigo-500/30 p-8 text-white sticky top-8 no-print">
                                    <h4 class="text-xl font-black uppercase tracking-tighter mb-6">Bring your team</h4>
                                    <livewire:events.invite-form :event="$event" />
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Media Tab -->
            @if($activeTab === 'media')
                <div class="animate-in fade-in slide-in-from-bottom-4 duration-500">
                    <livewire:events.media-library :event="$event" :user-permissions="$userPermissions" :user-role="$userRole" />
                </div>
            @endif

            <!-- Team Tab (Owner/Organizers) -->
            @if($activeTab === 'team' && ($this->hasPermission('manage_invites') || $this->hasPermission('owner')))
                <div class="max-w-4xl mx-auto animate-in fade-in slide-in-from-bottom-4 duration-500 space-y-12">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        @if($this->hasPermission('manage_invites'))
                            <div class="bg-white dark:bg-gray-800/50 dark:backdrop-blur-xl rounded-[2.5rem] p-8 border border-gray-100 dark:border-white/5 shadow-sm">
                                <div class="flex items-center justify-between mb-8">
                                    <h4 class="text-xl font-black uppercase tracking-tighter text-gray-900 dark:text-white">Invited Members</h4>
                                </div>
                                <livewire:events.invited-list :event="$event" :selectedInviteIds="$selectedInviteIds" :canEditEvent="$this->hasPermission('edit_event')" />

                            </div>
                        @endif

                        @if($this->hasPermission('owner'))
                            <div class="bg-white dark:bg-gray-800/50 dark:backdrop-blur-xl rounded-[2.5rem] p-8 border border-gray-100 dark:border-white/5 shadow-sm">
                                <h4 class="text-xl font-black uppercase tracking-tighter mb-8 text-gray-900 dark:text-white">Organizers</h4>
                                <livewire:events.manage-organizers :event="$event" />
                            </div>
                        @endif
                    </div>
                </div>
            @endif

        </div>

        <!-- Cancellation Confirmation Modal -->
        @if($isConfirmingCancellation)
            <div class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80 backdrop-blur-sm transition-opacity" aria-hidden="true"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div class="inline-block align-bottom bg-white dark:bg-[#1e293b] rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100 dark:border-white/5">
                        <div class="p-8">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-12 h-12 rounded-2xl bg-red-50 dark:bg-red-900/20 flex items-center justify-center text-red-600">
                                    <span class="material-symbols-outlined">warning</span>
                                </div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tighter" id="modal-title">
                                    Cancel Event?
                                </h3>
                            </div>

                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-8 leading-relaxed">
                                Are you sure you want to cancel **{{ $event->title }}**? This action cannot be undone and will notify all invited guests and participants.
                            </p>

                            <div class="space-y-2 mb-8">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Reason for Cancellation (Optional)</label>
                                <textarea wire:model="cancellationReason" rows="3" class="w-full bg-gray-50 dark:bg-white/5 border-0 ring-1 ring-gray-200 dark:ring-white/10 text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-red-500 p-4 transition-all" placeholder="Tell your guests why the event is being cancelled..."></textarea>
                                @error('cancellationReason') <span class="text-[10px] text-red-500 font-bold uppercase ml-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex flex-col sm:flex-row-reverse gap-3 mt-10">
                                <button wire:click="cancelEvent" class="w-full sm:w-auto px-8 py-4 bg-red-600 hover:bg-red-700 text-white text-xs font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-red-500/20 transition-all active:scale-95">
                                    Yes, Cancel Event
                                </button>
                                <button wire:click="$set('isConfirmingCancellation', false)" class="w-full sm:w-auto px-8 py-4 bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-400 text-xs font-black uppercase tracking-widest rounded-2xl transition-all">
                                    Keep Event
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Guest Import Modal -->
        @if($isImporting)
            <div class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80 backdrop-blur-sm transition-opacity" aria-hidden="true" wire:click="$set('isImporting', false)"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div class="relative inline-block align-middle bg-white dark:bg-[#0f172a] rounded-[3rem] text-left shadow-[0_40px_80px_-20px_rgba(0,0,0,0.2)] transform transition-all sm:my-8 sm:max-w-4xl sm:w-full border border-white/20 dark:border-white/10 mx-4 overflow-hidden">
                        <div class="p-10 border-b border-gray-100 dark:border-white/5">
                            <div class="flex justify-between items-start">
                                <div class="space-y-1">
                                    <h3 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight uppercase">Import Guests</h3>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Upload your guest list in bulk</p>
                                </div>
                                <button wire:click="$set('isImporting', false)" class="w-12 h-12 rounded-2xl bg-gray-50 dark:bg-white/5 flex items-center justify-center text-gray-400 hover:text-gray-900 dark:hover:text-white transition-all hover:rotate-90">
                                    <span class="material-symbols-outlined text-xl">close</span>
                                </button>
                            </div>
                        </div>

                        <div class="p-10 overflow-y-auto max-h-[70vh]">
                            @if (session()->has('error'))
                                <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-2xl flex items-center gap-3 text-red-500 text-xs font-bold uppercase tracking-widest">
                                    <span class="material-symbols-outlined text-sm">error</span>
                                    {{ session('error') }}
                                </div>
                            @endif
                            @if (session()->has('message'))
                                <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl flex items-center gap-3 text-emerald-500 text-xs font-bold uppercase tracking-widest">
                                    <span class="material-symbols-outlined text-sm">check_circle</span>
                                    {{ session('message') }}
                                </div>
                            @endif

                            @if(empty($importedGuests))
                                <div class="space-y-10">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                        <div class="p-8 bg-indigo-50 dark:bg-indigo-500/5 rounded-[2.5rem] border border-indigo-100 dark:border-indigo-500/10 space-y-4">
                                            <div class="w-12 h-12 rounded-2xl bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center text-indigo-600">
                                                <span class="material-symbols-outlined text-2xl">download</span>
                                            </div>
                                            <h4 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">Step 1: Get Template</h4>
                                            <p class="text-sm text-gray-500 leading-relaxed uppercase font-bold text-[10px] tracking-wide">Download our formatted Excel template to ensure your guest data is imported correctly.</p>
                                            <button wire:click="downloadTemplate" class="inline-flex items-center gap-2 text-[10px] font-black text-indigo-600 hover:text-indigo-700 uppercase tracking-widest bg-white dark:bg-white/5 px-4 py-2 rounded-xl shadow-sm transition-all hover:scale-105">
                                                Download Template
                                                <span class="material-symbols-outlined text-xs">arrow_forward</span>
                                            </button>
                                        </div>

                                        <div class="p-8 bg-emerald-50 dark:bg-emerald-500/5 rounded-[2.5rem] border border-emerald-100 dark:border-emerald-500/10 space-y-4">
                                            <div class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center text-emerald-600">
                                                <span class="material-symbols-outlined text-2xl">upload</span>
                                            </div>
                                            <h4 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">Step 2: Upload File</h4>
                                            <p class="text-sm text-gray-500 leading-relaxed uppercase font-bold text-[10px] tracking-wide">Once your template is ready, upload the file (Excel or CSV) to review your guests.</p>
                                        </div>
                                    </div>

                                    <div class="relative group">
                                        <div class="absolute inset-0 bg-indigo-600/5 dark:bg-indigo-500/10 rounded-[3rem] border-2 border-dashed border-indigo-200 dark:border-indigo-500/20 group-hover:bg-indigo-600/10 transition-all pointer-events-none"></div>
                                        <input type="file" wire:model="guestImportFile" class="relative z-10 w-full opacity-0 h-64 cursor-pointer">
                                        <div class="absolute inset-0 flex flex-col items-center justify-center gap-4 pointer-events-none">
                                            <div class="w-20 h-20 rounded-3xl bg-white dark:bg-white/5 shadow-2xl flex items-center justify-center text-indigo-600 group-hover:scale-110 transition-transform">
                                                <span class="material-symbols-outlined text-4xl">cloud_upload</span>
                                            </div>
                                            <div class="text-center">
                                                <p class="text-base font-black text-gray-900 dark:text-white tracking-tight">Drop your file here or click to browse</p>
                                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">Supports XLSX, XLS, and CSV</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div wire:loading wire:target="guestImportFile" class="w-full">
                                        <div class="flex items-center justify-center gap-4 p-8 bg-gray-50 dark:bg-white/5 rounded-[2.5rem]">
                                            <div class="w-6 h-6 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                                            <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Processing your guest list...</p>
                                        </div>
                                    </div>

                                    @if($guestImportFile)
                                        <div class="flex items-center justify-between p-6 bg-white dark:bg-white/5 rounded-[2rem] border border-emerald-100 dark:border-emerald-500/20 animate-in fade-in zoom-in-95">
                                            <div class="flex items-center gap-4">
                                                <div class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center text-emerald-600">
                                                    <span class="material-symbols-outlined text-2xl">check_circle</span>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight">File Ready</p>
                                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $guestImportFile->getClientOriginalName() }}</p>
                                                </div>
                                            </div>
                                            <button wire:click="uploadGuests" class="px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-indigo-600/20 transition-all">
                                                Process Guests
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="space-y-8">
                                    <div class="flex flex-col sm:flex-row justify-between items-center gap-6 p-6 bg-indigo-50 dark:bg-indigo-500/10 rounded-[2.5rem] border border-indigo-100 dark:border-indigo-500/10">
                                        <div class="space-y-1">
                                            <h4 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">Review Imported Guests</h4>
                                            <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest">Total: {{ count($importedGuests) }} guests found</p>
                                        </div>
                                        <div class="relative w-full sm:w-64">
                                            <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-sm text-gray-400">search</span>
                                            <input type="text" wire:model.live="draftSearch" placeholder="Search imported list..." class="w-full pl-10 pr-4 py-3 bg-white dark:bg-white/5 border-0 ring-1 ring-gray-100 dark:ring-white/10 rounded-xl text-xs font-bold transition-all focus:ring-2 focus:ring-indigo-500">
                                        </div>
                                    </div>

                                    <div class="bg-white dark:bg-white/5 rounded-[2.5rem] border border-gray-100 dark:border-white/10 overflow-hidden">
                                        <table class="w-full text-left border-collapse">
                                            <thead class="bg-gray-50/50 dark:bg-white/5 border-b border-gray-100 dark:border-white/10">
                                                <tr>
                                                    <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Name</th>
                                                    <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Email</th>
                                                    <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Phone</th>
                                                    <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest"></th>
                                                </tr>
                                            </thead>
                                            <tbody class="space-y-2">
                                                @php
                                                    $filteredGuests = collect($importedGuests)->filter(function($guest) {
                                                        if (empty($this->draftSearch)) return true;
                                                        $search = strtolower($this->draftSearch);
                                                        return str_contains(strtolower($guest['name']), $search) || 
                                                               str_contains(strtolower($guest['email']), $search);
                                                    });
                                                @endphp
                                                @foreach($filteredGuests as $index => $guest)
                                                <tr class="bg-gray-50 dark:bg-white/5 rounded-2xl border-4 border-white dark:border-[#1e293b] group/row">
                                                    <td class="p-4 pl-6 rounded-l-2xl">
                                                        <div class="flex items-center gap-2">
                                                            <span class="material-symbols-outlined text-xs text-gray-300 dark:text-slate-600 group-hover/row:text-indigo-500 transition-colors">edit</span>
                                                            <input type="text" 
                                                                   wire:change="updateImportedGuest({{ $index }}, 'name', $event.target.value)"
                                                                   value="{{ $guest['name'] }}"
                                                                   class="w-full bg-transparent border-0 p-0 text-sm font-bold focus:ring-0 {{ isset($guest['errors']['name']) ? 'text-red-500' : 'text-gray-900 dark:text-white' }}">
                                                        </div>
                                                        @if(isset($guest['errors']['name']))
                                                            <div class="text-[9px] font-black text-red-500 uppercase tracking-widest mt-1 ml-6">{{ $guest['errors']['name'] }}</div>
                                                        @endif
                                                    </td>
                                                    <td class="p-4">
                                                        <div class="flex items-center gap-2">
                                                            <span class="material-symbols-outlined text-xs text-gray-300 dark:text-slate-600 group-hover/row:text-indigo-500 transition-colors">alternate_email</span>
                                                            <input type="email" 
                                                                   wire:change="updateImportedGuest({{ $index }}, 'email', $event.target.value)"
                                                                   value="{{ $guest['email'] }}"
                                                                   class="w-full bg-transparent border-0 p-0 text-sm font-bold focus:ring-0 {{ isset($guest['errors']['email']) ? 'text-red-500' : 'text-gray-900 dark:text-white' }}">
                                                        </div>
                                                        @if(isset($guest['errors']['email']))
                                                            <div class="text-[9px] font-black text-red-500 uppercase tracking-widest mt-1 ml-6">{{ $guest['errors']['email'] }}</div>
                                                        @endif
                                                    </td>
                                                    <td class="p-4">
                                                        <div class="flex items-center gap-2">
                                                            <span class="material-symbols-outlined text-xs text-gray-300 dark:text-slate-600 group-hover/row:text-indigo-500 transition-colors">phone_iphone</span>
                                                            <input type="text" 
                                                                   wire:change="updateImportedGuest({{ $index }}, 'phone', $event.target.value)"
                                                                   value="{{ $guest['phone'] }}"
                                                                   class="w-full bg-transparent border-0 p-0 text-sm font-bold focus:ring-0 text-gray-600 dark:text-gray-400">
                                                        </div>
                                                    </td>
                                                    <td class="p-4 pr-6 rounded-r-2xl text-right">
                                                        <button wire:click="removeImportedGuest({{ $index }})" class="text-gray-300 hover:text-red-500 transition-colors">
                                                            <span class="material-symbols-outlined text-lg">delete</span>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-8 pt-8 border-t border-gray-100 dark:border-white/5">
                                    <button wire:click="$set('importedGuests', [])" class="text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest hover:text-gray-700">
                                        Clear List & Start Over
                                    </button>
                                    <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                        @php
                                            $hasErrors = collect($importedGuests)->pluck('errors')->flatten()->isNotEmpty();
                                        @endphp
                                        <button wire:click="finalizeImport(true)" 
                                                {{ $hasErrors ? 'disabled' : '' }}
                                                class="px-8 py-4 bg-gray-100 dark:bg-white/10 text-gray-900 dark:text-white text-xs font-black uppercase tracking-widest rounded-2xl transition-all hover:bg-gray-200 dark:hover:bg-white/20 disabled:opacity-50 disabled:cursor-not-allowed">
                                            Save as Draft
                                        </button>
                                        <button wire:click="finalizeImport(false)" 
                                                {{ $hasErrors ? 'disabled' : '' }}
                                                class="px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-indigo-500/20 transition-all hover:scale-105 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                                            Send Invites Now
                                        </button>
                                    </div>
                                </div>
                                <div class="mt-4 text-center">
                                    <button wire:click="$set('isImporting', false)" class="text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-gray-600 transition-colors">Close Modal</button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Draft Edit Modal -->
        @if($isEditingDraft)
            <div class="fixed inset-0 z-[200] overflow-y-auto" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
                    <!-- Backdrop with heavy blur -->
                    <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-2xl transition-opacity duration-500" aria-hidden="true" wire:click="$set('isEditingDraft', false)"></div>

                    <!-- Modal Panel -->
                    <div class="relative inline-block align-middle bg-white dark:bg-[#0f172a] rounded-[3rem] text-left overflow-hidden shadow-[0_40px_80px_-20px_rgba(0,0,0,0.2)] transform transition-all sm:my-8 sm:max-w-xl sm:w-full border border-white/20 dark:border-white/10 mx-4">
                        <div class="p-10">
                            <!-- Header -->
                            <div class="flex justify-between items-start mb-10">
                                <div class="space-y-1">
                                    <h3 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight uppercase">Edit Guest</h3>
                                    <p class="text-[10px] font-black text-amber-500 uppercase tracking-widest flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        Draft Invitation
                                    </p>
                                </div>
                                <button wire:click="$set('isEditingDraft', false)" class="w-12 h-12 rounded-2xl bg-gray-50 dark:bg-white/5 flex items-center justify-center text-gray-400 hover:text-gray-900 dark:hover:text-white transition-all hover:rotate-90">
                                    <span class="material-symbols-outlined text-xl">close</span>
                                </button>
                            </div>

                            <!-- Form -->
                            <div class="space-y-8">
                                <div class="group">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1 group-focus-within:text-indigo-500 transition-colors">Guest Name</label>
                                    <div class="relative">
                                        <input type="text" wire:model="editDraftName" 
                                               class="w-full bg-gray-50 dark:bg-white/5 border-0 ring-1 ring-gray-100 dark:ring-white/10 text-gray-900 dark:text-white text-base font-bold rounded-2xl focus:ring-2 focus:ring-indigo-500 p-5 transition-all outline-none" 
                                               placeholder="Full name">
                                    </div>
                                    @error('editDraftName') <p class="text-[9px] text-red-500 font-bold uppercase mt-2 ml-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="group">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1 group-focus-within:text-indigo-500 transition-colors">Email Address</label>
                                    <div class="relative">
                                        <input type="email" wire:model="editDraftEmail" 
                                               class="w-full bg-gray-50 dark:bg-white/5 border-0 ring-1 ring-gray-100 dark:ring-white/10 text-gray-900 dark:text-white text-base font-bold rounded-2xl focus:ring-2 focus:ring-indigo-500 p-5 transition-all outline-none" 
                                               placeholder="email@example.com">
                                    </div>
                                    @error('editDraftEmail') <p class="text-[9px] text-red-500 font-bold uppercase mt-2 ml-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="group">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1 group-focus-within:text-indigo-500 transition-colors">Phone Number</label>
                                    <div class="relative">
                                        <input type="text" wire:model="editDraftPhone" 
                                               class="w-full bg-gray-50 dark:bg-white/5 border-0 ring-1 ring-gray-100 dark:ring-white/10 text-gray-900 dark:text-white text-base font-bold rounded-2xl focus:ring-2 focus:ring-indigo-500 p-5 transition-all outline-none" 
                                               placeholder="+1 (555) 000-0000">
                                    </div>
                                    @error('editDraftPhone') <p class="text-[9px] text-red-500 font-bold uppercase mt-2 ml-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <!-- Footer Actions -->
                            <div class="flex flex-col gap-4 mt-12">
                                <button wire:click="updateDraft" 
                                        class="w-full py-5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black uppercase tracking-widest rounded-2xl shadow-2xl shadow-indigo-600/30 transition-all hover:scale-[1.02] active:scale-95">
                                    Save Changes
                                </button>
                                <div class="flex gap-4">
                                    <button wire:click="deleteInvite({{ $editingDraftId }})" 
                                            class="flex-1 py-4 bg-rose-50 dark:bg-rose-900/10 text-rose-600 dark:text-rose-400 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-rose-100 dark:hover:bg-rose-900/20 transition-all">
                                        Delete Draft
                                    </button>
                                    <button wire:click="$set('isEditingDraft', false)" 
                                            class="flex-1 py-4 bg-gray-50 dark:bg-white/5 text-gray-500 dark:text-gray-400 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-gray-100 dark:hover:bg-white/10 transition-all">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    <!-- Bulk Notification Modal -->
    @if($showBulkNotificationModal)
        <div class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity" aria-hidden="true" wire:click="closeBulkNotificationModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-200 dark:border-gray-700">
                    <div class="bg-indigo-600 px-6 py-4 flex justify-between items-center text-white">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined">campaign</span>
                            <h3 class="text-lg font-bold">Send Bulk Notification</h3>
                        </div>
                        <button wire:click="closeBulkNotificationModal" class="text-white/80 hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-xl font-bold">close</span>
                        </button>
                    </div>

                    <div class="p-6">
                        <div class="mb-6 p-4 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl border border-indigo-100 dark:border-indigo-800/50 flex items-start gap-3">
                            <span class="material-symbols-outlined text-indigo-600 mt-0.5">group</span>
                            <div>
                                <p class="text-sm font-bold text-indigo-900 dark:text-indigo-200">Notifying {{ count($selectedInviteIds) }} guests</p>
                                <p class="text-xs text-indigo-700 dark:text-indigo-300/80 mt-1">These guests will receive an email with your custom message and a link to the event.</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label for="bulk_message" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Compose Message</label>
                                <textarea wire:model="bulkNotificationMessage" id="bulk_message" rows="6" 
                                    class="w-full bg-gray-50 dark:bg-gray-900 border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all placeholder:text-gray-400"
                                    placeholder="Write your update or reminder here..."></textarea>
                                @error('bulkNotificationMessage') <span class="text-xs text-rose-500 font-bold mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-8 flex gap-3">
                            <button wire:click="closeBulkNotificationModal" class="flex-1 px-6 py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-xl font-bold transition-all">
                                Cancel
                            </button>
                            <button wire:click="sendBulkNotification" class="flex-1 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-lg shadow-indigo-500/30 transition-all flex items-center justify-center gap-2 group">
                                <span class="material-symbols-outlined text-sm group-hover:translate-x-1 transition-transform">send</span>
                                Send Message
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    </div>
</div>

@push('scripts')
<script>
    let map, marker, autocomplete;

    function initMap() {
        const isDark = document.documentElement.classList.contains('dark');
        const initialPos = { 
            lat: {{ $latitude ?: -1.286389 }}, 
            lng: {{ $longitude ?: 36.817223 }} 
        };

        const mapStyles = isDark ? [
            { elementType: "geometry", stylers: [{ color: "#242f3e" }] },
            { elementType: "labels.text.stroke", stylers: [{ color: "#242f3e" }] },
            { elementType: "labels.text.fill", stylers: [{ color: "#746855" }] },
            { featureType: "administrative.locality", elementType: "labels.text.fill", stylers: [{ color: "#d59563" }] },
            { featureType: "poi", elementType: "labels.text.fill", stylers: [{ color: "#d59563" }] },
            { featureType: "poi.park", elementType: "geometry", stylers: [{ color: "#263c3f" }] },
            { featureType: "poi.park", elementType: "labels.text.fill", stylers: [{ color: "#6b9a76" }] },
            { featureType: "road", elementType: "geometry", stylers: [{ color: "#38414e" }] },
            { featureType: "road", elementType: "geometry.stroke", stylers: [{ color: "#212a37" }] },
            { featureType: "road", elementType: "labels.text.fill", stylers: [{ color: "#9ca5b3" }] },
            { featureType: "road.highway", elementType: "geometry", stylers: [{ color: "#746855" }] },
            { featureType: "road.highway", elementType: "geometry.stroke", stylers: [{ color: "#1f2835" }] },
            { featureType: "road.highway", elementType: "labels.text.fill", stylers: [{ color: "#f3d19c" }] },
            { featureType: "transit", elementType: "geometry", stylers: [{ color: "#2f3948" }] },
            { featureType: "transit.station", elementType: "labels.text.fill", stylers: [{ color: "#d59563" }] },
            { featureType: "water", elementType: "geometry", stylers: [{ color: "#17263c" }] },
            { featureType: "water", elementType: "labels.text.fill", stylers: [{ color: "#515c6d" }] },
            { featureType: "water", elementType: "labels.text.stroke", stylers: [{ color: "#17263c" }] },
        ] : [];

        map = new google.maps.Map(document.getElementById("map"), {
            center: initialPos,
            zoom: {{ ($latitude && $longitude) ? 15 : 12 }},
            styles: mapStyles,
            disableDefaultUI: false,
            zoomControl: true,
            mapTypeControl: false,
            scaleControl: true,
            streetViewControl: false,
            rotateControl: false,
            fullscreenControl: true
        });

        marker = new google.maps.Marker({
            position: initialPos,
            map: map,
            draggable: {{ $this->hasPermission('edit_event') ? 'true' : 'false' }},
            animation: google.maps.Animation.DROP,
            title: "{{ $event->title }}",
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 12,
                fillColor: "#6366f1",
                fillOpacity: 1,
                strokeWeight: 3,
                strokeColor: "#ffffff",
            }
        });

        @if($this->hasPermission('edit_event'))
            const input = document.getElementById("pac-input");
            autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.bindTo("bounds", map);

            autocomplete.addListener("place_changed", () => {
                const place = autocomplete.getPlace();
                if (!place.geometry || !place.geometry.location) return;

                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(17);
                }

                marker.setPosition(place.geometry.location);
                
                @this.syncLocation(
                    place.geometry.location.lat(),
                    place.geometry.location.lng(),
                    place.place_id,
                    place.formatted_address
                );
            });

            marker.addListener("dragend", () => {
                const pos = marker.getPosition();
                @this.syncLocation(pos.lat(), pos.lng(), null, 'Custom Pin Drop');
            });
        @endif
    }

    async function shareLocation(title, lat, lng, placeId) {
        const url = `https://www.google.com/maps/search/?api=1&query=${lat},${lng}&query_place_id=${placeId}`;
        const shareData = {
            title: title + ' Location',
            text: 'Check out the location for ' + title,
            url: url
        };

        try {
            if (navigator.share && navigator.canShare(shareData)) {
                await navigator.share(shareData);
            } else {
                copyToClipboard(url);
            }
        } catch (err) {
            if (err.name !== 'AbortError') {
                console.error('Share failed:', err);
                copyToClipboard(url);
            }
        }
    }

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('Location link copied to clipboard! You can now paste it in any app.');
        }).catch(err => {
            const input = document.createElement('textarea');
            input.value = text;
            document.body.appendChild(input);
            input.select();
            document.execCommand('copy');
            document.body.removeChild(input);
            alert('Location link copied to clipboard! You can now paste it in any app.');
        });
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places&callback=initMap" async defer></script>
@endpush
