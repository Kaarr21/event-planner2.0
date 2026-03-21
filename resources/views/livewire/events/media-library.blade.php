<div class="space-y-10">
    <!-- Header with Toggles -->
    <div class="glass-card dark:glass-card-dark p-6 md:p-8 rounded-[2.5rem] border-none shadow-3xl">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-brand-orange/10 flex items-center justify-center text-brand-orange">
                        <span class="material-symbols-outlined font-bold text-2xl">folder_shared</span>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter italic leading-none mb-1">Vault</h3>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest opacity-60">Secure asset management.</p>
                    </div>
                </div>
                
                <div class="flex bg-white/5 p-1.5 rounded-[1.5rem] border border-white/5">
                    @if($this->hasPermission('manage_files') || $userRole !== 'guest')
                        <button wire:click="setFolder('team')" class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] transition-all {{ $activeFolder === 'team' ? 'bg-brand-orange text-white shadow-xl shadow-brand-orange/20' : 'text-gray-500 hover:text-gray-300' }}">
                            Team
                        </button>
                    @endif
                    <button wire:click="setFolder('shared')" class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] transition-all {{ $activeFolder === 'shared' ? 'bg-brand-orange text-white shadow-xl shadow-brand-orange/20' : 'text-gray-500 hover:text-gray-300' }}">
                        Shared
                    </button>
                </div>
            </div>

            @if($this->hasPermission('manage_files'))
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex items-center gap-4 px-5 py-3 bg-white/5 rounded-2xl border border-white/5">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Guest Uploads</span>
                    <button wire:click="toggleGuestUploads" class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-300 ease-in-out focus:outline-none {{ $allowGuestUploads ? 'bg-brand-teal' : 'bg-white/10' }}">
                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-xl ring-0 transition duration-300 ease-in-out {{ $allowGuestUploads ? 'translate-x-5' : 'translate-x-0' }}"></span>
                    </button>
                </div>
                <div class="flex items-center gap-4 px-5 py-3 bg-white/5 rounded-2xl border border-white/5">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Guest Vision</span>
                    <button wire:click="toggleGuestView" class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-300 ease-in-out focus:outline-none {{ $allowGuestViewShared ? 'bg-brand-orange' : 'bg-white/10' }}">
                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-xl ring-0 transition duration-300 ease-in-out {{ $allowGuestViewShared ? 'translate-x-5' : 'translate-x-0' }}"></span>
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Upload Zone -->
    @if(($activeFolder === 'shared' && ($allowGuestUploads || $this->hasPermission('manage_files'))) || ($activeFolder === 'team' && $this->hasPermission('manage_files')))
    <div x-data="{ isUploading: false, progress: 0 }" 
         x-on:livewire-upload-start="isUploading = true"
         x-on:livewire-upload-finish="isUploading = false"
         x-on:livewire-upload-error="isUploading = false"
         x-on:livewire-upload-progress="progress = $event.detail.progress"
         class="relative group">
        
        <label class="flex flex-col items-center justify-center w-full h-48 glass-card dark:glass-card-dark border-2 border-dashed border-white/10 hover:border-brand-orange/30 rounded-[2.5rem] cursor-pointer bg-white/5 hover:bg-brand-orange/5 transition-all duration-500 overflow-hidden relative">
            <div class="flex flex-col items-center justify-center pt-8 pb-10 relative z-10 transition-transform duration-500 group-hover:-translate-y-1">
                <div class="w-16 h-16 rounded-full bg-brand-orange/10 flex items-center justify-center text-brand-orange mb-4 group-hover:scale-110 transition-transform duration-500">
                    <span class="material-symbols-outlined text-3xl font-black">cloud_upload</span>
                </div>
                <p class="mb-2 text-base text-gray-900 dark:text-white font-black uppercase tracking-tighter italic">Upload to {{ $activeFolder }} Folder</p>
                <p class="text-[10px] text-gray-500 font-black uppercase tracking-[0.2em] opacity-60">Tap to deploy or drag & drop (MAX 10MB)</p>
            </div>
            <input type="file" wire:model="uploads" multiple class="hidden" accept="image/*" />
            
            <!-- Progress Bar Overlay -->
            <div x-show="isUploading" class="absolute bottom-0 left-0 h-2 bg-brand-orange transition-all duration-300 shadow-[0_0_20px_rgba(242,139,36,0.5)]" :style="'width: ' + progress + '%'"></div>
            <div x-show="isUploading" class="absolute inset-0 bg-brand-orange/5 backdrop-blur-[2px] flex items-center justify-center z-20">
                <span class="text-brand-orange font-black italic text-2xl animate-pulse" x-text="progress + '%'"></span>
            </div>
        </label>
    </div>
    @endif

    @if(session()->has('media_message'))
        <div class="bg-brand-teal/10 border border-brand-teal/20 text-brand-teal p-5 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] animate-in slide-in-from-left-4">
            {{ session('media_message') }}
        </div>
    @endif

    <!-- Media Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
        @forelse($mediaItems as $item)
            <div class="group relative aspect-square rounded-[2rem] overflow-hidden glass-card border-none shadow-xl hover:shadow-brand-orange/10 transition-all duration-700 hover:-translate-y-2">
                @if(str_contains($item->mime_type, 'image'))
                    <img src="{{ Storage::url($item->file_path) }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                @else
                    <div class="w-full h-full flex flex-col items-center justify-center bg-white/5">
                        <span class="material-symbols-outlined text-brand-orange text-5xl mb-3">description</span>
                        <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">{{ explode('.', $item->file_name)[1] ?? 'FILE' }}</span>
                    </div>
                @endif

                <!-- Status Badge -->
                <div class="absolute top-4 right-4 z-10 transition-transform duration-500 group-hover:scale-90">
                    <span class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest backdrop-blur-xl border border-white/20 shadow-2xl
                        {{ $item->visibility === 'private' ? 'bg-gray-900/80 text-gray-400' : '' }}
                        {{ $item->visibility === 'public' ? 'bg-brand-teal text-white' : '' }}
                        {{ $item->visibility === 'restricted' ? 'bg-brand-orange text-white' : '' }}
                    ">
                        {{ $item->visibility }}
                    </span>
                </div>

                <!-- Premium Overlay -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 flex flex-col justify-end p-6">
                    <p class="text-[10px] font-black text-white italic truncate mb-2 uppercase tracking-tighter">{{ $item->file_name }}</p>
                    
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-6 h-6 rounded-lg bg-brand-orange/20 border border-brand-orange/30 flex items-center justify-center text-[10px] font-black text-brand-orange italic">
                            {{ substr($item->uploader->name, 0, 1) }}
                        </div>
                        <span class="text-[9px] text-gray-300 font-black uppercase tracking-widest truncate italic">{{ $item->uploader->name }}</span>
                    </div>

                    <div class="flex items-center justify-between pointer-events-auto">
                        <div class="flex gap-2">
                            @if($item->user_id === auth()->id() || $this->hasPermission('manage_files'))
                                <button wire:click="deleteMedia({{ $item->id }})" class="w-10 h-10 rounded-xl bg-brand-red/80 hover:bg-brand-red text-white transition-all backdrop-blur-md flex items-center justify-center">
                                    <span class="material-symbols-outlined text-lg">delete</span>
                                </button>
                                @if($activeFolder === 'team')
                                    <button wire:click="makePublic({{ $item->id }})" class="w-10 h-10 rounded-xl bg-brand-teal/80 hover:bg-brand-teal text-white transition-all backdrop-blur-md flex items-center justify-center">
                                        <span class="material-symbols-outlined text-lg">share</span>
                                    </button>
                                @endif
                                <button wire:click="startRestricting({{ $item->id }})" class="w-10 h-10 rounded-xl bg-brand-orange/80 hover:bg-brand-orange text-white transition-all backdrop-blur-md flex items-center justify-center">
                                    <span class="material-symbols-outlined text-lg">person_add</span>
                                </button>
                            @endif
                        </div>
                        <a href="{{ Storage::url($item->file_path) }}" target="_blank" class="w-10 h-10 rounded-xl bg-white/20 hover:bg-white/40 text-white transition-all backdrop-blur-md flex items-center justify-center">
                            <span class="material-symbols-outlined text-lg">download</span>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-24 flex flex-col items-center justify-center glass-card dark:glass-card-dark rounded-[3rem] border border-dashed border-white/10 opacity-60">
                <span class="material-symbols-outlined text-brand-teal text-7xl mb-6 opacity-20">folder_open</span>
                <p class="text-sm text-gray-500 font-black uppercase tracking-[0.3em] italic">Vault is empty.</p>
                @if($activeFolder === 'shared' && !$allowGuestViewShared && $userRole === 'guest')
                    <p class="text-[10px] text-brand-red font-black uppercase tracking-widest mt-4">Host has restricted visual access.</p>
                @endif
            </div>
        @endforelse
    </div>

    <!-- Access Control Modal -->
    @if($selectingAccessFor)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-6 backdrop-blur-xl">
        <div class="absolute inset-0 bg-black/40" wire:click="saveAccess"></div>
        <div class="relative w-full max-w-xl glass-card dark:glass-card-dark rounded-[3.5rem] border-none shadow-4xl overflow-hidden animate-in zoom-in-95 duration-500">
            <div class="p-10 border-b border-white/5 flex justify-between items-start">
                <div>
                    <h4 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tighter italic leading-none mb-2">Access Control</h4>
                    <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest italic opacity-60">Curation of file visibility per talent.</p>
                </div>
                <button wire:click="saveAccess" class="w-12 h-12 rounded-2xl bg-white/5 hover:bg-white/10 text-gray-400 flex items-center justify-center transition-all">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            <div class="p-10 max-h-[50vh] overflow-y-auto space-y-10 custom-scrollbar">
                <!-- Organizers -->
                <div>
                    <h5 class="text-[10px] font-black text-brand-orange uppercase tracking-[.3em] mb-6 flex items-center gap-3">
                        <span class="w-2 h-2 rounded-full bg-brand-orange"></span>
                        Executive Team
                    </h5>
                    <div class="space-y-3">
                        @foreach($allOrganizers as $organizer)
                            <label class="flex items-center justify-between p-5 rounded-2xl bg-white/5 hover:bg-white/10 transition-all cursor-pointer border border-transparent hover:border-brand-orange/20 group">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-brand-orange inline-flex items-center justify-center text-white font-black text-xs uppercase italic transition-transform group-hover:scale-110">
                                        {{ substr($organizer->name, 0, 1) }}
                                    </div>
                                    <span class="text-base font-black text-gray-900 dark:text-white italic tracking-tight uppercase leading-none">{{ $organizer->name }}</span>
                                </div>
                                <input type="checkbox" wire:model="selectedGuestIds" value="{{ $organizer->id }}" class="w-6 h-6 rounded-lg border-2 border-brand-orange/20 text-brand-orange focus:ring-0 checked:bg-brand-orange checked:border-brand-orange transition-all" />
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Guests -->
                <div>
                    <h5 class="text-[10px] font-black text-brand-teal uppercase tracking-[.3em] mb-6 flex items-center gap-3">
                        <span class="w-2 h-2 rounded-full bg-brand-teal"></span>
                        Selected Talents
                    </h5>
                    <div class="space-y-3">
                        @foreach($allGuests as $guest)
                            @if(!$allOrganizers->contains('id', $guest->id))
                                <label class="flex items-center justify-between p-5 rounded-2xl bg-white/5 hover:bg-white/10 transition-all cursor-pointer border border-transparent hover:border-brand-teal/20 group">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-brand-teal inline-flex items-center justify-center text-white font-black text-xs uppercase italic transition-transform group-hover:scale-110">
                                            {{ substr($guest->name, 0, 1) }}
                                        </div>
                                        <span class="text-base font-black text-gray-900 dark:text-white italic tracking-tight uppercase leading-none">{{ $guest->name }}</span>
                                    </div>
                                    <input type="checkbox" wire:model="selectedGuestIds" value="{{ $guest->id }}" class="w-6 h-6 rounded-lg border-2 border-brand-teal/20 text-brand-teal focus:ring-0 checked:bg-brand-teal checked:border-brand-teal transition-all" />
                                </label>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="p-10 bg-white/5 flex justify-end gap-4">
                <button wire:click="saveAccess" class="btn-brand pamoja-gradient px-12 py-4">
                    Apply Clearances
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
