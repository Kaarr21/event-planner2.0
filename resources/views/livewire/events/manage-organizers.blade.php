<div class="glass-card dark:glass-card-dark overflow-hidden rounded-[2.5rem] border-none shadow-3xl">
    <div class="p-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tighter italic leading-none mb-1">Executive Board</h3>
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest opacity-60">Grant management clearances.</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-brand-orange/10 flex items-center justify-center text-brand-orange">
                <span class="material-symbols-outlined font-black">shield_person</span>
            </div>
        </div>

        @if (session()->has('organizer_message'))
            <div class="mb-8 bg-brand-teal/10 border border-brand-teal/20 text-brand-teal p-5 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] animate-in slide-in-from-left-4">
                {{ session('organizer_message') }}
            </div>
        @endif

        @if(auth()->id() === $event->user_id)
        <!-- Add Organizer Form -->
        <form wire:submit.prevent="addOrganizer" class="mb-12 space-y-6 bg-white/5 p-8 rounded-[2rem] border border-white/5 group">
            <div class="space-y-4">
                <label for="email" class="block text-[10px] font-black text-gray-400 uppercase tracking-[.3em] ml-2">Appoint by Email</label>
                <div class="flex flex-col sm:flex-row gap-4">
                    <input type="email" wire:model="email" placeholder="talent@onyx.com" class="flex-1 bg-white dark:bg-gray-800/50 border-0 ring-1 ring-white/10 text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-brand-orange p-5 transition-all font-bold italic shadow-inner">
                    <button type="submit" class="pamoja-gradient text-white px-8 py-5 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-brand-orange/20 hover:scale-105 active:scale-95 transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-base">person_add</span>
                        Appoint
                    </button>
                </div>
                @error('email') <span class="text-[10px] text-brand-red font-black uppercase tracking-widest ml-2">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-6">
                @foreach($availablePermissions as $key => $label)
                    <label class="flex items-center gap-3 p-3 rounded-xl bg-white/5 border border-transparent hover:border-brand-orange/20 transition-all cursor-pointer group/perm">
                        <input type="checkbox" wire:model="selectedPermissions" value="{{ $key }}" class="w-5 h-5 rounded-lg border-2 border-brand-orange/20 text-brand-orange focus:ring-0 checked:bg-brand-orange checked:border-brand-orange transition-all cursor-pointer">
                        <span class="text-[9px] font-black text-gray-500 uppercase tracking-widest group-hover/perm:text-gray-300 transition-colors">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </form>
        @endif

        <!-- Organizers List -->
        <div class="space-y-6">
            @forelse ($organizers as $organizer)
                <div class="flex flex-col p-6 rounded-[2rem] border border-white/5 hover:bg-white/5 transition-all duration-500 group">
                    <div class="flex items-center justify-between gap-6 mb-6">
                        <div class="flex items-center gap-5">
                            <div class="relative">
                                <img src="{{ $organizer->profile_photo_url }}" class="h-14 w-14 rounded-2xl object-cover border-2 border-white/5 shadow-2xl group-hover:scale-105 transition-transform" alt="{{ $organizer->name }}">
                                <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-brand-teal rounded-lg flex items-center justify-center text-[10px] text-white font-black shadow-lg">
                                    <span class="material-symbols-outlined text-xs">verified</span>
                                </div>
                            </div>
                            <div>
                                <span class="text-base font-black text-gray-900 dark:text-white uppercase tracking-tighter italic leading-none block mb-1 group-hover:text-brand-orange transition-colors">{{ $organizer->name }}</span>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest opacity-60">{{ $organizer->email }}</p>
                            </div>
                        </div>
                        @if(auth()->id() === $event->user_id)
                        <button wire:click="removeOrganizer({{ $organizer->id }})" wire:confirm="Are you sure you want to remove this organizer?" class="w-10 h-10 rounded-xl bg-brand-red/10 hover:bg-brand-red text-brand-red hover:text-white transition-all flex items-center justify-center shadow-lg">
                            <span class="material-symbols-outlined text-lg">person_remove</span>
                        </button>
                        @endif
                    </div>
                    
                    <div class="border-t border-white/5 pt-6">
                        <p class="text-[8px] font-black text-gray-500 uppercase tracking-[.3em] mb-4 opacity-60">Permission Clearance</p>
                        @if(auth()->id() === $event->user_id)
                        <div class="flex flex-wrap gap-4">
                            @foreach($availablePermissions as $key => $label)
                                @php
                                    $perms = $organizer->pivot->permissions ?? [];
                                    if (!is_array($perms)) {
                                        $perms = json_decode($perms, true) ?? [];
                                    }
                                @endphp
                                <label class="flex items-center gap-2 px-4 py-2 rounded-xl bg-white/5 border border-transparent hover:border-brand-teal/20 transition-all cursor-pointer group/perm">
                                    <input type="checkbox" 
                                        wire:click="togglePermission({{ $organizer->id }}, '{{ $key }}')"
                                        {{ in_array($key, $perms) ? 'checked' : '' }}
                                        class="w-4 h-4 rounded-md border-2 border-brand-teal/20 text-brand-teal focus:ring-0 checked:bg-brand-teal checked:border-brand-teal transition-all cursor-pointer">
                                    <span class="text-[9px] font-black text-gray-500 uppercase tracking-widest group-hover/perm:text-gray-300 transition-colors">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @else
                        <div class="flex flex-wrap gap-2">
                            @php
                                $perms = $organizer->pivot->permissions ?? [];
                                if (!is_array($perms)) {
                                    $perms = json_decode($perms, true) ?? [];
                                }
                            @endphp
                            @foreach($perms as $perm)
                                <span class="text-[8px] bg-brand-teal/10 text-brand-teal border border-brand-teal/20 px-3 py-1.5 rounded-lg font-black uppercase tracking-widest shadow-sm">
                                    {{ str_replace('_', ' ', $perm) }}
                                </span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-24 px-10 bg-white/5 rounded-[3rem] border-2 border-dashed border-white/5 opacity-40">
                    <span class="material-symbols-outlined text-7xl text-brand-orange mb-6 opacity-20">shield_person</span>
                    <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] italic text-center">No executive appointments detected.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
