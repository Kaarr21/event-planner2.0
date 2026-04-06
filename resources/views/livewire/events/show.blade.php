<div>
<x-slot name="header">
    <div class="flex justify-between items-center h-24">
        <div class="flex items-center gap-6">
            <h2 class="font-serif font-bold text-3xl text-slate-900 leading-tight">
                {{ $event->title }}
            </h2>

            @if($userRole !== 'invited')
                <span class="px-3 py-1 rounded-lg text-[11px] font-bold uppercase tracking-wider shadow-sm flex items-center gap-2
                    {{ $userRole === 'owner' ? 'bg-indigo-50 text-indigo-600' : 'bg-emerald-50 text-emerald-600' }}
                ">
                    <div class="w-1.5 h-1.5 rounded-full {{ $userRole === 'owner' ? 'bg-indigo-600' : 'bg-emerald-600' }}"></div>
                    {{ $userRole }}
                </span>
            @endif

            <span class="px-3 py-1 rounded-lg text-[11px] font-bold uppercase tracking-wider shadow-sm border border-slate-100 flex items-center gap-2
                {{ $event->status === 'published' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : '' }}
                {{ $event->status === 'draft' ? 'bg-indigo-50 text-indigo-600 border-indigo-100' : '' }}
                {{ $event->status === 'archived' ? 'bg-slate-100 text-slate-500 border-slate-200' : '' }}
                {{ $event->status === 'cancelled' ? 'bg-rose-50 text-rose-600 border-rose-100' : '' }}
            ">
                <div class="w-1.5 h-1.5 rounded-full
                    {{ $event->status === 'published' ? 'bg-emerald-500 shadow-[0_0_8px_rgba(52,211,153,0.5)]' : '' }}
                    {{ $event->status === 'draft' ? 'bg-indigo-500 shadow-[0_0_8px_rgba(129,140,248,0.5)]' : '' }}
                    {{ $event->status === 'cancelled' ? 'bg-rose-500' : '' }}
                "></div>
                {{ $event->status }}
            </span>
        </div>
        <a href="{{ route('dashboard') }}" class="btn-lux bg-white !text-indigo-600 border border-indigo-100 hover:bg-indigo-50 shadow-sm px-4 py-2">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-sm font-bold">arrow_back</span>
                <span class="font-bold text-xs uppercase tracking-wider">Back</span>
            </div>
        </a>
    </div>
</x-slot>


@if($event->banner_image_path)
    <div class="relative h-[550px] w-full overflow-hidden">
        <div class="absolute inset-0 bg-cover bg-center transition-transform duration-1000"
             style="background-image: url('{{ Storage::url($event->banner_image_path) }}')">
        </div>
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/40 to-transparent"></div>
        <div class="absolute bottom-0 left-0 right-0 p-12 max-w-7xl mx-auto px-6 lg:px-8">
            <span class="text-indigo-400 text-xs font-bold uppercase tracking-widest mb-2 block animate-in fade-in slide-in-from-bottom-4 duration-700">{{ $event->category?->name ?? 'EVENT' }}</span>
            <h1 class="text-5xl font-serif font-bold text-white leading-tight mb-6 animate-in fade-in slide-in-from-bottom-8 duration-700">{{ $event->title }}</h1>
            <div class="flex flex-wrap items-center gap-6 text-white/90 font-medium animate-in fade-in slide-in-from-bottom-12 duration-700">
                <div class="flex items-center gap-3 bg-white/10 backdrop-blur-md px-5 py-2 rounded-lg border border-white/20">
                    <span class="material-symbols-outlined text-indigo-400 text-sm">calendar_today</span>
                    <span class="text-base">{{ $event->start_at ? $event->start_at->format('M d, Y') : ($event->date ? $event->date->format('M d, Y') : 'Date Pending') }}</span>
                </div>

                <div x-data="{
                    target: new Date('{{ $event->start_at ? $event->start_at->toIso8601String() : ($event->date ? $event->date->toIso8601String() : '') }}').getTime(),
                    timeLeft: '',
                    update() {
                        const now = new Date().getTime();
                        const diff = this.target - now;
                        if (isNaN(this.target)) return;
                        if (diff <= 0) {
                            this.timeLeft = 'Experience Live';
                            return;
                        }
                        const hoursTotal = diff / (1000 * 60 * 60);
                        if (hoursTotal >= 24) {
                            const days = Math.ceil(diff / (1000 * 60 * 60 * 24));
                            this.timeLeft = `${days} Days Remaining`;
                        } else {
                            const hours = Math.floor(diff / (1000 * 60 * 60));
                            const mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                            this.timeLeft = `${hours}h ${mins}m`;
                        }
                    }
                }"
                x-init="update(); setInterval(() => update(), 60000)"
                class="flex items-center gap-4"
                >
                    <div x-show="timeLeft" class="flex items-center gap-4 px-8 py-3 bg-indigo-600 text-white rounded-2xl shadow-lux border border-indigo-500/50">
                        <span class="material-symbols-outlined text-white animate-pulse">timer</span>
                        <div class="flex flex-col">
                            <span class="text-[9px] font-bold uppercase tracking-widest text-indigo-200 leading-none mb-1">Commencing</span>
                            <span class="text-base font-bold uppercase tracking-wider leading-none" x-text="timeLeft"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif


<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if($userRole === 'invited')
    <div class="mb-12 p-10 glass-card rounded-lg shadow-lux animate-in fade-in zoom-in duration-700 relative overflow-hidden border border-slate-100 bg-white">
        <div class="relative flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="flex flex-col md:flex-row items-center gap-8">
                <div class="w-20 h-20 rounded-lg bg-indigo-600 flex items-center justify-center shadow-lg transform -rotate-2">
                    <span class="material-symbols-outlined text-4xl text-white">confirmation_number</span>
                </div>
                <div class="text-center md:text-left">
                    <div class="flex items-center gap-4 mb-3">
                        <img src="{{ $inviter?->profile_photo_url }}" class="w-12 h-12 rounded-full object-cover border border-slate-100" alt="{{ $inviter?->name }}">
                        <h3 class="text-3xl font-serif font-bold text-slate-900 leading-tight">You are <span class="text-indigo-600">Invited</span></h3>
                    </div>
                    <p class="text-slate-500 font-medium text-lg tracking-tight leading-relaxed max-w-xl">
                        {{ $inviter?->name ?? 'A host' }} has invited you to join this event.
                    </p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row items-center gap-4 w-full md:w-auto">
                <button wire:click="updateRSVP('attending')" class="btn-lux w-full sm:w-auto py-3 px-10 group">
                    <div class="flex items-center justify-center gap-2">
                        <span>Accept</span>
                        <span class="material-symbols-outlined text-lg transition-transform group-hover:translate-x-1">arrow_forward</span>
                    </div>
                </button>
                <button wire:click="updateRSVP('declined')" class="w-full sm:w-auto px-8 py-3 bg-white border border-slate-200 text-slate-500 text-xs font-bold uppercase tracking-wider rounded-lg hover:bg-slate-50 transition-all">
                    Decline
                </button>
            </div>
        </div>
    </div>
@endif


        <!-- Tab Navigation -->
        <div class="mb-12 border-b border-slate-100 overflow-x-auto no-scrollbar">
            <div class="flex space-x-12 min-w-max px-4">
                @php
                    $tabs = [
                        ['id' => 'overview', 'label' => 'About', 'icon' => 'info'],
                        ['id' => 'tickets', 'label' => 'Tickets', 'icon' => 'local_activity'],
                    ];

                    if ($this->hasPermission('view_tasks') || $this->hasPermission('manage_tasks') || $this->hasPermission('owner')) {
                        $tabs[] = ['id' => 'tasks', 'label' => 'Tasks', 'icon' => 'checklist'];
                    }

                    if ($this->hasPermission('view_guest_list') || $this->hasPermission('manage_invites') || $this->hasPermission('owner')) {
                        $tabs[] = ['id' => 'guests', 'label' => 'Guests', 'icon' => 'group'];
                    }

                    if ($this->hasPermission('owner') || $this->hasPermission('edit_event')) {
                        $tabs[] = ['id' => 'budget', 'label' => 'Budget', 'icon' => 'payments'];
                    }

                    if ($this->hasPermission('view_media') || $this->hasPermission('owner') || $this->hasPermission('manage_media')) {
                        $tabs[] = ['id' => 'media', 'label' => 'Media', 'icon' => 'gallery_thumbnail'];
                    }

                    if ($this->hasPermission('manage_invites') || $this->hasPermission('owner')) {
                        $tabs[] = ['id' => 'analytics', 'label' => 'Analytics', 'icon' => 'monitoring'];
                    }
                @endphp

                @foreach($tabs as $tab)
                    <button
                        wire:click="setActiveTab('{{ $tab['id'] }}')"
                        class="flex items-center gap-3 py-6 px-1 border-b-[3px] font-bold text-xs uppercase tracking-[0.25em] transition-all duration-300 relative group
                        {{ $activeTab === $tab['id']
                            ? 'border-indigo-600 text-indigo-600'
                            : 'border-transparent text-slate-400 hover:text-slate-600' }}"
                    >
                        <span class="material-symbols-outlined text-lg transition-transform group-hover:-translate-y-0.5">{{ $tab['icon'] }}</span>
                        {{ $tab['label'] }}
                    </button>
                @endforeach
            </div>
        </div>


        <!-- Status Controls for Owner -->
        @if($userRole === 'owner')
            @if($event->status === 'draft')
                <div class="mb-16 glass-card p-10 flex flex-col md:flex-row items-center justify-between gap-10 animate-in fade-in slide-in-from-top-8 duration-1000 relative overflow-hidden group">
                    <div class="absolute -top-24 -right-24 w-56 h-56 bg-indigo-50 rounded-full blur-[80px] group-hover:bg-indigo-100 transition-all duration-1000"></div>
                    <div class="flex items-center gap-8 relative z-10">
                        <div class="w-20 h-20 rounded-3xl bg-indigo-50 flex items-center justify-center text-indigo-600 shadow-inner shadow-indigo-100">
                            <span class="material-symbols-outlined text-4xl font-bold">gesture</span>
                        </div>
                        <div>
                            <h4 class="text-2xl font-display font-extrabold text-slate-900 tracking-tight leading-none mb-2">Private Concept</h4>
                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.2em] leading-none">Visibility is exclusive to the ownership team.</p>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row items-center gap-5 w-full md:w-auto relative z-10">
                        <button wire:click="publishEvent(true)" class="btn-lux w-full sm:w-auto py-5 px-12 group">
                            <div class="flex items-center justify-center gap-3">
                                <span>Publish Experience</span>
                                <span class="material-symbols-outlined text-lg transition-transform group-hover:rotate-12">send</span>
                            </div>
                        </button>
                        <button wire:click="publishEvent(false)" class="w-full sm:w-auto px-10 py-5 bg-white border border-slate-100 text-slate-400 text-[10px] font-bold uppercase tracking-widest rounded-2xl hover:bg-slate-50 transition-all">
                            Silent Launch
                        </button>
                    </div>
                </div>
            @elseif($event->status === 'published')
                <div class="mb-16 glass-card p-10 flex flex-col md:flex-row items-center justify-between gap-10 animate-in fade-in slide-in-from-top-8 duration-1000 relative overflow-hidden group">
                    <div class="absolute -top-24 -right-24 w-56 h-56 bg-emerald-50 rounded-full blur-[80px] group-hover:bg-emerald-100 transition-all duration-1000"></div>
                    <div class="flex items-center gap-8 relative z-10">
                        <div class="w-20 h-20 rounded-3xl bg-emerald-50 flex items-center justify-center text-emerald-600 shadow-inner shadow-emerald-100">
                            <span class="material-symbols-outlined text-4xl font-bold animate-pulse">sensors</span>
                        </div>
                        <div>
                            <h4 class="text-2xl font-display font-extrabold text-slate-900 tracking-tight leading-none mb-2">Live Broadcast</h4>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none">The event is active and visible to guests.</p>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row items-center gap-5 w-full md:w-auto relative z-10">
                        @if($this->hasPermission('edit_event'))
                            <button wire:click="startEditEvent" class="w-full sm:w-auto px-8 py-5 bg-white border border-slate-100 text-slate-600 text-[10px] font-bold uppercase tracking-widest rounded-2xl hover:bg-slate-50 transition-all">
                                Update Details
                            </button>
                        @endif
                        <button onclick="shareEvent('{{ addslashes($event->title) }}', '{{ url()->current() }}', '{{ addslashes(Str::limit($event->description, 100)) }}')" class="btn-lux w-full sm:w-auto py-5 px-10 group shadow-indigo-100">
                            <div class="flex items-center justify-center gap-3">
                                <span class="material-symbols-outlined text-sm">share</span>
                                <span>Broadcast</span>
                            </div>
                        </button>
                        <button wire:click="$set('status', 'archived')" class="w-full sm:w-auto px-8 py-5 bg-slate-50 text-slate-400 text-[10px] font-bold uppercase tracking-widest rounded-2xl hover:bg-slate-900 hover:text-white transition-all">
                            Archive
                        </button>
                    </div>
                </div>
            @elseif($event->status === 'archived')
                <div class="mb-12 p-8 bg-slate-50 border border-slate-100 rounded-[2.5rem] flex items-center gap-6 animate-in fade-in slide-in-from-top-6 duration-1000">
                    <div class="w-14 h-14 rounded-2xl bg-white flex items-center justify-center text-slate-400 shadow-sm">
                        <span class="material-symbols-outlined text-2xl font-bold">inventory_2</span>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-slate-900 tracking-tight">Archived Collection</h4>
                        <p class="text-sm text-slate-500 font-medium">This experience has concluded and is now stored in the archive.</p>
                    </div>
                    <button wire:click="publishEvent(false)" class="ml-auto btn-lux px-8 py-3.5">
                        Restore Live
                    </button>
                </div>
            @endif

            @if($event->status === 'cancelled')
                <div class="mb-12 p-10 bg-rose-50 border border-rose-100 rounded-[3rem] animate-in fade-in slide-in-from-top-6 duration-1000 relative overflow-hidden">
                    <div class="absolute -top-24 -right-24 w-56 h-56 bg-rose-500/10 rounded-full blur-[80px]"></div>
                    <div class="relative flex flex-col md:flex-row items-center gap-8">
                        <div class="w-20 h-20 rounded-3xl bg-rose-600 flex items-center justify-center text-white shadow-2xl shadow-rose-200">
                            <span class="material-symbols-outlined text-4xl font-bold">block</span>
                        </div>
                        <div class="flex-1 text-center md:text-left">
                            <h4 class="text-2xl font-display font-extrabold text-rose-900 tracking-tight leading-none mb-2 uppercase">Experience Cancelled</h4>
                            <p class="text-base text-rose-700/80 font-medium tracking-tight">
                                {{ $event->cancellation_reason ?: 'No specific reason provided for this cancellation.' }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            @if($this->hasPermission('edit_event') && $event->status !== 'cancelled')
                <div class="mb-12 flex justify-end">
                    <button wire:click="confirmCancellation" class="px-8 py-4 bg-white border border-rose-100 text-rose-600 text-[10px] font-bold uppercase tracking-widest rounded-2xl transition-all hover:bg-rose-50 flex items-center gap-3">
                        <span class="material-symbols-outlined text-lg">cancel</span>
                        Cancel Experience
                    </button>
                </div>
            @endif
        @endif


        <!-- Tab Content -->
        <div class="mt-6">

            <!-- Overview Tab -->
            @if($activeTab === 'overview')
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 animate-in fade-in slide-in-from-bottom-8 duration-1000">
                    <div class="lg:col-span-2 space-y-12">
                        <!-- Event Details Card -->
                        <div class="glass-card overflow-hidden border-none p-12">
                            <div class="flex items-center gap-5 text-indigo-600 mb-12">
                                <div class="w-10 h-px bg-indigo-200"></div>
                                <h3 class="text-xs font-bold uppercase tracking-[0.4em]">The Visionary Statement</h3>
                            </div>
                            <p class="text-4xl text-slate-900 font-display font-extrabold tracking-tight leading-tight mb-20">
                                {{ $event->description ?: 'No vision statement provided for this masterpiece.' }}
                            </p>

                            @if($userRole !== 'invited')
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-14 pt-12 border-t border-slate-100 no-print">
                                    <div class="flex flex-col gap-5 group">
                                        <div class="w-16 h-16 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-500 shadow-sm">
                                            <span class="material-symbols-outlined text-3xl">calendar_today</span>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Schedule</p>
                                            <p class="text-xl font-bold text-slate-900 tracking-tight">{{ $event->start_at ? $event->start_at->format('M d, Y') : ($event->date ? $event->date->format('M d, Y') : 'Date Pending') }}</p>
                                            <p class="text-xs text-slate-500 font-medium mt-1 uppercase opacity-80">{{ $event->start_at ? $event->start_at->format('h:i A') : ($event->date ? $event->date->format('h:i A') : '') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex flex-col gap-4 group">
                                        <div class="w-14 h-14 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600 transition-all duration-300 shadow-sm">
                                            <span class="material-symbols-outlined text-2xl">location_on</span>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Venue</p>
                                            <p class="text-lg font-serif font-bold text-slate-900 leading-tight line-clamp-2">{{ $event->location ?: 'Location TBD' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex flex-col gap-4 group">
                                        <div class="w-14 h-14 rounded-lg bg-violet-50 flex items-center justify-center text-violet-600 transition-all duration-300 shadow-sm">
                                            <span class="material-symbols-outlined text-2xl">curtains</span>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Category</p>
                                            <p class="text-lg font-serif font-bold text-slate-900 leading-tight uppercase">{{ $event->category?->name ?: 'UNCATEGORIZED' }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>


                    <div class="space-y-12">
                        <!-- RSVP Status Card -->
                        @if($userRole !== 'invited')
                            <div class="glass-card overflow-hidden p-10 no-print bg-white rounded-lg border border-slate-100">
                                <h3 class="text-xl font-serif font-bold text-slate-900 leading-tight mb-8">Attendance</h3>

                                <div class="space-y-4">
                                    <div class="grid grid-cols-1 gap-4">
                                        <button wire:click="updateRSVP('attending')" class="flex items-center justify-between p-5 rounded-lg border transition-all duration-200 {{ $userRSVP?->status === 'attending' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'border-slate-100 text-slate-400 hover:bg-slate-50' }}">
                                            <span class="text-xs font-bold uppercase tracking-wider">Going</span>
                                            @if($userRSVP?->status === 'attending') <span class="material-symbols-outlined font-bold text-emerald-600">verified</span> @endif
                                        </button>
                                        <button wire:click="updateRSVP('maybe')" class="flex items-center justify-between p-5 rounded-lg border transition-all duration-200 {{ $userRSVP?->status === 'maybe' ? 'bg-amber-50 border-amber-200 text-amber-700' : 'border-slate-100 text-slate-400 hover:bg-slate-50' }}">
                                            <span class="text-xs font-bold uppercase tracking-wider">Maybe</span>
                                            @if($userRSVP?->status === 'maybe') <span class="material-symbols-outlined font-bold text-amber-600">help</span> @endif
                                        </button>
                                        <button wire:click="updateRSVP('declined')" class="flex items-center justify-between p-5 rounded-lg border transition-all duration-200 {{ $userRSVP?->status === 'declined' ? 'bg-rose-50 border-rose-200 text-rose-700' : 'border-slate-100 text-slate-400 hover:bg-slate-50' }}">
                                            <span class="text-xs font-bold uppercase tracking-wider">Not Going</span>
                                            @if($userRSVP?->status === 'declined') <span class="material-symbols-outlined font-bold text-rose-600">do_not_disturb_on</span> @endif
                                        </button>
                                    </div>

                                    @if (session()->has('rsvp_message'))
                                        <div class="mt-6 flex items-center justify-center gap-2">
                                            <div class="h-1 w-1 rounded-full bg-indigo-600"></div>
                                            <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest">{{ session('rsvp_message') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>


                <!-- Location Map Card -->
                <div class="col-span-full mt-20 animate-in fade-in slide-in-from-bottom-12 duration-1000 delay-200">
                    <div class="glass-card overflow-hidden bg-white rounded-lg border border-slate-100 shadow-lux">
                        <div class="p-10 md:p-12">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8 mb-10 no-print">
                                <div>
                                    <div class="flex items-center gap-4 mb-3">
                                        <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 shadow-sm text-sm">
                                            <span class="material-symbols-outlined font-bold">map</span>
                                        </div>
                                        <h3 class="text-3xl font-serif font-bold text-slate-900 leading-tight">Venue Location</h3>
                                    </div>
                                    <p class="text-sm text-slate-500 font-medium">Find our location with ease.</p>
                                </div>

                                <div class="flex flex-wrap items-center gap-3">
                                    @if($this->hasPermission('manage_invites'))
                                        <button wire:click="bulkShareLocation" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold uppercase tracking-wider rounded-lg shadow-md shadow-emerald-100 transition-all flex items-center">
                                            <span class="material-symbols-outlined text-sm mr-2">send</span>
                                            Share Map Pin
                                        </button>
                                    @endif

                                    @if($latitude && $longitude)
                                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $latitude }},{{ $longitude }}&destination_place_id={{ $googlePlaceId }}" target="_blank" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-[11px] font-bold uppercase tracking-wider rounded-lg shadow-md shadow-indigo-100 transition-all flex items-center">
                                            <span class="material-symbols-outlined text-sm mr-2">directions</span>
                                            Directions
                                        </a>

                                        <button onclick="shareLocation('{{ $event->title }}', '{{ $latitude }}', '{{ $longitude }}', '{{ $googlePlaceId }}')" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-600 text-[11px] font-bold uppercase tracking-wider rounded-lg shadow-sm transition-all flex items-center">
                                            <span class="material-symbols-outlined text-sm mr-2">share</span>
                                            Share Location
                                        </button>
                                    @endif
                                </div>
                            </div>

                            @if($this->hasPermission('edit_event'))
                                <div class="mb-8 no-print" wire:ignore>
                                    <div class="relative">
                                        <span class="absolute left-5 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400">search</span>
                                        <input id="pac-input" type="text" placeholder="Search for a location..." class="w-full bg-slate-50 border border-slate-100 text-slate-900 text-sm rounded-lg focus:ring-2 focus:ring-indigo-600 pl-14 pr-6 py-4 transition-all" value="{{ $locationSearch }}">
                                    </div>
                                </div>
                            @endif

                            <div id="map" class="w-full h-[450px] rounded-lg bg-slate-50 border border-slate-100 overflow-hidden shadow-inner relative" wire:ignore>
                                <div class="absolute inset-0 flex items-center justify-center text-slate-400">
                                    <div class="text-center">
                                        <span class="material-symbols-outlined text-5xl text-indigo-500 block mb-4">location_on</span>
                                        <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Loading Map...</p>
                                    </div>
                                </div>
                            </div>

                            @if(session()->has('location_message'))
                                <div class="mt-8 p-6 bg-emerald-50 border border-emerald-100 rounded-3xl no-print">
                                    <p class="text-[11px] font-bold text-center text-emerald-700 uppercase tracking-widest animate-pulse">{{ session('location_message') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            @endif

            <!-- Tasks Tab -->
            @if($activeTab === 'tasks' && ($this->hasPermission('view_tasks') || $this->hasPermission('manage_tasks') || $this->hasPermission('owner')))
                <div class="max-w-5xl mx-auto animate-in fade-in slide-in-from-bottom-8 duration-1000">
                    <div class="glass-card overflow-hidden shadow-lux p-12 md:p-16">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-10 mb-16">
                            <div>
                                <div class="flex items-center gap-5 mb-4">
                                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 shadow-sm">
                                        <span class="material-symbols-outlined font-bold">assignment_turned_in</span>
                                    </div>
                                <h3 class="text-3xl font-serif font-bold text-slate-900 leading-tight">Tasks</h3>
                            </div>
                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Manage all event tasks here.</p>
                        </div>
                            <div class="flex flex-wrap items-center gap-4 no-print">
                                @if($this->hasPermission('manage_tasks'))
                                    <!-- Export Dropdown -->
                                    <div x-data="{ open: false }" class="relative">
                                        <button @click="open = !open" class="flex items-center gap-3 px-6 py-4 bg-white border border-slate-100 rounded-2xl transition-all text-[10px] font-bold uppercase tracking-widest text-slate-500 hover:bg-slate-50 shadow-sm">
                                            <span class="material-symbols-outlined text-sm">download</span>
                                            Export
                                            <span class="material-symbols-outlined text-sm transition-transform" :class="open ? 'rotate-180' : ''">expand_more</span>
                                        </button>

                                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-slate-100 z-50 overflow-hidden" x-transition>
                                            <button wire:click="exportTasksToExcel" class="w-full text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-slate-600 hover:bg-slate-50 flex items-center gap-3 transition-colors">
                                                Excel
                                            </button>
                                            <button wire:click="exportTasksToCSV" class="w-full text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-slate-600 hover:bg-slate-50 flex items-center gap-3 transition-colors">
                                                CSV
                                            </button>
                                            <button wire:click="exportTasksToPDF" class="w-full text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-slate-600 hover:bg-slate-50 flex items-center gap-3 transition-colors">
                                                PDF
                                            </button>
                                        </div>
                                    </div>

                                    <button onclick="window.print()" class="flex items-center justify-center w-14 h-14 bg-white border border-slate-100 text-slate-400 rounded-2xl transition-all hover:text-indigo-600 shadow-sm">
                                        <span class="material-symbols-outlined text-2xl">print</span>
                                    </button>

                                    <button type="button" wire:click="suggestAITasks" class="btn-lux px-6 py-3 flex items-center gap-2">
                                        <span class="material-symbols-outlined text-sm">magic_button</span>
                                        <span>AI Help</span>
                                    </button>
                                @endif
                            </div>
                        </div>


                            @if($this->hasPermission('manage_tasks'))
                            <!-- Add Task Form -->
                            <form wire:submit.prevent="addTask" class="mb-16 bg-white/5 p-8 rounded-[2.5rem] border border-white/5 no-print">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Objective</label>
                                        <input type="text" wire:model="newTaskTitle" placeholder="Describe the mission..." class="w-full bg-white dark:bg-gray-800/50 border-0 ring-1 ring-gray-200 dark:ring-white/10 text-gray-900 text-sm rounded-2xl focus:ring-2 focus:ring-brand-orange p-5 dark:text-white transition-all font-medium italic">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Deadline</label>
                                        <input type="date" wire:model="newTaskDueDate" class="w-full bg-white dark:bg-gray-800/50 border-0 ring-1 ring-gray-200 dark:ring-white/10 text-gray-900 text-sm rounded-2xl focus:ring-2 focus:ring-brand-orange p-5 dark:text-white transition-all">
                                    </div>
                                </div>
                                <div class="flex flex-col md:flex-row gap-4 items-end">
                                    <div class="flex-1 w-full space-y-1">
                                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Assign To</label>
                                        <select wire:model="newTaskAssignedTo" class="w-full bg-white border border-slate-100 text-slate-900 text-sm rounded-lg focus:ring-2 focus:ring-indigo-600 p-4 transition-all">
                                            <option value="">Select individual</option>
                                            @foreach($eligibleAssignees as $assignee)
                                                <option value="{{ $assignee->id }}">{{ $assignee->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="w-full md:w-auto btn-lux py-4 px-8 group">
                                        Add Task
                                        <span class="material-symbols-outlined text-lg ml-2">add_circle</span>
                                    </button>
                                </div>
                            </form>
                            @endif

                              <!-- AI Suggestions Section -->
                              @if (!empty($aiSuggestions))
                                 <div class="mb-10 p-8 bg-indigo-50 border border-indigo-100 rounded-lg shadow-sm relative overflow-hidden no-print">
                                     <div class="relative">
                                         <div class="flex items-center justify-between mb-6">
                                             <div>
                                                 <h4 class="text-xl font-serif font-bold text-slate-900 leading-tight">AI Suggestions</h4>
                                                 <p class="text-indigo-600 text-[10px] font-bold uppercase tracking-widest mt-1">Suggested tasks for your event.</p>
                                             </div>
                                             <button type="button" wire:click="suggestAITasks" class="w-8 h-8 bg-white border border-slate-100 rounded-md transition-colors flex items-center justify-center text-slate-400 hover:text-indigo-600">
                                                 <span class="material-symbols-outlined text-sm">refresh</span>
                                             </button>
                                         </div>
                                         <div class="space-y-3 mb-8">
                                             @foreach($aiSuggestions as $index => $suggestion)
                                                 <div class="flex items-center gap-4 bg-white p-4 rounded-lg border border-slate-100 transition-all hover:bg-slate-50">
                                                     <input type="checkbox" wire:model.live="aiSuggestions.{{ $index }}.selected" class="w-5 h-5 bg-transparent border-2 border-indigo-200 rounded text-indigo-600 focus:ring-0">
                                                     <input type="text" wire:model="aiSuggestions.{{ $index }}.title" class="flex-1 bg-transparent border-0 text-slate-900 p-0 text-base font-bold italic tracking-tight focus:ring-0" {{ !$suggestion['selected'] ? 'disabled' : '' }}>
                                                     <button wire:click="removeSuggestion({{ $index }})" class="text-slate-300 hover:text-rose-500 transition-colors">
                                                         <span class="material-symbols-outlined text-lg">close</span>
                                                     </button>
                                                 </div>
                                             @endforeach
                                         </div>
                                         <div class="flex justify-end">
                                             <button wire:click="saveSuggestions" class="btn-lux px-8 py-3">
                                                 Save Selected
                                             </button>
                                         </div>
                                     </div>
                                 </div>
                             @endif

                            <!-- Tasks List -->
                            <div class="space-y-8 print-container">
                                @forelse ($tasks as $task)
                                    <div class="group p-8 bg-slate-50/50 rounded-lg border border-transparent hover:border-indigo-100 transition-all duration-300 hover:bg-white shadow-sm">
                                        @if($editingTaskId === $task->id)
                                            <!-- Inline Edit -->
                                            <div class="space-y-6">
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div class="space-y-1">
                                                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Task Title</label>
                                                        <input type="text" wire:model="editTaskTitle" class="w-full bg-white border border-slate-100 rounded-lg p-4 text-sm font-bold italic focus:ring-2 focus:ring-indigo-600" placeholder="Task title">
                                                    </div>
                                                    <div class="space-y-1">
                                                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Due Date</label>
                                                        <input type="date" wire:model="editTaskDueDate" class="w-full bg-white border border-slate-100 rounded-lg p-4 text-sm focus:ring-2 focus:ring-indigo-600">
                                                    </div>
                                                </div>
                                                <div class="space-y-1">
                                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Comments</label>
                                                    <textarea wire:model="editTaskDescription" rows="2" class="w-full bg-white border border-slate-100 rounded-lg p-4 text-sm font-medium italic focus:ring-2 focus:ring-indigo-600" placeholder="Add comments here..."></textarea>
                                                </div>
                                                <div class="flex items-center justify-between gap-4">
                                                    <div class="flex-1 space-y-1">
                                                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Assign To</label>
                                                        <select wire:model="editTaskAssignedTo" class="w-full bg-white border border-slate-100 rounded-lg p-4 text-xs font-bold italic focus:ring-2 focus:ring-indigo-600 appearance-none">
                                                            <option value="">Select individual</option>
                                                            @foreach($eligibleAssignees as $assignee)
                                                                <option value="{{ $assignee->id }}">{{ $assignee->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="flex gap-3 pt-4">
                                                        <button wire:click="cancelEditTask" class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-slate-400 hover:text-slate-600">Cancel</button>
                                                        <button wire:click="saveTask" class="btn-lux px-8 py-3">Save</button>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="flex items-start justify-between gap-6">
                                                <div class="flex items-start gap-6">
                                                    <div class="pt-1">
                                                        <button wire:click="toggleTask({{ $task->id }})" class="w-8 h-8 rounded-md border-2 flex items-center justify-center transition-all duration-300 {{ $task->completed ? 'bg-emerald-500 border-emerald-500 text-white shadow-md' : 'bg-white border-slate-100 hover:border-emerald-400 text-transparent' }}">
                                                            @if($task->completed) <span class="material-symbols-outlined font-bold text-sm">check</span> @endif
                                                        </button>
                                                    </div>
                                                    <div class="space-y-2">
                                                        <p class="text-xl font-serif font-bold transition-all duration-300 {{ $task->completed ? 'line-through text-slate-300' : 'text-slate-900' }}">
                                                            {{ $task->title }}
                                                        </p>
                                                        @if ($task->description)
                                                            <p class="text-sm text-slate-500 font-medium italic opacity-80 leading-relaxed max-w-2xl">{{ $task->description }}</p>
                                                        @endif

                                                        <div class="flex flex-wrap items-center gap-6 pt-2">
                                                            @if ($task->due_date)
                                                                <div class="flex items-center gap-3 px-4 py-2 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100">
                                                                    <span class="material-symbols-outlined text-sm font-bold">event</span>
                                                                    <span class="text-[10px] font-bold uppercase tracking-wider">{{ $task->due_date->format('M d, Y') }}</span>
                                                                </div>
                                                            @endif

                                                            @if($task->assignee)
                                                                <div class="flex items-center gap-4">
                                                                    <div class="h-10 w-10 rounded-xl bg-slate-900 flex items-center justify-center text-[10px] font-bold uppercase text-white shadow-lux">
                                                                        {{ substr($task->assignee->name, 0, 1) }}
                                                                    </div>
                                                                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-widest italic">{{ $task->assignee->name }}</span>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <!-- Interaction for Assigned User -->
                                                        @if($task->assigned_to === auth()->id() && !$task->completed)
                                                            <div class="mt-8 flex gap-4">
                                                                @if($task->assignment_status === 'pending')
                                                                    <button wire:click="acceptTask({{ $task->id }})" class="btn-lux px-8 py-3">Accept Assignment</button>
                                                                    <button wire:click="declineTask({{ $task->id }})" class="px-8 py-3 bg-white border border-slate-100 text-slate-400 text-[10px] font-bold rounded-xl uppercase tracking-widest hover:bg-rose-50 hover:text-rose-600 transition-all">Decline</button>
                                                                 @elseif($task->assignment_status === 'accepted')
                                                                    @if($completingTaskId === $task->id)
                                                                        <div class="w-full space-y-4 mt-6 bg-slate-50 p-6 rounded-lg border border-slate-100">
                                                                            <textarea wire:model="completionComment" placeholder="Add comments..." class="w-full bg-white border border-slate-100 rounded-lg p-4 text-sm font-medium italic focus:ring-2 focus:ring-indigo-600 outline-none"></textarea>
                                                                            <div class="flex gap-4">
                                                                                <button wire:click="completeTask" class="btn-lux px-8 py-3">Finish</button>
                                                                                <button wire:click="$set('completingTaskId', null)" class="text-[11px] font-bold text-slate-400 uppercase tracking-widest hover:text-slate-600">Cancel</button>
                                                                            </div>
                                                                        </div>
                                                                    @else
                                                                        <button wire:click="startCompletion({{ $task->id }})" class="text-indigo-600 text-[11px] font-bold uppercase tracking-widest flex items-center gap-2 transition-all hover:translate-x-1">
                                                                            <span class="material-symbols-outlined text-lg font-bold">task_alt</span>
                                                                            Complete Task
                                                                        </button>
                                                                    @endif
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity no-print">
                                                    @if($this->hasPermission('manage_tasks'))
                                                        <button wire:click="startEditTask({{ $task->id }})" class="w-12 h-12 rounded-xl text-slate-400 hover:bg-indigo-50 hover:text-indigo-600 transition-all flex items-center justify-center">
                                                            <span class="material-symbols-outlined text-xl">edit</span>
                                                        </button>
                                                        <button wire:click="deleteTask({{ $task->id }})" class="w-12 h-12 rounded-xl text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-all flex items-center justify-center">
                                                            <span class="material-symbols-outlined text-xl">delete</span>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <div class="text-center py-24 bg-slate-50/30 rounded-lg border-2 border-dashed border-slate-100">
                                        <div class="w-20 h-20 rounded-full bg-white mx-auto flex items-center justify-center text-slate-200 shadow-sm mb-6">
                                            <span class="material-symbols-outlined text-4xl">checklist</span>
                                        </div>
                                        <p class="text-slate-400 font-bold uppercase tracking-widest text-xs">No tasks found.</p>
                                    </div>
                                @endforelse
                            </div>

                        </div>
                    </div>
                </div>
            @endif

            <!-- Guests Tab -->
            @if($activeTab === 'guests' && ($this->hasPermission('view_guest_list') || $this->hasPermission('manage_invites') || $this->hasPermission('owner')))
                <div class="max-w-5xl mx-auto animate-in fade-in slide-in-from-bottom-8 duration-1000">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                        <div class="lg:col-span-2 space-y-12">
                            <!-- Guest List Card -->
                            <div class="glass-card overflow-hidden shadow-lux p-12 md:p-16">
                                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-10 mb-16 no-print">
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 shadow-sm">
                                                <span class="material-symbols-outlined font-bold">group</span>
                                            </div>
                                            <h3 class="text-3xl font-serif font-bold text-slate-900 leading-tight">Guests</h3>
                                        </div>
                                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Manage your event guests.</p>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-4 w-full md:w-auto">
                                        @if($this->hasPermission('manage_invites'))
                                            @php $draftCount = $event->invites()->where('status', 'draft')->count(); @endphp

                                            <div class="flex flex-col sm:flex-row items-center gap-4 w-full sm:w-auto">
                                                <button wire:click="startImport" class="w-full sm:w-auto px-8 py-4 bg-white border border-slate-100 text-slate-500 font-bold text-[10px] uppercase tracking-widest rounded-2xl hover:bg-slate-50 transition-all flex items-center justify-center gap-3">
                                                    <span class="material-symbols-outlined text-sm">upload_file</span>
                                                    Import List
                                                </button>

                                                @if($draftCount > 0)
                                                    <button wire:click="sendAllDrafts" class="btn-lux w-full sm:w-auto py-3 px-8 group">
                                                        <div class="flex items-center justify-center gap-2">
                                                            <span>Send All ({{ $draftCount }})</span>
                                                            <span class="material-symbols-outlined text-sm">send</span>
                                                        </div>
                                                    </button>
                                                @endif
                                            </div>

                                            <div class="flex items-center gap-3 ml-2">
                                                <!-- Export Dropdown -->
                                                <div x-data="{ open: false }" class="relative">
                                                    <button @click="open = !open" class="flex items-center gap-3 px-6 py-4 bg-white border border-slate-100 rounded-2xl transition-all text-[10px] font-bold uppercase tracking-widest text-slate-400 hover:bg-slate-50 shadow-sm">
                                                        <span class="material-symbols-outlined text-sm">download</span>
                                                        Export
                                                    </button>

                                                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-4 w-60 bg-white rounded-[2.5rem] shadow-lux border border-slate-100 z-50 overflow-hidden" x-transition>
                                                        <button wire:click="exportToExcel" class="w-full text-left px-7 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-600 hover:bg-emerald-50 flex items-center gap-4 transition-colors">
                                                            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                                                <span class="material-symbols-outlined text-sm">table_chart</span>
                                                            </div>
                                                            Excel List
                                                        </button>
                                                        <button wire:click="exportToCSV" class="w-full text-left px-7 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-600 hover:bg-indigo-50 flex items-center gap-4 transition-colors">
                                                            <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center">
                                                                <span class="material-symbols-outlined text-sm">description</span>
                                                            </div>
                                                            CSV Format
                                                        </button>
                                                        <button wire:click="exportToPDF" class="w-full text-left px-7 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-600 hover:bg-indigo-50 flex items-center gap-4 transition-colors">
                                                            <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center">
                                                                <span class="material-symbols-outlined text-sm">picture_as_pdf</span>
                                                            </div>
                                                            PDF Dossier
                                                        </button>
                                                    </div>
                                                </div>

                                                <button onclick="window.print()" class="flex items-center justify-center w-14 h-14 bg-white border border-slate-100 text-slate-400 rounded-2xl transition-all hover:text-indigo-600 shadow-sm">
                                                    <span class="material-symbols-outlined text-2xl">print</span>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="print-only mb-10 border-b pb-6">
                                    <h1 class="text-3xl font-display font-extrabold uppercase tracking-tight text-slate-950">{{ $event->title }}</h1>
                                    <p class="text-slate-500 font-bold text-[10px] uppercase tracking-widest mt-2">Confirmed Guest List • {{ now()->format('M d, Y') }}</p>
                                </div>

                                <div class="space-y-20">
                                    <div class="space-y-8 print-container">
                                        <h4 class="text-[10px] font-bold uppercase tracking-[0.3em] text-slate-400 flex items-center gap-4 ml-6 opacity-80">
                                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                                            Confirmed Responses
                                        </h4>
                                        @forelse ($rsvps as $rsvp)
                                            <div class="flex flex-col sm:flex-row items-center justify-between gap-6 p-8 bg-slate-50/50 rounded-lg border border-transparent hover:border-indigo-100 transition-all duration-300 hover:shadow-md hover:bg-white">
                                                <div class="flex items-center gap-6">
                                                    <div class="relative">
                                                        <img src="{{ $rsvp->user->profile_photo_url }}" class="h-16 w-16 rounded-lg object-cover ring-2 ring-white shadow-sm" alt="{{ $rsvp->user->name }}">
                                                        <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full border-2 border-white flex items-center justify-center text-white text-[8px] font-bold shadow-sm
                                                            {{ $rsvp->status === 'attending' ? 'bg-emerald-500' : '' }}
                                                            {{ $rsvp->status === 'maybe' ? 'bg-amber-500' : '' }}
                                                            {{ $rsvp->status === 'declined' ? 'bg-rose-500' : '' }}
                                                        ">
                                                            @if($rsvp->status === 'attending') <span class="material-symbols-outlined text-xs font-bold">check</span> @endif
                                                            @if($rsvp->status === 'maybe') <span class="material-symbols-outlined text-xs font-bold">help</span> @endif
                                                            @if($rsvp->status === 'declined') <span class="material-symbols-outlined text-xs font-bold">close</span> @endif
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <p class="text-xl font-serif font-bold text-slate-900 leading-tight">{{ $rsvp->user->name }}</p>
                                                        <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">{{ $rsvp->user->email }}</p>
                                                    </div>
                                                </div>

                                                <div class="flex items-center gap-6">
                                                    @if($this->hasPermission('owner') || $this->hasPermission('manage_invites'))
                                                        <div class="flex items-center gap-2 bg-white p-2 rounded-lg shadow-inner border border-slate-100">
                                                            <button wire:click="toggleGuestPermission({{ $rsvp->id }}, 'can_view_guests')"
                                                                class="w-10 h-10 rounded-md transition-all flex items-center justify-center {{ $rsvp->can_view_guests ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-50' }}"
                                                                title="View Guest List">
                                                                <span class="material-symbols-outlined text-lg">group</span>
                                                            </button>
                                                            <button wire:click="toggleGuestPermission({{ $rsvp->id }}, 'can_view_checklist')"
                                                                class="w-10 h-10 rounded-md transition-all flex items-center justify-center {{ $rsvp->can_view_checklist ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-50' }}"
                                                                title="View Tasks">
                                                                <span class="material-symbols-outlined text-lg">assignment</span>
                                                            </button>
                                                        </div>
                                                    @endif

                                                    <span class="px-5 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider border shadow-sm
                                                        {{ $rsvp->status === 'attending' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : '' }}
                                                        {{ $rsvp->status === 'maybe' ? 'bg-amber-50 text-amber-700 border-amber-100' : '' }}
                                                        {{ $rsvp->status === 'declined' ? 'bg-rose-50 text-rose-700 border-rose-100' : '' }}
                                                    ">
                                                        {{ ucfirst($rsvp->status) }}
                                                    </span>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center py-20 bg-slate-50/30 rounded-lg border-2 border-dashed border-slate-100">
                                                <div class="w-16 h-16 rounded-full bg-white mx-auto flex items-center justify-center text-slate-200 shadow-sm mb-4">
                                                    <span class="material-symbols-outlined text-3xl">person_search</span>
                                                </div>
                                                <p class="text-slate-400 font-bold uppercase tracking-widest text-[10px]">No guests found yet.</p>
                                            </div>
                                        @endforelse
                                    </div>

                                    @if($this->hasPermission('manage_invites'))
                                        <div class="pt-16 border-t border-slate-100 space-y-10">
                                            <h4 class="text-[10px] font-bold uppercase tracking-widest text-slate-400 flex items-center gap-4 ml-6 opacity-80">
                                                <div class="w-1.5 h-1.5 rounded-full bg-indigo-500"></div>
                                                Active Invitations
                                            </h4>
                                            <livewire:events.invited-list :event="$event" :selectedInviteIds="$selectedInviteIds" :canEditEvent="$this->hasPermission('edit_event')" />

                                            @if(count($selectedInviteIds) > 0 && $this->hasPermission('edit_event'))
                                                <div class="fixed bottom-12 right-12 z-50">
                                                    <button wire:click="openBulkNotificationModal" class="btn-lux px-10 py-5 group shadow-2xl flex items-center gap-4 animate-in fade-in slide-in-from-bottom-12">
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
                    </div>
                </div>

                        </div>

                        <div>
                            <!-- Invite Form (Moved to Tab) -->
                            @if($this->hasPermission('manage_invites'))
                                <div class="bg-indigo-600 rounded-lg shadow-lg p-8 text-white sticky top-8 no-print border border-indigo-700">
                                    <div class="relative">
                                        <h4 class="text-2xl font-serif font-bold mb-2 leading-none">Add Guests</h4>
                                        <p class="text-white/70 text-[10px] font-bold uppercase tracking-widest mb-8">Invite more people to your event.</p>
                                        <livewire:events.invite-form :event="$event" />
                                    </div>
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

            <!-- Tickets Tab -->
            @if($activeTab === 'tickets')
                <div class="max-w-4xl mx-auto">
                    <livewire:events.event-tickets :event="$event" />
                </div>
            @endif

            <!-- Budget Tab -->
            @if($activeTab === 'budget')
                <div class="animate-in fade-in slide-in-from-bottom-4 duration-500">
                    <livewire:events.budget-tracking :event="$event" :user-permissions="$userPermissions" :user-role="$userRole" />
                </div>
            @endif

            <!-- Analytics Tab (Owner/Organizers) -->
            @if($activeTab === 'analytics' && ($this->hasPermission('manage_invites') || $this->hasPermission('owner')))
                <div class="animate-in fade-in slide-in-from-bottom-4 duration-500 space-y-12">
                    <!-- Analytics Section -->
                    <livewire:events.analytics-dashboard :event="$event" />
                </div>
            @endif

        </div>

        <!-- Cancellation Confirmation Modal -->
        @if($isConfirmingCancellation)
            <div class="fixed inset-0 z-[100] flex items-center justify-center p-6 sm:p-0 overflow-y-auto" role="dialog" aria-modal="true">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xl transition-opacity" aria-hidden="true" wire:click="$set('isConfirmingCancellation', false)"></div>

                <div class="relative w-full max-w-xl bg-white rounded-[3rem] shadow-lux border border-slate-100 overflow-hidden animate-in fade-in zoom-in-95 duration-500">
                    <div class="p-12 md:p-16">
                        <div class="flex items-center gap-6 mb-10">
                            <div class="w-16 h-16 rounded-[2rem] bg-rose-50 flex items-center justify-center text-rose-500 shadow-sm">
                                <span class="material-symbols-outlined text-3xl font-bold">warning</span>
                            </div>
                            <h3 class="text-3xl font-display font-extrabold text-slate-900 tracking-tight">Revoke Event?</h3>
                        </div>

                        <p class="text-sm text-slate-500 font-medium italic mb-12 leading-relaxed opacity-80">
                            Are you certain you wish to cancel <span class="text-slate-900 font-bold">"{{ $event->title }}"</span>? This event will be permanently cancelled, and all guests will be notified.
                        </p>

                        <div class="space-y-3 mb-12">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em] ml-1">Rationale for Revocation</label>
                            <textarea wire:model="cancellationReason" rows="3" class="w-full bg-slate-50 border-none ring-1 ring-slate-100 text-slate-900 text-sm rounded-lg focus:ring-2 focus:ring-rose-500 p-6 transition-all font-medium shadow-inner" placeholder="Provide a reason for your guests..."></textarea>
                            @error('cancellationReason') <span class="text-[10px] text-rose-500 font-bold uppercase ml-4">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex flex-col sm:flex-row-reverse gap-5">
                            <button wire:click="cancelEvent" class="w-full sm:w-auto px-10 py-5 bg-rose-600 hover:bg-rose-700 text-white text-[11px] font-bold uppercase tracking-widest rounded-2xl shadow-xl shadow-rose-200 transition-all hover:scale-105 active:scale-95">
                                Yes, Cancel Event
                            </button>
                            <button wire:click="$set('isConfirmingCancellation', false)" class="w-full sm:w-auto px-10 py-5 bg-white border border-slate-100 text-slate-400 text-[11px] font-bold uppercase tracking-widest rounded-2xl hover:bg-slate-50 transition-all">
                                Retain Event
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Guest Import Modal -->
        @if($isImporting)
            <div class="fixed inset-0 z-[100] flex items-center justify-center p-6 sm:p-0 overflow-y-auto" role="dialog" aria-modal="true">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xl transition-opacity animate-in fade-in duration-700" aria-hidden="true" wire:click="$set('isImporting', false)"></div>

                <div class="relative w-full max-w-5xl bg-white rounded-lg shadow-xl border border-slate-100 overflow-hidden">
                    <div class="p-12 md:p-16 border-b border-slate-50">
                        <div class="flex justify-between items-start">
                            <div class="space-y-3">
                                <h3 class="text-3xl font-serif font-bold text-slate-900 leading-tight">Import Guests</h3>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Upload a file to add multiple guests at once.</p>
                            </div>
                            <button wire:click="$set('isImporting', false)" class="w-14 h-14 rounded-2xl bg-slate-50 hover:bg-indigo-50 flex items-center justify-center text-slate-400 hover:text-indigo-600 transition-all hover:rotate-90">
                                <span class="material-symbols-outlined text-2xl font-bold">close</span>
                            </button>
                        </div>
                    </div>

                    <div class="p-12 md:p-16 overflow-y-auto max-h-[70vh] custom-scrollbar">
                        @if (session()->has('error'))
                            <div class="mb-10 p-6 bg-rose-50 border border-rose-100 rounded-3xl flex items-center gap-4 text-rose-600 text-[11px] font-bold uppercase tracking-widest animate-pulse">
                                <span class="material-symbols-outlined text-lg">error</span>
                                {{ session('error') }}
                            </div>
                        @endif
                        @if (session()->has('message'))
                            <div class="mb-10 p-6 bg-emerald-50 border border-emerald-100 rounded-3xl flex items-center gap-4 text-emerald-600 text-[11px] font-bold uppercase tracking-widest">
                                <span class="material-symbols-outlined text-lg">check_circle</span>
                                {{ session('message') }}
                            </div>
                        @endif

                        @if(empty($importedGuests))
                            <div class="space-y-12">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                    <div class="p-10 bg-slate-50 rounded-[3rem] border border-transparent hover:border-indigo-100 transition-all group shadow-sm hover:shadow-indigo-50/30">
                                        <div class="w-16 h-16 rounded-[1.5rem] bg-white flex items-center justify-center text-indigo-600 shadow-sm mb-8 group-hover:scale-110 transition-transform duration-500">
                                            <span class="material-symbols-outlined text-3xl font-bold">download</span>
                                        </div>
                                        <h4 class="text-xl font-serif font-bold text-slate-900 mb-3">Step 1: Download Template</h4>
                                        <p class="text-sm text-slate-500 font-medium mb-6 leading-relaxed">Download our table format to make sure your guest data fits perfectly.</p>
                                        <button wire:click="downloadTemplate" class="inline-flex items-center gap-3 text-xs font-bold text-indigo-600 hover:text-indigo-800 uppercase tracking-widest bg-white border border-slate-100 px-6 py-3 rounded-lg shadow-sm transition-all">
                                            Get Template
                                            <span class="material-symbols-outlined text-lg">east</span>
                                        </button>
                                    </div>

                                    <div class="p-10 bg-slate-50 rounded-[3rem] border border-transparent hover:border-emerald-100 transition-all group shadow-sm hover:shadow-emerald-50/30">
                                        <div class="w-16 h-16 rounded-[1.5rem] bg-white flex items-center justify-center text-emerald-600 shadow-sm mb-8 group-hover:scale-110 transition-transform duration-500">
                                            <span class="material-symbols-outlined text-3xl font-bold">upload_file</span>
                                        </div>
                                        <h4 class="text-xl font-serif font-bold text-slate-900 mb-3">Step 2: Upload File</h4>
                                        <p class="text-sm text-slate-500 font-medium mb-6 leading-relaxed">Once your list is ready, upload the Excel or CSV file for us to process.</p>
                                    </div>
                                </div>

                                <div class="relative group">
                                    <div class="absolute inset-0 bg-indigo-50 rounded-[4rem] border-2 border-dashed border-indigo-100 group-hover:bg-indigo-100/50 transition-all pointer-events-none"></div>
                                    <input type="file" wire:model="guestImportFile" class="relative z-10 w-full opacity-0 h-80 cursor-pointer">
                                    <div class="absolute inset-0 flex flex-col items-center justify-center gap-8 pointer-events-none">
                                        <div class="w-24 h-24 rounded-[2.5rem] bg-white shadow-lux flex items-center justify-center text-indigo-600 group-hover:scale-110 transition-transform duration-700">
                                            <span class="material-symbols-outlined text-5xl font-bold">cloud_upload</span>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-xl font-bold text-slate-900 tracking-tight italic">Relinquish file here or click to browse</p>
                                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.4em] mt-3">Compatible with XLSX, XLS, and CSV</p>
                                        </div>
                                    </div>
                                </div>

                                <div wire:loading wire:target="guestImportFile" class="w-full">
                                    <div class="flex items-center justify-center gap-6 p-12 bg-slate-50 rounded-[3rem] animate-pulse shadow-inner">
                                        <div class="w-8 h-8 border-[3px] border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Reading guest list...</p>
                                    </div>
                                </div>

                                @if($guestImportFile)
                                    <div class="flex items-center justify-between p-10 bg-white rounded-[3rem] border border-emerald-100 shadow-xl shadow-emerald-50 animate-in fade-in zoom-in-95">
                                        <div class="flex items-center gap-8">
                                            <div class="w-16 h-16 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 shadow-sm">
                                                <span class="material-symbols-outlined text-3xl font-bold">check_circle</span>
                                            </div>
                                            <div>
                                                <p class="text-xl font-serif font-bold text-slate-900 leading-none mb-2">File Ready</p>
                                                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">{{ $guestImportFile->getClientOriginalName() }}</p>
                                            </div>
                                        </div>
                                        <button wire:click="uploadGuests" class="btn-lux px-10 py-4">
                                            Import Now
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="space-y-12">
                                <div class="flex flex-col sm:flex-row justify-between items-center gap-10 p-10 bg-slate-50 rounded-[3.5rem] border border-slate-100 shadow-inner">
                                    <div class="space-y-2 text-center sm:text-left">
                                        <h4 class="text-2xl font-serif font-bold text-slate-900">Review Guests</h4>
                                        <p class="text-xs font-bold text-indigo-600 uppercase tracking-widest">{{ count($importedGuests) }} guests found</p>
                                    </div>
                                    <div class="relative w-full sm:w-80">
                                        <span class="absolute left-6 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400">search</span>
                                        <input type="text" wire:model.live="draftSearch" placeholder="Search entries..." class="w-full pl-16 pr-6 py-4 bg-white border-none ring-1 ring-slate-100 rounded-[1.5rem] text-[11px] font-bold uppercase tracking-widest transition-all focus:ring-2 focus:ring-indigo-600 shadow-sm outline-none">
                                    </div>
                                </div>

                                <div class="bg-white rounded-[3.5rem] border border-slate-100 overflow-hidden shadow-lux">
                                    <table class="w-full text-left border-collapse">
                                        <thead class="bg-slate-50 border-b border-slate-100">
                                            <tr>
                                                <th class="px-10 py-6 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Name</th>
                                                <th class="px-10 py-6 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Email</th>
                                                <th class="px-10 py-6 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Phone</th>
                                                <th class="px-10 py-6"></th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-50">
                                            @php
                                                $filteredGuests = collect($importedGuests)->filter(function($guest) {
                                                    if (empty($this->draftSearch)) return true;
                                                    $search = strtolower($this->draftSearch);
                                                    return str_contains(strtolower($guest['name']), $search) || 
                                                           str_contains(strtolower($guest['email']), $search);
                                                });
                                            @endphp
                                            @foreach($filteredGuests as $index => $guest)
                                            <tr class="group/row hover:bg-slate-50/50 transition-all duration-500">
                                                <td class="px-10 py-8">
                                                    <div class="flex items-center gap-4">
                                                        <span class="material-symbols-outlined text-sm text-slate-300 group-hover/row:text-indigo-400 transition-colors">edit_note</span>
                                                        <input type="text" 
                                                               wire:change="updateImportedGuest({{ $index }}, 'name', $event.target.value)"
                                                               value="{{ $guest['name'] }}"
                                                               class="w-full bg-transparent border-none p-0 text-base font-bold tracking-tight focus:ring-0 italic outline-none {{ isset($guest['errors']['name']) ? 'text-rose-500' : 'text-slate-900' }}">
                                                    </div>
                                                    @if(isset($guest['errors']['name']))
                                                        <div class="text-[9px] font-bold text-rose-500 uppercase tracking-[0.2em] mt-2 ml-10">{{ $guest['errors']['name'] }}</div>
                                                    @endif
                                                </td>
                                                <td class="px-10 py-8">
                                                    <div class="flex items-center gap-4">
                                                        <span class="material-symbols-outlined text-sm text-slate-300 group-hover/row:text-indigo-400 transition-colors">alternate_email</span>
                                                        <input type="email" 
                                                               wire:change="updateImportedGuest({{ $index }}, 'email', $event.target.value)"
                                                               value="{{ $guest['email'] }}"
                                                               class="w-full bg-transparent border-none p-0 text-sm font-bold tracking-tight focus:ring-0 italic outline-none {{ isset($guest['errors']['email']) ? 'text-rose-500' : 'text-slate-900' }}">
                                                    </div>
                                                    @if(isset($guest['errors']['email']))
                                                        <div class="text-[9px] font-bold text-rose-500 uppercase tracking-[0.2em] mt-2 ml-10">{{ $guest['errors']['email'] }}</div>
                                                    @endif
                                                </td>
                                                <td class="px-10 py-8">
                                                    <div class="flex items-center gap-4">
                                                        <span class="material-symbols-outlined text-sm text-slate-300 group-hover/row:text-indigo-400 transition-colors">phone_iphone</span>
                                                        <input type="text" 
                                                               wire:change="updateImportedGuest({{ $index }}, 'phone', $event.target.value)"
                                                               value="{{ $guest['phone'] }}"
                                                               class="w-full bg-transparent border-none p-0 text-sm font-bold tracking-tight focus:ring-0 text-slate-500 italic outline-none">
                                                    </div>
                                                </td>
                                                <td class="px-10 py-8 text-right">
                                                    <button wire:click="removeImportedGuest({{ $index }})" class="w-12 h-12 rounded-xl text-slate-300 hover:bg-rose-50 hover:text-rose-500 transition-all flex items-center justify-center hover:rotate-6">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="flex flex-col sm:flex-row justify-between items-center gap-10 mt-12 pt-12 border-t border-slate-100">
                                    <button wire:click="$set('importedGuests', [])" class="text-xs font-bold text-slate-400 uppercase tracking-widest hover:text-rose-600 transition-colors">
                                        Clear and Restart
                                    </button>
                                    <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
                                        @php
                                            $hasErrors = collect($importedGuests)->pluck('errors')->flatten()->isNotEmpty();
                                        @endphp
                                        <button wire:click="finalizeImport(true)" 
                                                {{ $hasErrors ? 'disabled' : '' }}
                                                class="px-8 py-4 bg-white border border-slate-200 text-slate-600 text-xs font-bold uppercase tracking-widest rounded-lg transition-all hover:bg-slate-50 disabled:opacity-30 disabled:cursor-not-allowed shadow-sm">
                                            Save as Draft
                                        </button>
                                        <button wire:click="finalizeImport(false)" 
                                                {{ $hasErrors ? 'disabled' : '' }}
                                                class="btn-lux px-10 py-4 disabled:opacity-30 disabled:cursor-not-allowed">
                                            Send Invites Now
                                        </button>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <button wire:click="$set('isImporting', false)" class="text-[10px] font-bold text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-colors">Close Portal</button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif        <!-- Draft Edit Modal -->
        @if($isEditingDraft)
            <div class="fixed inset-0 z-[100] flex items-center justify-center p-6 sm:p-0 overflow-y-auto" role="dialog" aria-modal="true">
                <div class="fixed inset-0 bg-slate-900/60 transition-opacity" aria-hidden="true" wire:click="$set('isEditingDraft', false)"></div>

                <div class="relative w-full max-w-2xl bg-white rounded-lg shadow-xl border border-slate-100 overflow-hidden">
                    <div class="p-10 md:p-12 border-b border-slate-50">
                        <div class="flex justify-between items-start">
                            <div class="space-y-2">
                                <h3 class="text-2xl font-serif font-bold text-slate-900 leading-tight">Edit Draft</h3>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Update guest details before saving.</p>
                            </div>
                            <button wire:click="$set('isEditingDraft', false)" class="w-12 h-12 rounded-lg bg-slate-50 hover:bg-indigo-50 flex items-center justify-center text-slate-400 hover:text-indigo-600 transition-all">
                                <span class="material-symbols-outlined text-2xl font-bold">close</span>
                            </button>
                        </div>
                    </div>

                    <form wire:submit.prevent="updateDraft" class="p-10 md:p-12 space-y-10">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Guest Name</label>
                                <input type="text" wire:model="draftName" class="w-full bg-white border border-slate-200 text-slate-900 text-sm font-bold rounded-lg focus:ring-2 focus:ring-indigo-600 p-4 outline-none" placeholder="e.g. John Doe">
                                @error('draftName') <span class="text-[10px] text-rose-500 font-bold uppercase tracking-widest ml-4 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-3">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Email Address</label>
                                <input type="email" wire:model="draftEmail" class="w-full bg-white border border-slate-200 text-slate-900 text-sm font-bold rounded-lg focus:ring-2 focus:ring-indigo-600 p-4 outline-none" placeholder="john@example.com">
                                @error('draftEmail') <span class="text-[10px] text-rose-500 font-bold uppercase tracking-widest ml-4 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-3">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Phone Number</label>
                                <input type="text" wire:model="draftPhone" class="w-full bg-white border border-slate-200 text-slate-900 text-sm font-bold rounded-lg focus:ring-2 focus:ring-indigo-600 p-4 outline-none" placeholder="+254 ...">
                                @error('draftPhone') <span class="text-[10px] text-rose-500 font-bold uppercase tracking-widest ml-4 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row-reverse gap-4 pt-10 border-t border-slate-50">
                            <button type="submit" class="btn-lux px-10 py-4">
                                Save Changes
                            </button>
                            <button type="button" wire:click="$set('isEditingDraft', false)" class="px-8 py-4 bg-white border border-slate-100 text-slate-400 hover:text-slate-600 rounded-lg transition-all font-bold text-xs uppercase tracking-widest">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <!-- Bulk Notification Modal -->
        @if($showBulkNotificationModal)
            <div class="fixed inset-0 z-[100] flex items-center justify-center p-6 sm:p-0 overflow-y-auto" role="dialog" aria-modal="true">
                <div class="fixed inset-0 bg-slate-900/60 transition-opacity" aria-hidden="true" wire:click="$set('showBulkNotificationModal', false)"></div>

                <div class="relative w-full max-w-2xl bg-white rounded-lg shadow-xl border border-slate-100 overflow-hidden">
                    <div class="p-10 md:p-12 border-b border-slate-50">
                        <div class="flex justify-between items-start">
                            <div class="space-y-2">
                                <h3 class="text-2xl font-serif font-bold text-slate-900 leading-tight">Send Message</h3>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Send a message to all selected guests.</p>
                            </div>
                            <button wire:click="$set('showBulkNotificationModal', false)" class="w-12 h-12 rounded-lg bg-slate-50 hover:bg-indigo-50 flex items-center justify-center text-slate-400 hover:text-indigo-600 transition-all">
                                <span class="material-symbols-outlined text-2xl font-bold">close</span>
                            </button>
                        </div>
                    </div>

                    <form wire:submit.prevent="sendBulkNotification" class="p-10 md:p-12 space-y-10">
                        <div class="space-y-4">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Message Content</label>
                            <textarea wire:model="bulkNotificationMessage" rows="5" class="w-full bg-white border border-slate-200 text-slate-900 text-sm font-bold rounded-lg focus:ring-2 focus:ring-indigo-600 p-5 outline-none" placeholder="Type your message here..."></textarea>
                            @error('bulkNotificationMessage') <span class="text-[10px] text-rose-500 font-bold uppercase tracking-widest ml-4 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex flex-col sm:flex-row-reverse gap-4 pt-10 border-t border-slate-50">
                            <button type="submit" class="btn-lux px-10 py-4 group">
                                <div class="flex items-center gap-3">
                                    <span>Send Message</span>
                                    <span class="material-symbols-outlined text-lg">send</span>
                                </div>
                            </button>
                            <button type="button" wire:click="$set('showBulkNotificationModal', false)" class="px-8 py-4 bg-white border border-slate-100 text-slate-400 hover:text-rose-600 rounded-lg transition-all font-bold text-xs uppercase tracking-widest">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <!-- Notify Later Modal -->
        @if($isNotifyingLater)
            <div class="fixed inset-0 z-[100] flex items-center justify-center p-6 sm:p-0 overflow-y-auto" role="dialog" aria-modal="true">
                <div class="fixed inset-0 bg-slate-900/60 transition-opacity" aria-hidden="true" wire:click="$set('isNotifyingLater', false)"></div>

                <div class="relative w-full max-w-2xl bg-white rounded-lg shadow-xl border border-slate-100 overflow-hidden">
                    <div class="p-10 md:p-12 border-b border-slate-50">
                        <div class="flex justify-between items-start">
                            <div class="space-y-2">
                                <h3 class="text-2xl font-serif font-bold text-slate-900 leading-tight">Join Waitlist</h3>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Enter your details to be notified about this event.</p>
                            </div>
                            <button wire:click="$set('isNotifyingLater', false)" class="w-12 h-12 rounded-lg bg-slate-50 hover:bg-indigo-50 flex items-center justify-center text-slate-400 hover:text-indigo-600 transition-all">
                                <span class="material-symbols-outlined text-2xl font-bold">close</span>
                            </button>
                        </div>
                    </div>

                    <form wire:submit.prevent="saveNotificationRequest" class="p-10 md:p-12 space-y-10">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Your Name</label>
                                <input type="text" wire:model="notifyName" class="w-full bg-white border border-slate-200 text-slate-900 text-sm font-bold rounded-lg focus:ring-2 focus:ring-indigo-600 p-4 outline-none" placeholder="e.g. Alex Alex">
                                @error('notifyName') <span class="text-[10px] text-rose-500 font-bold uppercase tracking-widest ml-4 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-3">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Email Address</label>
                                <input type="email" wire:model="notifyEmail" class="w-full bg-white border border-slate-200 text-slate-900 text-sm font-bold rounded-lg focus:ring-2 focus:ring-indigo-600 p-4 outline-none" placeholder="alex@example.com">
                                @error('notifyEmail') <span class="text-[10px] text-rose-500 font-bold uppercase tracking-widest ml-4 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row-reverse gap-4 pt-10 border-t border-slate-50">
                            <button type="submit" class="btn-lux px-10 py-4">
                                Join Now
                            </button>
                            <button type="button" wire:click="$set('isNotifyingLater', false)" class="px-8 py-4 bg-white border border-slate-100 text-slate-400 hover:text-slate-600 rounded-lg transition-all font-bold text-xs uppercase tracking-widest">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <!-- Edit Event Modal -->
        @if($isEditingEvent)
            <div class="fixed inset-0 z-[100] flex items-center justify-center p-6 sm:p-0 overflow-y-auto" role="dialog" aria-modal="true">
                <div class="fixed inset-0 bg-slate-900/60 transition-opacity" aria-hidden="true" wire:click="$set('isEditingEvent', false)"></div>

                <div class="relative w-full max-w-5xl bg-white rounded-lg shadow-xl border border-slate-100 overflow-hidden">
                    <div class="p-10 md:p-12 border-b border-slate-50">
                        <div class="flex justify-between items-start">
                            <div class="space-y-2">
                                <h3 class="text-3xl font-serif font-bold text-slate-900 leading-tight">Edit Event</h3>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Update your event details here.</p>
                            </div>
                            <button wire:click="$set('isEditingEvent', false)" class="w-12 h-12 rounded-lg bg-slate-50 hover:bg-indigo-50 flex items-center justify-center text-slate-400 hover:text-indigo-600 transition-all">
                                <span class="material-symbols-outlined text-2xl font-bold">close</span>
                            </button>
                        </div>
                    </div>

                    <form wire:submit.prevent="updateEvent" class="p-10 md:p-12 space-y-10">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Event Title</label>
                                <input type="text" wire:model="editTitle" class="w-full bg-white border border-slate-200 text-slate-900 text-base font-serif font-bold rounded-lg focus:ring-2 focus:ring-indigo-600 p-5 transition-all outline-none">
                            </div>

                            <div class="space-y-3">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Event Type</label>
                                <select wire:model="editType" class="w-full bg-white border border-slate-200 text-slate-900 text-sm font-bold rounded-lg focus:ring-2 focus:ring-indigo-600 p-5 transition-all outline-none">
                                    <option value="conference">Conference</option>
                                    <option value="workshop">Workshop</option>
                                    <option value="seminar">Seminar</option>
                                    <option value="networking">Networking</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <div class="space-y-3">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Start Date & Time</label>
                                <input type="datetime-local" wire:model="editStartDate" class="w-full bg-white border border-slate-200 text-slate-900 font-bold rounded-lg focus:ring-2 focus:ring-indigo-600 p-5 outline-none">
                            </div>

                            <div class="space-y-3">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">End Date & Time</label>
                                <input type="datetime-local" wire:model="editEndDate" class="w-full bg-white border border-slate-200 text-slate-900 font-bold rounded-lg focus:ring-2 focus:ring-indigo-600 p-5 outline-none">
                            </div>
                        </div>

                        <div class="space-y-4">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Description</label>
                            <textarea wire:model="editDescription" rows="5" class="w-full bg-white border border-slate-200 text-slate-900 text-base font-serif font-bold rounded-lg focus:ring-2 focus:ring-indigo-600 p-6 transition-all outline-none"></textarea>
                        </div>

                        <div class="space-y-4">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Banner Image</label>
                            <div class="relative h-48 bg-slate-50 rounded-lg border-2 border-dashed border-slate-200 flex flex-col items-center justify-center overflow-hidden transition-all hover:bg-indigo-50/20">
                                <input type="file" wire:model="editBanner" class="absolute inset-0 opacity-0 cursor-pointer z-10">
                                <div class="flex flex-col items-center gap-3 text-slate-400">
                                    <span class="material-symbols-outlined text-3xl font-bold">image</span>
                                    <span class="text-[11px] font-bold uppercase tracking-widest">Upload New Banner</span>
                                </div>
                                @if($editBanner)
                                    <div class="absolute inset-0 bg-indigo-600/5 flex items-center justify-center">
                                        <p class="text-[10px] font-bold text-emerald-600 bg-white px-5 py-2 rounded-full border border-emerald-100">Image Selected</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row-reverse gap-4 pt-10 border-t border-slate-50">
                            <button type="submit" class="px-12 py-4 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold uppercase tracking-wider rounded-lg shadow-md transition-all">
                                Save Changes
                            </button>
                            <button type="button" wire:click="$set('isEditingEvent', false)" class="px-10 py-4 bg-white border border-slate-100 text-slate-400 hover:text-slate-600 rounded-lg transition-all font-bold text-xs uppercase tracking-widest">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    @push('scripts')
    <script>
        let map, marker, autocomplete;

        function initMap() {
            const myLatLng = { 
                lat: {{ $event->latitude ?? -1.2921 }}, 
                lng: {{ $event->longitude ?? 36.8219 }} 
            };
            
            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 15,
                center: myLatLng,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: false,
                styles: [
                    {
                        "featureType": "administrative",
                        "elementType": "labels.text.fill",
                        "stylers": [{"color": "#444444"}]
                    },
                    {
                        "featureType": "landscape",
                        "elementType": "all",
                        "stylers": [{"color": "#f2f2f2"}]
                    },
                    {
                        "featureType": "poi",
                        "elementType": "all",
                        "stylers": [{"visibility": "off"}]
                    },
                    {
                        "featureType": "road",
                        "elementType": "all",
                        "stylers": [{"saturation": -100}, {"lightness": 45}]
                    },
                    {
                        "featureType": "road.highway",
                        "elementType": "all",
                        "stylers": [{"visibility": "simplified"}]
                    },
                    {
                        "featureType": "road.arterial",
                        "elementType": "labels.icon",
                        "stylers": [{"visibility": "off"}]
                    },
                    {
                        "featureType": "transit",
                        "elementType": "all",
                        "stylers": [{"visibility": "off"}]
                    },
                    {
                        "featureType": "water",
                        "elementType": "all",
                        "stylers": [{"color": "#c8d7d4"}, {"visibility": "on"}]
                    }
                ]
            });

            marker = new google.maps.Marker({
                position: myLatLng,
                map,
                draggable: @json($this->hasPermission('edit_event')),
                animation: google.maps.Animation.DROP,
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

        async function shareEvent(title, url, description) {
            const shareData = {
                title: title,
                text: description + 'Check out this event at:',
                url: url
            };

            try {
                if (navigator.share && navigator.canShare(shareData)) {
                    await navigator.share(shareData);
                } else {
                    navigator.clipboard.writeText(url).then(() => {
                        alert('Event link copied to clipboard! You can now share it manually.');
                    }).catch(err => {
                        console.error('Copy failed:', err);
                    });
                }
            } catch (err) {
                if (err.name !== 'AbortError') {
                    console.error('Share failed:', err);
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
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places&callback=initMap" async defer></script>
    @endpush

    @push('styles')
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.02);
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(67, 56, 202, 0.1);
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(67, 56, 202, 0.2);
        }
    </style>
    @endpush
</div>
