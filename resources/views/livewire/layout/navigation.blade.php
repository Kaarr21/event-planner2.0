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
        $userId = auth()->id();
        return [
            'hasCancelledEvents' => $userId ? \App\Models\Event::cancelledForUser($userId)->exists() : false,
            'hasOrganizations' => $userId ? auth()->user()->organizations()->exists() : false,
        ];
    }
}; ?>

<nav x-data="{ open: false }" class="bg-white/80 backdrop-blur-2xl border-b border-slate-200/60 sticky top-0 z-40 transition-all duration-500">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2 group transition-transform duration-300 hover:scale-105">
                        <x-application-logo class="w-12 h-12 text-indigo-600" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-10 sm:-my-px sm:ms-12 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate class="nav-link-lux">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    @auth
                        <x-nav-link :href="route('events.index')" :active="request()->routeIs('events.index')"
                            wire:navigate class="nav-link-lux">
                            {{ __('My Events') }}
                        </x-nav-link>
                    @endauth

                    <x-nav-link :href="route('events.discovery')" :active="request()->routeIs('events.discovery')"
                        wire:navigate class="nav-link-lux">
                        {{ __('Discover') }}
                    </x-nav-link>

                    @if($hasCancelledEvents)
                        <x-nav-link :href="route('events.cancelled')" :active="request()->routeIs('events.cancelled')"
                            wire:navigate class="nav-link-lux">
                            {{ __('Cancelled') }}
                        </x-nav-link>
                    @endif

                    @if($hasOrganizations)
                        <x-nav-link href="/org-admin" :active="request()->is('org-admin*')" class="nav-link-lux">
                            {{ __('Org Dashboard') }}
                        </x-nav-link>
                    @endif

                    @auth
                        <x-nav-link :href="route('notifications.index')" :active="request()->routeIs('notifications.*')"
                            wire:navigate class="nav-link-lux">
                            {{ __('Notifications') }}
                            @php
                                $unreadCount = \App\Models\Notification::where('user_id', auth()->id())->where('read', false)->count();
                            @endphp
                            @if($unreadCount > 0)
                                <span
                                    class="ms-2 px-2 py-0.5 text-[10px] font-bold leading-none text-white bg-indigo-600 rounded-full shadow-sm shadow-indigo-200">
                                    {{ $unreadCount }}
                                </span>
                            @endif
                        </x-nav-link>
                    @endauth
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                    <x-dropdown align="right" width="56">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center px-4 py-2 border border-slate-100 text-sm leading-4 font-medium rounded-2xl text-slate-600 bg-white hover:bg-slate-50 hover:text-indigo-600 focus:outline-none transition ease-in-out duration-300 shadow-sm">
                                <div x-data="{{ json_encode(['name' => auth()->user()->name, 'photo_url' => auth()->user()->profile_photo_url]) }}" class="flex items-center gap-3"
                                    x-on:profile-updated.window="name = $event.detail.name; photo_url = $event.detail.photo_url">
                                    <img :src="photo_url" class="h-9 w-9 rounded-full object-cover ring-2 ring-indigo-50" :alt="name">
                                    <span x-text="name" class="font-semibold"></span>
                                </div>

                                <div class="ms-2 transition-transform duration-300 group-hover:rotate-180">
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
                            <div class="px-4 py-3 border-b border-slate-100">
                                <p class="text-xs text-slate-400 font-medium uppercase tracking-wider">Account</p>
                                <p class="text-sm font-semibold text-slate-900 truncate">{{ auth()->user()->email }}</p>
                            </div>
                            <x-dropdown-link :href="route('profile')" wire:navigate class="hover:bg-indigo-50 hover:text-indigo-600">
                                {{ __('Profile Settings') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <button wire:click="logout" class="w-full text-start">
                                <x-dropdown-link class="hover:bg-red-50 hover:text-red-600">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </button>
                        </x-slot>
                    </x-dropdown>
                @else
                    <div class="flex items-center gap-6">
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-600 hover:text-indigo-600 transition-colors duration-300">Login</a>
                        <a href="{{ route('register') }}" class="btn-lux shadow-indigo-100">Join Now</a>
                    </div>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-3 rounded-2xl text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 focus:outline-none transition duration-150 ease-in-out">
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
        class="hidden sm:hidden bg-white/95 backdrop-blur-xl border-t border-slate-100 overflow-hidden transition-all duration-500">
        <div class="pt-4 pb-6 space-y-2 px-4">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate class="rounded-2xl">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('events.index')" :active="request()->routeIs('events.index')"
                wire:navigate class="rounded-2xl">
                {{ __('My Events') }}
            </x-responsive-nav-link>

            @if($hasCancelledEvents)
                <x-responsive-nav-link :href="route('events.cancelled')" :active="request()->routeIs('events.cancelled')"
                    wire:navigate class="rounded-2xl">
                    {{ __('Cancelled') }}
                </x-responsive-nav-link>
            @endif

            @if($hasOrganizations)
                <x-responsive-nav-link href="/org-admin" :active="request()->is('org-admin*')" class="rounded-2xl">
                    {{ __('Org Dashboard') }}
                </x-responsive-nav-link>
            @endif

            <x-responsive-nav-link :href="route('notifications.index')" :active="request()->routeIs('notifications.*')"
                wire:navigate class="rounded-2xl">
                {{ __('Notifications') }}
            </x-responsive-nav-link>
        </div>

        @auth
            <!-- Responsive Settings Options -->
            <div class="pt-6 pb-8 border-t border-slate-100 bg-slate-50/50">
                <div class="flex items-center px-6 gap-4">
                    <div class="flex-shrink-0" x-data="{{ json_encode(['photo_url' => auth()->user()->profile_photo_url]) }}"
                        x-on:profile-updated.window="photo_url = $event.detail.photo_url">
                        <img :src="photo_url" class="h-12 w-12 rounded-full object-cover ring-4 ring-white shadow-sm" alt="{{ auth()->user()->name }}">
                    </div>

                    <div class="flex-grow">
                        <div class="font-bold text-lg text-slate-900"
                            x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name"
                            x-on:profile-updated.window="name = $event.detail.name"></div>
                        <div class="font-medium text-sm text-slate-500">{{ auth()->user()->email }}</div>
                    </div>
                </div>

                <div class="mt-6 space-y-2 px-4">
                    <x-responsive-nav-link :href="route('profile')" wire:navigate class="rounded-2xl">
                        {{ __('Profile Settings') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <button wire:click="logout" class="w-full text-start">
                        <x-responsive-nav-link class="rounded-2xl text-red-600">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </button>
                </div>
            </div>
        @else
            <div class="pt-6 pb-8 border-t border-slate-100 bg-slate-50/50">
                <div class="px-6 space-y-4">
                    <a href="{{ route('login') }}" class="block w-full py-3 text-center text-base font-semibold text-slate-600 hover:text-indigo-600">Login</a>
                    <a href="{{ route('register') }}" class="block w-full py-3 text-center btn-lux">Join Now</a>
                </div>
            </div>
        @endauth
    </div>
</nav>