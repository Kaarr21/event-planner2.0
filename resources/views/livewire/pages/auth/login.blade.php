<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.public')] class extends Component {
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

<<div class="relative min-h-screen w-full flex flex-col bg-slate-50 font-serif">
    <header class="flex items-center justify-between px-8 md:px-20 py-6 absolute top-0 w-full z-20">
        <div class="flex items-center gap-3">
            <x-application-logo class="w-10 h-10 text-indigo-600" />
            <span class="text-xl font-bold text-slate-800 tracking-tight">Pearl Pavilion</span>
        </div>
        <nav class="flex items-center gap-8">
            <a class="text-xs font-bold text-slate-500 hover:text-indigo-600 uppercase tracking-widest transition-colors"
                href="{{ route('home') }}" wire:navigate>Home</a>
            <a class="btn-lux px-6 py-2 text-xs"
                href="{{ route('register') }}" wire:navigate>Register</a>
        </nav>
    </header>

    <main class="flex-1 flex items-center justify-center px-4 pt-24 pb-12">
        <div class="w-full max-w-[420px] bg-white border border-slate-100 rounded-lg p-10 md:p-12 shadow-2xl shadow-slate-200/50">
            <div class="mb-10 text-center">
                <h1 class="text-slate-900 text-3xl font-bold mb-3">Login</h1>
                <p class="text-slate-500 text-sm italic">Enter your details to continue</p>
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form wire:submit="login" class="space-y-6">
                <!-- Email Address -->
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Email Address</label>
                    <input wire:model="form.email"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-lg text-slate-900 placeholder:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all font-sans text-sm"
                        placeholder="name@example.com" type="email" required autofocus />
                    <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between ml-1">
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400">Password</label>
                        @if (Route::has('password.request'))
                            <a class="text-indigo-600 hover:text-indigo-700 text-[10px] font-bold uppercase tracking-widest transition-colors"
                                href="{{ route('password.request') }}" wire:navigate>
                                Forgot?
                            </a>
                        @endif
                    </div>
                    <input wire:model="form.password"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-lg text-slate-900 placeholder:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all font-sans text-sm"
                        placeholder="••••••••" type="password" required />
                    <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <label for="remember" class="inline-flex items-center cursor-pointer group">
                        <input wire:model="form.remember" id="remember" type="checkbox"
                            class="rounded border-slate-200 text-indigo-600 shadow-sm focus:ring-indigo-500"
                            name="remember">
                        <span class="ms-2 text-[10px] font-bold uppercase tracking-widest text-slate-400 group-hover:text-slate-600 transition-colors">{{ __('Remember me') }}</span>
                    </label>
                </div>

                <div class="pt-2">
                    <button class="btn-lux w-full py-4 text-xs" type="submit">
                        <span wire:loading.remove>Login</span>
                        <span wire:loading>Authenticating...</span>
                    </button>
                </div>
            </form>

            <div class="mt-10 pt-8 border-t border-slate-50 text-center">
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">
                    Don't have an account?
                    <a class="text-indigo-600 hover:text-indigo-700 ml-1 transition-colors underline underline-offset-4"
                        href="{{ route('register') }}" wire:navigate>Sign up</a>
                </p>
            </div>
        </div>
    </main>

    <footer class="w-full py-8 text-center">
        <p class="text-slate-400 text-[9px] font-bold uppercase tracking-[0.2em]">
            © 2026 Pearl Pavilion. Professional Event Coordination.
        </p>
    </footer>
    </footer>
</div>
