<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.public')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        // Instead of immediate login, we validate credentials and initiate 2FA
        $user = $this->form->validateCredentials();

        // Generate 6-digit code
        $code = rand(100000, 999999);
        $user->update([
            'two_factor_code' => $code,
            'two_factor_expires_at' => now()->addMinutes(10),
        ]);

        // Send the 2FA code via email
        \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\TwoFactorCodeMail((string) $code));

        // Store user ID in session for 2FA verification
        Session::put('auth.2fa_user_id', $user->id);
        Session::put('auth.remember', $this->form->remember);

        $this->redirect(route('verification.2fa'), navigate: true);
    }
}; ?>

<div class="relative min-h-screen w-full flex flex-col bg-hero-blur" style="background-image: linear-gradient(rgba(16, 23, 34, 0.8), rgba(16, 23, 34, 0.8)), url(https://images.unsplash.com/photo-1511795409834-ef04bbd61622?q=80&w=2069&auto=format&fit=crop); background-size: cover; background-position: center;">
    <header class="flex items-center justify-between whitespace-nowrap px-8 md:px-20 py-6 absolute top-0 w-full z-10">
        <div class="flex items-center gap-3">
            <div class="size-10 bg-[#257bf4] rounded-lg flex items-center justify-center text-white shadow-lg shadow-[#257bf4]/20">
                <span class="material-symbols-outlined !text-3xl">celebration</span>
            </div>
            <h2 class="text-white text-xl font-extrabold leading-tight tracking-tight">Event Planner</h2>
        </div>
        <nav class="flex items-center gap-8">
            <a class="text-slate-300 hover:text-white text-sm font-semibold transition-colors" href="{{ route('home') }}" wire:navigate>Home</a>
            <a class="px-5 py-2 rounded-full bg-white/10 hover:bg-white/20 text-white text-sm font-semibold backdrop-blur-md transition-all border border-white/10" href="{{ route('register') }}" wire:navigate>Register</a>
        </nav>
    </header>

    <main class="flex-1 flex items-center justify-center px-4 pt-20 pb-12">
        <div class="glass-panel w-full max-w-[480px] rounded-xl p-8 md:p-12 shadow-2xl relative overflow-hidden bg-[#171022]/60 backdrop-blur-[24px] border border-white/10">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-[#257bf4] to-transparent opacity-50"></div>
            <div class="mb-10 text-center">
                <h1 class="text-white text-4xl font-black leading-tight tracking-tight mb-2">Welcome Back</h1>
                <p class="text-slate-400 text-base font-medium">Elevate your next gathering.</p>
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form wire:submit="login" class="space-y-6">
                <!-- Email Address -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-slate-300 ml-1">Email Address</label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-[#257bf4] transition-colors">mail</span>
                        <input wire:model="form.email" class="w-full pl-12 pr-4 py-4 bg-white/5 border border-white/10 rounded-xl text-white placeholder:text-slate-600 focus:outline-none focus:ring-2 focus:ring-[#257bf4]/50 focus:border-[#257bf4] transition-all" placeholder="name@example.com" type="email" required autofocus />
                    </div>
                    <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-slate-300 ml-1">Password</label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-[#257bf4] transition-colors">lock</span>
                        <input wire:model="form.password" class="w-full pl-12 pr-12 py-4 bg-white/5 border border-white/10 rounded-xl text-white placeholder:text-slate-600 focus:outline-none focus:ring-2 focus:ring-[#257bf4]/50 focus:border-[#257bf4] transition-all" placeholder="••••••••" type="password" required />
                    </div>
                    <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label for="remember" class="inline-flex items-center">
                        <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-white/10 bg-white/5 text-[#257bf4] shadow-sm focus:ring-[#257bf4]" name="remember">
                        <span class="ms-2 text-sm text-slate-400">{{ __('Remember me') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-slate-400 hover:text-[#257bf4] text-sm font-medium transition-colors" href="{{ route('password.request') }}" wire:navigate>
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                </div>

                <div class="pt-2">
                    <button class="w-full bg-[#257bf4] hover:bg-[#257bf4]/90 text-white font-bold py-4 rounded-xl shadow-lg shadow-[#257bf4]/25 transition-all active:scale-[0.98] flex items-center justify-center gap-2" type="submit">
                        <span wire:loading.remove>Log in to Dashboard</span>
                        <span wire:loading>Authenticating...</span>
                        <span class="material-symbols-outlined">arrow_forward</span>
                    </button>
                </div>
            </form>

            <div class="mt-10 pt-8 border-t border-white/5 text-center">
                <p class="text-slate-500 text-sm">
                    Don't have an account? 
                    <a class="text-white hover:text-[#257bf4] font-bold ml-1 transition-colors underline decoration-[#257bf4]/30 underline-offset-4" href="{{ route('register') }}" wire:navigate>Sign up for free</a>
                </p>
            </div>
        </div>
    </main>

    <footer class="w-full py-6 px-8 text-center md:text-left">
        <p class="text-slate-500 text-xs font-medium tracking-widest uppercase">
            © 2024 Event Planner Premium. All rights reserved.
        </p>
    </footer>
</div>
