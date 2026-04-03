<div class="space-y-12">
    <!-- Header with Toggles -->
    <div class="bg-white rounded-[3rem] p-8 md:p-10 border border-slate-100 shadow-lux">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-10">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-10">
                <div class="flex items-center gap-6">
                    <div class="w-14 h-14 rounded-[1.5rem] bg-indigo-50 flex items-center justify-center text-indigo-600 shadow-sm">
                        <span class="material-symbols-outlined font-bold text-2xl">folder_shared</span>
                    </div>
                    <div>
                        <h3 class="text-3xl font-display font-extrabold text-slate-900 uppercase tracking-tight leading-none mb-2">Digital Vault</h3>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.3em] opacity-80 italic">Orchestrated asset management.</p>
                    </div>
                </div>
                
                <div class="flex bg-slate-50 p-2 rounded-[2rem] border border-slate-100 shadow-inner">
                    @if($this->hasPermission('manage_files') || $userRole !== 'guest')
                        <button wire:click="setFolder('team')" class="px-8 py-3 rounded-[1.5rem] text-[11px] font-bold uppercase tracking-widest transition-all {{ $activeFolder === 'team' ? 'bg-indigo-600 text-white shadow-xl shadow-indigo-100' : 'text-slate-400 hover:text-indigo-600' }}">
                            Team
                        </button>
                    @endif
                    <button wire:click="setFolder('shared')" class="px-8 py-3 rounded-[1.5rem] text-[11px] font-bold uppercase tracking-widest transition-all {{ $activeFolder === 'shared' ? 'bg-indigo-600 text-white shadow-xl shadow-indigo-100' : 'text-slate-400 hover:text-indigo-600' }}">
                        Shared
                    </button>
                </div>
            </div>

            @if($this->hasPermission('manage_files'))
            <div class="flex flex-wrap items-center gap-6">
                <div class="flex items-center gap-6 px-6 py-4 bg-slate-50 rounded-[2rem] border border-slate-100 shadow-inner">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Guest Uploads</span>
                    <button wire:click="toggleGuestUploads" class="relative inline-flex h-7 w-12 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-300 ease-in-out focus:outline-none {{ $allowGuestUploads ? 'bg-emerald-500' : 'bg-slate-200' }}">
                        <span class="pointer-events-none inline-block h-6 w-6 transform rounded-full bg-white shadow-lux transition duration-300 ease-in-out {{ $allowGuestUploads ? 'translate-x-5' : 'translate-x-0' }}"></span>
                    </button>
                </div>
                <div class="flex items-center gap-6 px-6 py-4 bg-slate-50 rounded-[2rem] border border-slate-100 shadow-inner">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Guest Vision</span>
                    <button wire:click="toggleGuestView" class="relative inline-flex h-7 w-12 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-300 ease-in-out focus:outline-none {{ $allowGuestViewShared ? 'bg-indigo-600' : 'bg-slate-200' }}">
                        <span class="pointer-events-none inline-block h-6 w-6 transform rounded-full bg-white shadow-lux transition duration-300 ease-in-out {{ $allowGuestViewShared ? 'translate-x-5' : 'translate-x-0' }}"></span>
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
        
        <label class="flex flex-col items-center justify-center w-full h-64 bg-slate-50 border-2 border-dashed border-slate-200 hover:border-indigo-200 rounded-[4rem] cursor-pointer hover:bg-indigo-50/30 transition-all duration-700 overflow-hidden relative shadow-inner">
            <div class="flex flex-col items-center justify-center pt-8 pb-10 relative z-10 transition-all duration-700 group-hover:-translate-y-2">
                <div class="w-20 h-20 rounded-[2.5rem] bg-white shadow-lux flex items-center justify-center text-indigo-600 mb-6 group-hover:scale-110 transition-transform duration-700">
                    <span class="material-symbols-outlined text-4xl font-bold">cloud_upload</span>
                </div>
                <p class="mb-3 text-xl text-slate-900 font-bold uppercase tracking-tight italic opacity-90">Ingest assets to <span class="text-indigo-600">{{ $activeFolder }}</span> Vault</p>
                <p class="text-[11px] text-slate-400 font-bold uppercase tracking-[0.3em] opacity-60">Tap to deploy or drag & drop (LIMIT 10MB)</p>
            </div>
            <input type="file" wire:model="uploads" multiple class="hidden" accept="image/*" />
            
            <!-- Progress Bar Overlay -->
            <div x-show="isUploading" class="absolute bottom-0 left-0 h-3 bg-indigo-600 transition-all duration-300 shadow-[0_0_30px_rgba(67,56,202,0.4)]" :style="'width: ' + progress + '%'"></div>
            <div x-show="isUploading" class="absolute inset-0 bg-indigo-900/5 backdrop-blur-[2px] flex items-center justify-center z-20">
                <span class="text-indigo-600 font-bold italic text-3xl animate-pulse" x-text="progress + '%'"></span>
            </div>
        </label>
    </div>
    @endif

    @if(session()->has('media_message'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-600 p-6 rounded-[2rem] text-[11px] font-bold uppercase tracking-[0.3em] animate-in slide-in-from-left-6">
            {{ session('media_message') }}
        </div>
    @endif

    <!-- Media Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-10">
        @forelse($mediaItems as $item)
            <div class="group relative aspect-square rounded-[3rem] overflow-hidden bg-white border border-slate-50 shadow-lux hover:shadow-indigo-100 transition-all duration-700 hover:-translate-y-3">
                @if(str_contains($item->mime_type, 'image'))
                    <img src="{{ Storage::url($item->file_path) }}" class="w-full h-full object-cover transition-all duration-1000 group-hover:scale-110 grayscale-[0.2] group-hover:grayscale-0" />
                @else
                    <div class="w-full h-full flex flex-col items-center justify-center bg-slate-50">
                        <span class="material-symbols-outlined text-indigo-400 text-6xl mb-4 group-hover:scale-110 transition-transform">description</span>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.3em] bg-white px-4 py-1.5 rounded-full shadow-sm">{{ explode('.', $item->file_name)[1] ?? 'FILE' }}</span>
                    </div>
                @endif

                <!-- Status Badge -->
                <div class="absolute top-6 right-6 z-10 transition-all duration-500 group-hover:scale-95 group-hover: -translate-x-1 group-hover:translate-y-1">
                    <span class="px-4 py-2 rounded-2xl text-[9px] font-bold uppercase tracking-[0.2em] backdrop-blur-2xl border border-white/20 shadow-xl
                        {{ $item->visibility === 'private' ? 'bg-slate-900/80 text-slate-300' : '' }}
                        {{ $item->visibility === 'public' ? 'bg-emerald-500/90 text-white' : '' }}
                        {{ $item->visibility === 'restricted' ? 'bg-indigo-600/90 text-white' : '' }}
                    ">
                        {{ $item->visibility }}
                    </span>
                </div>

                <!-- Premium Overlay -->
                <div class="absolute inset-0 bg-gradient-to-t from-indigo-950/95 via-indigo-900/40 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-700 flex flex-col justify-end p-8">
                    <p class="text-[11px] font-bold text-white italic truncate mb-3 uppercase tracking-tighter">{{ $item->file_name }}</p>
                    
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-8 h-8 rounded-xl bg-indigo-500/30 border border-white/20 flex items-center justify-center text-[10px] font-bold text-white italic">
                            {{ substr($item->uploader->name, 0, 1) }}
                        </div>
                        <span class="text-[10px] text-indigo-100 font-bold uppercase tracking-widest truncate italic opacity-80">{{ $item->uploader->name }}</span>
                    </div>

                    <div class="flex items-center justify-between pointer-events-auto">
                        <div class="flex gap-3">
                            @if($item->user_id === auth()->id() || $this->hasPermission('manage_files'))
                                <button wire:click="deleteMedia({{ $item->id }})" class="w-12 h-12 rounded-[1.25rem] bg-rose-500/80 hover:bg-rose-500 text-white transition-all backdrop-blur-md flex items-center justify-center shadow-lg hover:rotate-3">
                                    <span class="material-symbols-outlined text-xl">delete</span>
                                </button>
                                @if($activeFolder === 'team')
                                    <button wire:click="makePublic({{ $item->id }})" class="w-12 h-12 rounded-[1.25rem] bg-emerald-500/80 hover:bg-emerald-500 text-white transition-all backdrop-blur-md flex items-center justify-center shadow-lg hover:rotate-3">
                                        <span class="material-symbols-outlined text-xl">share</span>
                                    </button>
                                @endif
                                <button wire:click="startRestricting({{ $item->id }})" class="w-12 h-12 rounded-[1.25rem] bg-indigo-500/80 hover:bg-indigo-500 text-white transition-all backdrop-blur-md flex items-center justify-center shadow-lg hover:rotate-3">
                                    <span class="material-symbols-outlined text-xl">lock_open</span>
                                </button>
                            @endif
                        </div>
                        <a href="{{ Storage::url($item->file_path) }}" target="_blank" class="w-12 h-12 rounded-[1.25rem] bg-white/20 hover:bg-white/40 text-white transition-all backdrop-blur-md flex items-center justify-center shadow-lg">
                            <span class="material-symbols-outlined text-xl">download</span>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-32 flex flex-col items-center justify-center bg-slate-50 rounded-[4rem] border-2 border-dashed border-slate-200 opacity-80 shadow-inner">
                <div class="w-24 h-24 rounded-[2.5rem] bg-white shadow-lux flex items-center justify-center text-slate-200 mb-8">
                    <span class="material-symbols-outlined text-6xl">folder_off</span>
                </div>
                <p class="text-[13px] text-slate-400 font-bold uppercase tracking-[0.4em] italic mb-2">Vault is presently void.</p>
                @if($activeFolder === 'shared' && !$allowGuestViewShared && $userRole === 'guest')
                    <p class="text-[10px] text-rose-500 font-bold uppercase tracking-[0.2em] mt-6 italic bg-rose-50 px-6 py-2 rounded-full border border-rose-100 animate-pulse">Orchestrator has restricted visual access.</p>
                @endif
            </div>
        @endforelse
    </div>

    <!-- Access Control Modal -->
    @if($selectingAccessFor)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-8 overflow-y-auto">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xl transition-opacity" wire:click="saveAccess"></div>
        <div class="relative w-full max-w-2xl bg-white rounded-[4rem] shadow-lux border border-slate-100 overflow-hidden animate-in zoom-in-95 duration-700">
            <div class="p-12 md:p-14 border-b border-slate-50 flex justify-between items-start">
                <div class="space-y-2">
                    <h4 class="text-3xl font-display font-extrabold text-slate-900 uppercase tracking-tight">Access Control</h4>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.3em] italic opacity-80">Precision clearance distribution.</p>
                </div>
                <button wire:click="saveAccess" class="w-14 h-14 rounded-2xl bg-slate-50 hover:bg-indigo-50 text-slate-400 hover:text-indigo-600 flex items-center justify-center transition-all hover:rotate-90">
                    <span class="material-symbols-outlined text-2xl font-bold">close</span>
                </button>
            </div>
            
            <div class="p-12 md:p-14 max-h-[60vh] overflow-y-auto custom-scrollbar space-y-12">
                <!-- Organizers -->
                <div>
                    <h5 class="text-[11px] font-bold text-indigo-600 uppercase tracking-[0.4em] mb-8 flex items-center gap-4">
                        <span class="w-2.5 h-2.5 rounded-full bg-indigo-600 shadow-[0_0_10px_rgba(67,56,202,0.3)]"></span>
                        Executive Vanguard
                    </h5>
                    <div class="space-y-4">
                        @foreach($allOrganizers as $organizer)
                            <label class="flex items-center justify-between p-6 rounded-[2rem] bg-slate-50 hover:bg-indigo-50/50 transition-all cursor-pointer border border-transparent hover:border-indigo-100 group shadow-sm hover:shadow-indigo-50">
                                <div class="flex items-center gap-5">
                                    <div class="w-12 h-12 rounded-[1.25rem] bg-indigo-600 inline-flex items-center justify-center text-white font-bold text-sm uppercase italic transition-all group-hover:scale-110 shadow-lg shadow-indigo-100">
                                        {{ substr($organizer->name, 0, 1) }}
                                    </div>
                                    <span class="text-lg font-bold text-slate-900 italic tracking-tight uppercase leading-none">{{ $organizer->name }}</span>
                                </div>
                                <div class="relative">
                                    <input type="checkbox" wire:model="selectedGuestIds" value="{{ $organizer->id }}" class="w-7 h-7 rounded-lg border-2 border-slate-200 text-indigo-600 focus:ring-0 focus:ring-offset-0 checked:bg-indigo-600 checked:border-indigo-600 transition-all cursor-pointer" />
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Guests -->
                <div>
                    <h5 class="text-[11px] font-bold text-emerald-600 uppercase tracking-[0.4em] mb-8 flex items-center gap-4">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.3)]"></span>
                        Delegated Assembly
                    </h5>
                    <div class="space-y-4">
                        @foreach($allGuests as $guest)
                            @if(!$allOrganizers->contains('id', $guest->id))
                                <label class="flex items-center justify-between p-6 rounded-[2rem] bg-slate-50 hover:bg-emerald-50/50 transition-all cursor-pointer border border-transparent hover:border-emerald-100 group shadow-sm hover:shadow-emerald-50">
                                    <div class="flex items-center gap-5">
                                        <div class="w-12 h-12 rounded-[1.25rem] bg-emerald-500 inline-flex items-center justify-center text-white font-bold text-sm uppercase italic transition-all group-hover:scale-110 shadow-lg shadow-emerald-100">
                                            {{ substr($guest->name, 0, 1) }}
                                        </div>
                                        <span class="text-lg font-bold text-slate-900 italic tracking-tight uppercase leading-none">{{ $guest->name }}</span>
                                    </div>
                                    <div class="relative">
                                        <input type="checkbox" wire:model="selectedGuestIds" value="{{ $guest->id }}" class="w-7 h-7 rounded-lg border-2 border-slate-200 text-emerald-600 focus:ring-0 focus:ring-offset-0 checked:bg-emerald-500 checked:border-emerald-500 transition-all cursor-pointer" />
                                    </div>
                                </label>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="p-12 md:p-14 bg-slate-50/50 flex justify-end gap-6 border-t border-slate-50">
                <button wire:click="saveAccess" class="btn-lux px-16 py-5 shadow-indigo-100 bg-indigo-600 text-white rounded-2xl font-bold uppercase tracking-widest text-[11px]">
                    Authenticate Clearances
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
