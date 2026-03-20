<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Create New Event') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <form wire:submit.prevent="save" class="space-y-6">
                    <div>
                        <x-input-label for="title" :value="__('Event Title')" />
                        <x-text-input wire:model="title" id="title" name="title" type="text" class="mt-1 block w-full" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('title')" />
                    </div>

                    <div>
                        <div class="flex justify-between">
                            <x-input-label for="description" :value="__('Description')" />
                            <button type="button" wire:click="generateAIDescription" class="text-xs text-indigo-600 hover:text-indigo-900 font-semibold flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A3.005 3.005 0 011 15v3H0v-3a3 3 0 013.75-2.906z" />
                                </svg>
                                Generate with AI
                            </button>
                        </div>
                        <textarea wire:model="description" id="description" name="description" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" rows="4"></textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="date" :value="__('Date & Time')" />
                            <x-text-input wire:model="date" id="date" name="date" type="datetime-local" class="mt-1 block w-full" required />
                            <x-input-error class="mt-2" :messages="$errors->get('date')" />
                        </div>

                        <div>
                            <x-input-label for="location" :value="__('Location')" />
                            <x-text-input wire:model="location" id="location" name="location" type="text" class="mt-1 block w-full" placeholder="Online, City, or Venue" />
                            <x-input-error class="mt-2" :messages="$errors->get('location')" />
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="material-symbols-outlined text-indigo-500">group_add</span>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Invite Guests (Optional)</h3>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="inviteEmails" :value="__('Guest Emails')" />
                                <p class="text-xs text-gray-500 mb-2">Separate emails with commas or new lines.</p>
                                <textarea wire:model="inviteEmails" id="inviteEmails" name="inviteEmails" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" rows="3" placeholder="friend@example.com, colleague@work.com"></textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('inviteEmails')" />
                            </div>

                            <div>
                                <x-input-label for="inviteMessage" :value="__('Personal Message')" />
                                <x-text-input wire:model="inviteMessage" id="inviteMessage" name="inviteMessage" type="text" class="mt-1 block w-full" placeholder="Hey! Hope you can make it to my event." />
                                <x-input-error class="mt-2" :messages="$errors->get('inviteMessage')" />
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 mt-6">
                        <x-secondary-button href="{{ route('dashboard') }}" wire:navigate>
                            {{ __('Cancel') }}
                        </x-secondary-button>

                        <x-primary-button>
                            {{ __('Create Event') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
