<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="p-8 md:p-12 bg-white dark:bg-[#1e293b]/50 dark:backdrop-blur-xl overflow-hidden shadow-sm sm:rounded-[2.5rem] border border-gray-100 dark:border-white/5">
                <div class="max-w-xl">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            <div class="p-8 md:p-12 bg-white dark:bg-[#1e293b]/50 dark:backdrop-blur-xl overflow-hidden shadow-sm sm:rounded-[2.5rem] border border-gray-100 dark:border-white/5">
                <div class="max-w-xl">
                    <livewire:profile.update-password-form />
                </div>
            </div>

            <div class="p-8 md:p-12 bg-white dark:bg-[#1e293b]/50 dark:backdrop-blur-xl overflow-hidden shadow-sm sm:rounded-[2.5rem] border border-gray-100 dark:border-white/5">
                <div class="max-w-xl">
                    <livewire:profile.delete-user-form />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
