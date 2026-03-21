<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }

    public function with(): array
    {
        return [
            'hasCancelledEvents' => \App\Models\Event::cancelledForUser(auth()->id())->exists(),
        ];
    }
}; ?>

<nav x-data="{ open: false }" class="bg-white/90 dark:bg-brand-dark/60 backdrop-blur-2xl border-b border-gray-100 dark:border-white/5 sticky top-0 z-40">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2">
                        <x-application-logo class="w-10 h-10" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('events.index')" :active="request()->routeIs('events.index')"
                        wire:navigate>
                        {{ __('My Events') }}
                    </x-nav-link>

                    @if($hasCancelledEvents)
                        <x-nav-link :href="route('events.cancelled')" :active="request()->routeIs('events.cancelled')"
                            wire:navigate>
                            {{ __('Cancelled') }}
                        </x-nav-link>
                    @endif

                    <x-nav-link :href="route('notifications.index')" :active="request()->routeIs('notifications.*')"
                        wire:navigate>
                        {{ __('Notifications') }}
                        @php
                            $unreadCount = \App\Models\Notification::where('user_id', auth()->id())->where('read', false)->count();
                        @endphp
                        @if($unreadCount > 0)
                            <span
                                class="ms-1 px-1.5 py-0.5 text-[10px] font-bold leading-none text-white bg-[#257bf4] rounded-full">
                                {{ $unreadCount }}
                            </span>
                        @endif
                    </x-nav-link>
                </div>
            </div>

            <!-- Theme Toggle & Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-4">
                <div x-data="{ 
                    theme: localStorage.theme || 'system',
                    init() {
                        window.addEventListener('storage', (e) => {
                            if (e.key === 'theme') {
                                this.theme = e.newValue || 'system';
                            }
                        });
                    },
                    setTheme(newTheme) {
                        this.theme = newTheme;
                        if (newTheme === 'system') {
                            localStorage.removeItem('theme');
                        } else {
                            localStorage.theme = newTheme;
                        }
                        if (window.applyTheme) window.applyTheme();
                    }
                }" class="flex items-center">
                    <button @click="setTheme(theme === 'light' ? 'dark' : (theme === 'dark' ? 'system' : 'light'))"
                        class="p-2 rounded-lg text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-white/5 transition duration-150 ease-in-out focus:outline-none"
                        :title="'Theme: ' + theme">
                        <span x-show="theme === 'light'" class="material-symbols-outlined">light_mode</span>
                        <span x-show="theme === 'dark'" class="material-symbols-outlined">dark_mode</span>
                        <span x-show="theme === 'system'" class="material-symbols-outlined">desktop_windows</span>
                    </button>
                </div>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-[#101722]/50 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div x-data="{{ json_encode(['name' => auth()->user()->name, 'photo_url' => auth()->user()->profile_photo_url]) }}" class="flex items-center gap-2"
                                x-on:profile-updated.window="name = $event.detail.name; photo_url = $event.detail.photo_url">
                                <img :src="photo_url" class="h-8 w-8 rounded-full object-cover" :alt="name">
                                <span x-text="name"></span>
                            </div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile')" wire:navigate>
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link>
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}"
        class="hidden sm:hidden bg-white dark:bg-[#1e293b] transition-colors duration-300">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('events.index')" :active="request()->routeIs('events.index')"
                wire:navigate>
                {{ __('My Events') }}
            </x-responsive-nav-link>

            @if($hasCancelledEvents)
                <x-responsive-nav-link :href="route('events.cancelled')" :active="request()->routeIs('events.cancelled')"
                    wire:navigate>
                    {{ __('Cancelled') }}
                </x-responsive-nav-link>
            @endif

            <x-responsive-nav-link :href="route('notifications.index')" :active="request()->routeIs('notifications.*')"
                wire:navigate>
                {{ __('Notifications') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-white/10">
            <div class="flex items-center px-4 gap-3">
                <div class="flex-shrink-0" x-data="{{ json_encode(['photo_url' => auth()->user()->profile_photo_url]) }}"
                    x-on:profile-updated.window="photo_url = $event.detail.photo_url">
                    <img :src="photo_url" class="h-10 w-10 rounded-full object-cover" alt="{{ auth()->user()->name }}">
                </div>

                <div class="flex-grow">
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200"
                        x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name"
                        x-on:profile-updated.window="name = $event.detail.name"></div>
                    <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
                </div>

                <div x-data="{ 
                    theme: localStorage.theme || 'system',
                    init() {
                        window.addEventListener('storage', (e) => {
                            if (e.key === 'theme') {
                                this.theme = e.newValue || 'system';
                            }
                        });
                    },
                    setTheme(newTheme) {
                        this.theme = newTheme;
                        if (newTheme === 'system') {
                            localStorage.removeItem('theme');
                        } else {
                            localStorage.theme = newTheme;
                        }
                        if (window.applyTheme) window.applyTheme();
                    }
                }" class="flex items-center">
                    <button @click="setTheme(theme === 'light' ? 'dark' : (theme === 'dark' ? 'system' : 'light'))"
                        class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5 transition duration-150 ease-in-out focus:outline-none">
                        <span x-show="theme === 'light'" class="material-symbols-outlined">light_mode</span>
                        <span x-show="theme === 'dark'" class="material-symbols-outlined">dark_mode</span>
                        <span x-show="theme === 'system'" class="material-symbols-outlined">desktop_windows</span>
                    </button>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile')" wire:navigate>
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link>
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>