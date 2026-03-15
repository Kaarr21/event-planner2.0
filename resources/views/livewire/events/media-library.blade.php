<div class="space-y-6">
    <!-- Header with Toggles -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white/5 dark:bg-white/5 backdrop-blur-md p-4 rounded-2xl border border-white/10 shadow-xl">
        <div class="flex items-center gap-4">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <span class="material-symbols-outlined text-indigo-500">folder_shared</span>
                Event Media Library
            </h3>
            
            <div class="flex bg-gray-200/50 dark:bg-gray-800/50 p-1 rounded-xl">
                @if($this->hasPermission('manage_files') || $userRole !== 'guest')
                    <button wire:click="setFolder('team')" class="px-4 py-1.5 rounded-lg text-xs font-black uppercase tracking-widest transition-all {{ $activeFolder === 'team' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
                        Team Vault
                    </button>
                @endif
                <button wire:click="setFolder('shared')" class="px-4 py-1.5 rounded-lg text-xs font-black uppercase tracking-widest transition-all {{ $activeFolder === 'shared' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
                    Shared Folder
                </button>
            </div>
        </div>

        @if($this->hasPermission('manage_files'))
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2 px-3 py-1.5 bg-white/5 rounded-lg border border-white/5">
                <span class="text-[10px] font-bold text-gray-500 uppercase tracking-tighter">Guest Uploads</span>
                <button wire:click="toggleGuestUploads" class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $allowGuestUploads ? 'bg-indigo-600' : 'bg-gray-700' }}">
                    <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $allowGuestUploads ? 'translate-x-4' : 'translate-x-0' }}"></span>
                </button>
            </div>
            <div class="flex items-center gap-2 px-3 py-1.5 bg-white/5 rounded-lg border border-white/5">
                <span class="text-[10px] font-bold text-gray-500 uppercase tracking-tighter">Guest Vision</span>
                <button wire:click="toggleGuestView" class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $allowGuestViewShared ? 'bg-emerald-500' : 'bg-gray-700' }}">
                    <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $allowGuestViewShared ? 'translate-x-4' : 'translate-x-0' }}"></span>
                </button>
            </div>
        </div>
        @endif
    </div>

    <!-- Upload Zone -->
    @if(($activeFolder === 'shared' && ($allowGuestUploads || $this->hasPermission('manage_files'))) || ($activeFolder === 'team' && $this->hasPermission('manage_files')))
    <div x-data="{ isUploading: false, progress: 0 }" 
         x-on:livewire-upload-start="isUploading = true"
         x-on:livewire-upload-finish="isUploading = false"
         x-on:livewire-upload-error="isUploading = false"
         x-on:livewire-upload-progress="progress = $event.detail.progress"
         class="relative">
        
        <label class="group flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-2xl cursor-pointer bg-gray-50/50 dark:bg-gray-800/20 hover:bg-gray-100 dark:hover:bg-gray-700/30 transition-all overflow-hidden">
            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                <span class="material-symbols-outlined text-indigo-500 text-3xl mb-2 group-hover:scale-110 transition-transform">cloud_upload</span>
                <p class="mb-1 text-sm text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest leading-none">Upload to {{ $activeFolder }} Folder</p>
                <p class="text-[10px] text-gray-400 dark:text-gray-500">Tap to browse or drag and drop images (MAX 10MB)</p>
            </div>
            <input type="file" wire:model="uploads" multiple class="hidden" accept="image/*" />
            
            <!-- Progress Bar -->
            <div x-show="isUploading" class="absolute bottom-0 left-0 h-1 bg-indigo-500 transition-all duration-300" :style="'width: ' + progress + '%'"></div>
        </label>
    </div>
    @endif

    @if(session()->has('media_message'))
        <div class="bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 p-3 rounded-xl text-xs font-bold uppercase tracking-wider">
            {{ session('media_message') }}
        </div>
    @endif

    <!-- Media Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
        @forelse($mediaItems as $item)
            <div class="group relative aspect-square rounded-2xl overflow-hidden bg-gray-200 dark:bg-gray-800 border border-white/5 shadow-lg">
                @if(str_contains($item->mime_type, 'image'))
                    <img src="{{ Storage::url($item->file_path) }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
                @else
                    <div class="w-full h-full flex flex-col items-center justify-center bg-indigo-50 dark:bg-indigo-900/10">
                        <span class="material-symbols-outlined text-indigo-400 text-4xl">description</span>
                        <span class="text-[10px] mt-2 font-bold text-gray-400 uppercase tracking-tighter">{{ explode('.', $item->file_name)[1] ?? 'FILE' }}</span>
                    </div>
                @endif

                <!-- Overlay Info -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-3">
                    <p class="text-[10px] font-bold text-white truncate mb-1">{{ $item->file_name }}</p>
                    <div class="flex items-center gap-1.5 mb-2">
                        <div class="w-4 h-4 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center text-[8px] font-black text-white">
                            {{ substr($item->uploader->name, 0, 1) }}
                        </div>
                        <span class="text-[8px] text-gray-300 font-medium truncate">{{ $item->uploader->name }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex gap-1.5">
                            @if($item->user_id === auth()->id() || $this->hasPermission('manage_files'))
                                <button wire:click="deleteMedia({{ $item->id }})" class="p-1.5 rounded-lg bg-rose-500/80 hover:bg-rose-600 text-white transition-all backdrop-blur-md">
                                    <span class="material-symbols-outlined text-xs">delete</span>
                                </button>
                                @if($activeFolder === 'team')
                                    <button wire:click="makePublic({{ $item->id }})" class="p-1.5 rounded-lg bg-emerald-500/80 hover:bg-emerald-600 text-white transition-all backdrop-blur-md">
                                        <span class="material-symbols-outlined text-xs">share</span>
                                    </button>
                                @endif
                                <button wire:click="startRestricting({{ $item->id }})" class="p-1.5 rounded-lg bg-indigo-500/80 hover:bg-indigo-600 text-white transition-all backdrop-blur-md">
                                    <span class="material-symbols-outlined text-xs">person_add</span>
                                </button>
                            @endif
                        </div>
                        <a href="{{ Storage::url($item->file_path) }}" target="_blank" class="p-1.5 rounded-lg bg-white/20 hover:bg-white/40 text-white transition-all backdrop-blur-md">
                            <span class="material-symbols-outlined text-xs">download</span>
                        </a>
                    </div>
                </div>

                <!-- Status Badge -->
                <div class="absolute top-2 right-2">
                    <span class="px-2 py-0.5 rounded-full text-[8px] font-black uppercase tracking-widest shadow-xl backdrop-blur-md border border-white/10
                        {{ $item->visibility === 'private' ? 'bg-gray-900/60 text-gray-400' : '' }}
                        {{ $item->visibility === 'public' ? 'bg-emerald-500/60 text-white' : '' }}
                        {{ $item->visibility === 'restricted' ? 'bg-indigo-500/60 text-white' : '' }}
                    ">
                        {{ $item->visibility }}
                    </span>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 flex flex-col items-center justify-center bg-gray-50/50 dark:bg-gray-800/10 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-800">
                <span class="material-symbols-outlined text-gray-300 dark:text-gray-700 text-5xl mb-4">folder_open</span>
                <p class="text-sm text-gray-400 dark:text-gray-500 font-bold uppercase tracking-widest">No files in this vault yet</p>
                @if($activeFolder === 'shared' && !$allowGuestViewShared && $userRole === 'guest')
                    <p class="text-[10px] text-rose-400 dark:text-rose-500 mt-2">Shared gallery has been restricted by the host.</p>
                @endif
            </div>
        @endforelse
    </div>

    <!-- Access Control Modal -->
    @if($selectingAccessFor)
    <div class="fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="saveAccess"></div>
        <div class="relative w-full max-w-lg bg-white dark:bg-[#1e293b] rounded-3xl border border-white/10 shadow-2xl overflow-hidden animate-in zoom-in duration-300">
            <div class="p-6 border-b border-gray-100 dark:border-white/5 flex justify-between items-center">
                <div>
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white uppercase tracking-tight">Grant File Permissions</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Select users who can view this restricted file.</p>
                </div>
                <button wire:click="saveAccess" class="p-2 rounded-xl bg-gray-100 dark:bg-white/5 hover:bg-gray-200 text-gray-400">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            <div class="p-6 max-h-[60vh] overflow-y-auto space-y-6">
                <!-- Organizers -->
                <div>
                    <h5 class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-3">Organizers</h5>
                    <div class="space-y-2">
                        @foreach($allOrganizers as $organizer)
                            <label class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-white/5 transition-all cursor-pointer border border-transparent hover:border-indigo-500/30">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 font-bold text-xs uppercase">
                                        {{ substr($organizer->name, 0, 1) }}
                                    </div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $organizer->name }}</span>
                                </div>
                                <input type="checkbox" wire:model="selectedGuestIds" value="{{ $organizer->id }}" class="rounded-md border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-700" />
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Guests -->
                <div>
                    <h5 class="text-[10px] font-black text-emerald-500 uppercase tracking-widest mb-3">Accepted Guests</h5>
                    <div class="space-y-2">
                        @foreach($allGuests as $guest)
                            @if(!$allOrganizers->contains('id', $guest->id))
                                <label class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-white/5 transition-all cursor-pointer border border-transparent hover:border-emerald-500/30">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 font-bold text-xs uppercase">
                                            {{ substr($guest->name, 0, 1) }}
                                        </div>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $guest->name }}</span>
                                    </div>
                                    <input type="checkbox" wire:model="selectedGuestIds" value="{{ $guest->id }}" class="rounded-md border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-700" />
                                </label>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="p-6 bg-gray-50 dark:bg-white/5 flex justify-end">
                <button wire:click="saveAccess" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black uppercase tracking-widest rounded-xl shadow-lg shadow-indigo-500/20 transition-all">
                    Apply Permissions
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
