<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.public')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="relative min-h-screen w-full flex flex-col bg-hero-blur" style="background-image: linear-gradient(rgba(16, 23, 34, 0.8), rgba(16, 23, 34, 0.95)), url(https://images.unsplash.com/photo-1511795409834-ef04bbd61622?q=80&w=2069&auto=format&fit=crop); background-size: cover; background-position: center;">
    <header class="w-full px-6 py-4">
        <nav class="max-w-7xl mx-auto flex items-center justify-between bg-[#101722]/60 backdrop-blur-[12px] border border-white/10 px-8 py-3 rounded-full text-white">
            <div class="flex items-center gap-3">
                <div class="text-[#257bf4] flex items-center">
                    <span class="material-symbols-outlined text-3xl">celebration</span>
                </div>
                <h2 class="text-xl font-bold tracking-tight">Event Planner</h2>
            </div>
            <div class="flex items-center gap-8">
                <div class="hidden md:flex items-center gap-8">
                    <a class="text-sm font-medium hover:text-[#257bf4] transition-colors" href="{{ route('home') }}" wire:navigate>Home</a>
                    <a class="text-sm font-medium hover:text-[#257bf4] transition-colors" href="#">Pricing</a>
                    <a class="text-sm font-medium hover:text-[#257bf4] transition-colors" href="#">Portfolio</a>
                </div>
                <div class="h-6 w-px bg-slate-700 mx-2"></div>
                <div class="flex items-center gap-4">
                    <a class="text-sm font-medium hover:text-[#257bf4] transition-colors" href="{{ route('login') }}" wire:navigate>Login</a>
                    <a href="{{ route('register') }}" class="bg-[#257bf4] hover:bg-[#257bf4]/90 text-white px-6 py-2 rounded-full text-sm font-bold transition-all shadow-lg shadow-[#257bf4]/20">
                        Get Started
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <main class="flex-1 flex items-center justify-center p-6">
        <div class="w-full max-w-[520px] bg-[#101722]/60 backdrop-blur-[12px] border border-white/10 rounded-xl p-8 md:p-12 shadow-2xl text-white">
            <div class="mb-10 text-center">
                <h1 class="text-3xl font-black mb-2 tracking-tight">Join Event Planner</h1>
                <p class="text-slate-400 font-normal">Create your premium account to start planning gala events.</p>
            </div>

            <form wire:submit="register" class="space-y-5">
                <!-- Name -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-300 ml-1">Full Name</label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-[#257bf4] transition-colors">person</span>
                        <input wire:model="name" class="w-full bg-slate-900/50 border border-slate-700/50 rounded-xl py-4 pl-12 pr-4 text-white placeholder:text-slate-500 focus:ring-2 focus:ring-[#257bf4]/50 focus:border-[#257bf4] outline-none transition-all" placeholder="Enter your full name" type="text" required autofocus autocomplete="name" />
                    </div>
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email Address -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-300 ml-1">Email Address</label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-[#257bf4] transition-colors">mail</span>
                        <input wire:model="email" class="w-full bg-slate-900/50 border border-slate-700/50 rounded-xl py-4 pl-12 pr-4 text-white placeholder:text-slate-500 focus:ring-2 focus:ring-[#257bf4]/50 focus:border-[#257bf4] outline-none transition-all" placeholder="name@company.com" type="email" required autocomplete="username" />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-300 ml-1">Password</label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-[#257bf4] transition-colors">lock</span>
                        <input wire:model="password" class="w-full bg-slate-900/50 border border-slate-700/50 rounded-xl py-4 pl-12 pr-12 text-white placeholder:text-slate-500 focus:ring-2 focus:ring-[#257bf4]/50 focus:border-[#257bf4] outline-none transition-all" placeholder="Create a strong password" type="password" required autocomplete="new-password" />
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-300 ml-1">Confirm Password</label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-[#257bf4] transition-colors">lock_reset</span>
                        <input wire:model="password_confirmation" class="w-full bg-slate-900/50 border border-slate-700/50 rounded-xl py-4 pl-12 pr-4 text-white placeholder:text-slate-500 focus:ring-2 focus:ring-[#257bf4]/50 focus:border-[#257bf4] outline-none transition-all" placeholder="Repeat your password" type="password" required autocomplete="new-password" />
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <!-- Submit Button -->
                <button class="w-full bg-[#257bf4] hover:bg-[#257bf4]/90 text-white font-bold py-4 rounded-xl transition-all shadow-xl shadow-[#257bf4]/20 transform hover:-translate-y-0.5 mt-4" type="submit">
                    Register Account
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="text-slate-400 text-sm">
                    Already have an account? 
                    <a class="text-[#257bf4] font-bold hover:underline ml-1" href="{{ route('login') }}" wire:navigate>Log in here</a>
                </p>
            </div>
        </div>
    </main>

    <footer class="w-full p-8 text-center">
        <p class="text-slate-500 text-xs tracking-widest uppercase">© 2024 Event Planner Premium. All Rights Reserved.</p>
    </footer>
</div>
