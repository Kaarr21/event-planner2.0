<div class="bg-white dark:bg-gray-800/50 overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 dark:border-gray-700/50 backdrop-blur-sm">
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center gap-4">
                @if($invites->count() > 0 && $canEditEvent)
                    <div class="flex items-center gap-3 bg-gray-50 dark:bg-gray-900/50 px-3 py-2 rounded-xl border border-gray-100 dark:border-gray-700/50">
                        <input type="checkbox" 
                               wire:click="$parent.selectAllInvites" 
                               {{ count($selectedInviteIds) === $invites->count() && $invites->count() > 0 ? 'checked' : '' }}
                               class="w-5 h-5 rounded-lg border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 bg-white dark:bg-gray-800 transition-all cursor-pointer">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Select All</span>
                    </div>
                @endif
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">All Invited People</h3>
            </div>
            <span class="text-[10px] bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300 px-3 py-1 rounded-full font-black uppercase tracking-widest">Total: {{ $invites->count() }}</span>
        </div>

        <div class="space-y-3">
            @forelse ($invites as $invite)
                <div class="flex items-center justify-between p-4 rounded-2xl border border-gray-100 dark:border-gray-700/30 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-all group {{ in_array($invite->id, $selectedInviteIds) ? 'bg-indigo-50/50 dark:bg-indigo-900/10 border-indigo-200 dark:border-indigo-800/50' : '' }}">
                    <div class="flex items-center gap-4 flex-1">
                        @if($canEditEvent)
                            <input type="checkbox" 
                                   wire:click="$parent.toggleInviteSelection({{ $invite->id }})" 
                                   {{ in_array($invite->id, $selectedInviteIds) ? 'checked' : '' }}
                                   class="w-5 h-5 rounded-lg border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 bg-white dark:bg-gray-800 transition-all cursor-pointer">
                        @endif
                        
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $invite->invitee_email }}</span>
                            @if($invite->invitee_id)
                                <div class="flex items-center gap-2 mt-1">
                                    <img src="{{ $invite->invitee?->profile_photo_url }}" class="w-6 h-6 rounded-full object-cover border border-gray-100 dark:border-white/10" alt="{{ $invite->invitee?->name }}">
                                    <span class="text-[10px] text-green-600 dark:text-green-400 font-black uppercase tracking-widest">{{ $invite->invitee?->name ?? 'User Found' }}</span>
                                </div>
                            @else
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500/50"></span>
                                    <span class="text-[10px] text-amber-600/70 dark:text-amber-400/50 italic font-medium uppercase tracking-widest">Awaiting registration</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex flex-col items-end gap-2">
                        <div class="flex items-center gap-3">
                            @if($invite->status === 'draft')
                                <button wire:click="$parent.sendDraftInvite({{ $invite->id }})" class="p-2 text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-xl transition-all border border-emerald-100 dark:border-emerald-900/30 shadow-sm" title="Send Invitation">
                                    <span class="material-symbols-outlined text-sm">send</span>
                                </button>
                                <button wire:click="$parent.openEditDraft({{ $invite->id }})" class="p-2 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-xl transition-all border border-amber-100 dark:border-amber-900/30 shadow-sm" title="Edit Draft">
                                    <span class="material-symbols-outlined text-sm">edit</span>
                                </button>
                            @endif
                            <span class="text-[9px] px-3 py-1 rounded-full font-black uppercase tracking-widest border shadow-sm
                                {{ $invite->status === 'accepted' ? 'bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800' : '' }}
                                {{ $invite->status === 'pending' ? 'bg-indigo-100 text-indigo-700 border-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-400 dark:border-indigo-800' : '' }}
                                {{ $invite->status === 'declined' ? 'bg-rose-100 text-rose-700 border-rose-200 dark:bg-rose-900/30 dark:text-rose-400 dark:border-rose-800' : '' }}
                                {{ $invite->status === 'draft' ? 'bg-amber-100 text-amber-700 border-amber-200 dark:bg-amber-900/30 dark:text-amber-400 dark:border-amber-800' : '' }}
                            ">
                                {{ $invite->status }}
                            </span>
                        </div>
                        @if($invite->responded_at)
                            <span class="text-[9px] text-gray-400 dark:text-gray-500 font-bold uppercase tracking-wider">{{ \Carbon\Carbon::parse($invite->responded_at)->diffForHumans() }}</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-12 px-6 bg-gray-50 dark:bg-gray-900/30 rounded-3xl border-2 border-dashed border-gray-100 dark:border-gray-800">
                    <span class="material-symbols-outlined text-4xl text-gray-300 dark:text-gray-700 mb-4">person_add</span>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium text-center">No invitations sent yet.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
