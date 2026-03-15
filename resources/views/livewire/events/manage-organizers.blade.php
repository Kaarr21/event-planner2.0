<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Event Organizers</h3>

        @if (session()->has('organizer_message'))
            <div class="mb-4 text-sm text-green-600 font-medium bg-green-50 p-2 rounded">
                {{ session('organizer_message') }}
            </div>
        @endif

        @if(auth()->id() === $event->user_id)
        <!-- Add Organizer Form -->
        <form wire:submit.prevent="addOrganizer" class="mb-8 space-y-4 bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Add Organizer by Email</label>
                <div class="flex gap-2">
                    <input type="email" wire:model="email" placeholder="user@example.com" class="flex-1 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-semibold hover:bg-indigo-700">Add</button>
                </div>
                @error('email') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-2 mt-2">
                @foreach($availablePermissions as $key => $label)
                    <label class="inline-flex items-center text-xs text-gray-600 dark:text-gray-400">
                        <input type="checkbox" wire:model="selectedPermissions" value="{{ $key }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 mr-2">
                        {{ $label }}
                    </label>
                @endforeach
            </div>
        </form>
        @endif

        <!-- Organizers List -->
        <div class="space-y-6">
            @forelse ($organizers as $organizer)
                <div class="flex flex-col border-b border-gray-100 dark:border-gray-700 pb-4 last:border-0">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs">
                                {{ substr($organizer->name, 0, 1) }}
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $organizer->name }}</span>
                                <p class="text-xs text-gray-500">{{ $organizer->email }}</p>
                            </div>
                        </div>
                        @if(auth()->id() === $event->user_id)
                        <button wire:click="removeOrganizer({{ $organizer->id }})" wire:confirm="Are you sure you want to remove this organizer?" class="text-xs text-red-600 hover:text-red-900">Remove</button>
                        @endif
                    </div>
                    
                    @if(auth()->id() === $event->user_id)
                    <div class="flex flex-wrap gap-x-4 gap-y-2 mt-1">
                        @foreach($availablePermissions as $key => $label)
                            <label class="inline-flex items-center text-xs text-gray-500">
                                <input type="checkbox" 
                                    wire:change="updatePermissions({{ $organizer->id }}, $event.target.checked ? [...JSON.parse('{{ json_encode($organizer->pivot->permissions ?? []) }}'), '{{ $key }}'] : JSON.parse('{{ json_encode($organizer->pivot->permissions ?? []) }}').filter(p => p !== '{{ $key }}'))"
                                    {{ in_array($key, $organizer->pivot->permissions ?? []) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 mr-1.5 w-3 h-3">
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                    @else
                    <div class="flex flex-wrap gap-2 mt-1">
                        @foreach($organizer->pivot->permissions ?? [] as $perm)
                            <span class="text-[10px] bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 px-2 py-0.5 rounded-full capitalize">
                                {{ str_replace('_', ' ', $perm) }}
                            </span>
                        @endforeach
                    </div>
                    @endif
                </div>
            @empty
                <p class="text-sm text-gray-500 text-center py-4">No additional organizers yet.</p>
            @endforelse
        </div>
    </div>
</div>
