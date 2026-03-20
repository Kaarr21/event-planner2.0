<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">All Invited People</h3>
            <span class="text-xs bg-indigo-100 text-indigo-800 px-2 py-1 rounded-full font-bold">Total: {{ $invites->count() }}</span>
        </div>

        <div class="space-y-4">
            @forelse ($invites as $invite)
                <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700 last:border-0">
                    <div class="flex flex-col">
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $invite->invitee_email }}</span>
                        @if($invite->invitee_id)
                            <span class="text-[10px] text-green-600 font-medium">Registered: {{ $invite->invitee?->name ?? 'User Found' }}</span>
                        @else
                            <span class="text-[10px] text-amber-600 italic">Account not registered</span>
                        @endif
                    </div>
                    
                    <div class="flex flex-col items-end gap-2">
                        <div class="flex items-center gap-2">
                            @if($invite->status === 'draft')
                                <button wire:click="$parent.sendDraftInvite({{ $invite->id }})" class="p-1 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors border border-emerald-100" title="Send Invitation">
                                    <span class="material-symbols-outlined text-sm">send</span>
                                </button>
                                <button wire:click="$parent.openEditDraft({{ $invite->id }})" class="p-1 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors border border-amber-100" title="Edit Draft">
                                    <span class="material-symbols-outlined text-sm">edit</span>
                                </button>
                            @endif
                            <span class="text-[10px] px-2 py-1 rounded-lg font-black uppercase tracking-widest border
                                {{ $invite->status === 'accepted' ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : '' }}
                                {{ $invite->status === 'pending' ? 'bg-indigo-100 text-indigo-700 border-indigo-200' : '' }}
                                {{ $invite->status === 'declined' ? 'bg-rose-100 text-rose-700 border-rose-200' : '' }}
                                {{ $invite->status === 'draft' ? 'bg-amber-100 text-amber-700 border-amber-200' : '' }}
                            ">
                                {{ ucfirst($invite->status) }}
                            </span>
                        </div>
                        @if($invite->responded_at)
                            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">{{ \Carbon\Carbon::parse($invite->responded_at)->diffForHumans() }}</span>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500 text-center py-4">No invitations sent yet.</p>
            @endforelse
        </div>
    </div>
</div>
