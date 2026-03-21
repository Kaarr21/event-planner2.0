<div class="glass-card dark:glass-card-dark overflow-hidden rounded-[2.5rem] border-none shadow-3xl">
    <div class="p-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-10">
            <div class="flex items-center gap-6">
                @if($invites->count() > 0 && $canEditEvent)
                    <div class="flex items-center gap-4 bg-white/5 px-5 py-3 rounded-2xl border border-white/5 group cursor-pointer hover:bg-white/10 transition-all">
                        <input type="checkbox" 
                               wire:click="$parent.selectAllInvites" 
                               {{ count($selectedInviteIds) === $invites->count() && $invites->count() > 0 ? 'checked' : '' }}
                               class="w-6 h-6 rounded-lg border-2 border-brand-orange/20 text-brand-orange focus:ring-0 checked:bg-brand-orange checked:border-brand-orange transition-all cursor-pointer">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] group-hover:text-gray-300 transition-colors">Select All</span>
                    </div>
                @endif
                <div>
                    <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tighter italic leading-none mb-1">Curation Registry</h3>
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest opacity-60">Verified attendance tracking.</p>
                </div>
            </div>
            <span class="text-[10px] bg-brand-teal/10 text-brand-teal border border-brand-teal/20 px-5 py-2 rounded-full font-black uppercase tracking-widest shadow-sm">Total: {{ $invites->count() }}</span>
        </div>

        <div class="space-y-4">
            @forelse ($invites as $invite)
                <div class="flex items-center justify-between p-6 rounded-[2rem] border border-white/5 hover:bg-white/5 transition-all duration-500 group {{ in_array($invite->id, $selectedInviteIds) ? 'bg-brand-orange/10 border-brand-orange/20' : '' }}">
                    <div class="flex items-center gap-6 flex-1">
                        @if($canEditEvent)
                            <input type="checkbox" 
                                   wire:click="$parent.toggleInviteSelection({{ $invite->id }})" 
                                   {{ in_array($invite->id, $selectedInviteIds) ? 'checked' : '' }}
                                   class="w-6 h-6 rounded-lg border-2 border-brand-orange/20 text-brand-orange focus:ring-0 checked:bg-brand-orange checked:border-brand-orange transition-all cursor-pointer">
                        @endif
                        
                        <div class="flex flex-col">
                            <span class="text-base font-black text-gray-900 dark:text-white group-hover:text-brand-orange transition-colors italic tracking-tight uppercase leading-none mb-1">{{ $invite->invitee_email }}</span>
                            @if($invite->invitee_id)
                                <div class="flex items-center gap-3 mt-1 group-hover:translate-x-1 transition-transform">
                                    <img src="{{ $invite->invitee?->profile_photo_url }}" class="w-7 h-7 rounded-lg object-cover border border-white/10 shadow-lg" alt="{{ $invite->invitee?->name }}">
                                    <span class="text-[10px] text-brand-teal font-black uppercase tracking-[0.2em] italic">{{ $invite->invitee?->name ?? 'User Found' }}</span>
                                </div>
                            @else
                                <div class="flex items-center gap-2 mt-1 opacity-60">
                                    <span class="w-1.5 h-1.5 rounded-full bg-brand-yellow animate-pulse shadow-[0_0_8px_rgba(255,215,0,0.5)]"></span>
                                    <span class="text-[9px] text-brand-yellow italic font-black uppercase tracking-[0.2em]">Awaiting onboarding</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex flex-col items-end gap-3">
                        <div class="flex items-center gap-3">
                            @if($invite->status === 'draft')
                                <button wire:click="$parent.sendDraftInvite({{ $invite->id }})" class="w-10 h-10 rounded-xl bg-brand-teal/10 hover:bg-brand-teal text-brand-teal hover:text-white transition-all border border-brand-teal/20 shadow-sm flex items-center justify-center group/btn" title="Send Invitation">
                                    <span class="material-symbols-outlined text-lg group-hover/btn:translate-x-1 group-hover/btn:-translate-y-1 transition-transform">send</span>
                                </button>
                                <button wire:click="$parent.openEditDraft({{ $invite->id }})" class="w-10 h-10 rounded-xl bg-brand-orange/10 hover:bg-brand-orange text-brand-orange hover:text-white transition-all border border-brand-orange/20 shadow-sm flex items-center justify-center group/btn" title="Edit Draft">
                                    <span class="material-symbols-outlined text-lg group-hover/btn:rotate-12 transition-transform">edit</span>
                                </button>
                            @endif
                            <span class="text-[9px] px-4 py-1.5 rounded-xl font-black uppercase tracking-[0.2em] border shadow-sm transition-all duration-500 group-hover:scale-105
                                {{ $invite->status === 'accepted' ? 'bg-brand-teal/10 text-brand-teal border-brand-teal' : '' }}
                                {{ $invite->status === 'pending' ? 'bg-blue-500/10 text-blue-500 border-blue-500/30' : '' }}
                                {{ $invite->status === 'declined' ? 'bg-brand-red/10 text-brand-red border-brand-red' : '' }}
                                {{ $invite->status === 'draft' ? 'bg-brand-yellow/10 text-brand-yellow border-brand-yellow' : '' }}
                            ">
                                {{ $invite->status }}
                            </span>
                        </div>
                        @if($invite->responded_at)
                            <span class="text-[8px] text-gray-500 font-black uppercase tracking-widest italic opacity-60">{{ \Carbon\Carbon::parse($invite->responded_at)->diffForHumans() }}</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-24 px-10 bg-white/5 rounded-[3rem] border-2 border-dashed border-white/5 opacity-40">
                    <span class="material-symbols-outlined text-7xl text-brand-orange mb-6 opacity-20">person_add</span>
                    <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] italic text-center">No invitations dispatched.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
