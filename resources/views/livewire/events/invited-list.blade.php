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
                        @if($invite->invitee)
                            <span class="text-[10px] text-gray-500">Registered: {{ $invite->invitee->name }}</span>
                        @else
                            <span class="text-[10px] text-amber-600 italic">Account not registered</span>
                        @endif
                    </div>
                    
                    <div class="flex flex-col items-end">
                        <span class="text-xs px-2 py-0.5 rounded-full font-bold
                            {{ $invite->status === 'accepted' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $invite->status === 'pending' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $invite->status === 'declined' ? 'bg-red-100 text-red-800' : '' }}
                        ">
                            {{ ucfirst($invite->status) }}
                        </span>
                        @if($invite->responded_at)
                            <span class="text-[10px] text-gray-400 mt-1">{{ \Carbon\Carbon::parse($invite->responded_at)->diffForHumans() }}</span>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500 text-center py-4">No invitations sent yet.</p>
            @endforelse
        </div>
    </div>
</div>
