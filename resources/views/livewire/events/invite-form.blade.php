<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Invite Guests</h3>
        
        @if (session()->has('invite_warning'))
            <div class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                <p class="text-xs text-amber-800 mb-2">{{ session('invite_warning') }}</p>
                <div class="flex gap-2">
                    <button type="button" wire:click="resendInvite" class="px-3 py-1 bg-amber-600 text-white rounded text-[10px] font-bold uppercase hover:bg-amber-700">Resend Anyway</button>
                    <button type="button" wire:click="$reset" class="px-3 py-1 bg-white border border-gray-300 text-gray-600 rounded text-[10px] font-bold uppercase">Cancel</button>
                </div>
            </div>
        @endif

        <form wire:submit.prevent="invite" class="space-y-4">
            <div>
                <x-input-label for="email" :value="__('Email Address')" />
                <x-text-input wire:model="email" id="email" type="email" class="mt-1 block w-full" placeholder="guest@example.com" required />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />
            </div>

            <div>
                <x-input-label for="invite_message" :value="__('Personal Note (Optional)')" />
                <textarea wire:model="message" id="invite_message" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:border-indigo-600 rounded-md shadow-sm" rows="2" placeholder="Join us for a great time!"></textarea>
                <x-input-error class="mt-2" :messages="$errors->get('message')" />
            </div>

            @if (session()->has('invite_message'))
                <p class="text-sm text-green-600">{{ session('invite_message') }}</p>
            @endif

            <div class="flex justify-end">
                <x-primary-button>
                    {{ __('Send Invitation') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</div>
