<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.public')] class extends Component
{
    public string $code = '';
    public ?string $error = null;
    public string $email = '';

    public function mount(): void
    {
        $userId = Session::get('auth.2fa_user_id');
        if (! $userId) {
            $this->redirect(route('login'), navigate: true);
            return;
        }

        $user = User::find($userId);
        if (! $user) {
            $this->redirect(route('login'), navigate: true);
            return;
        }

        // Mask the email
        $parts = explode('@', $user->email);
        $this->email = substr($parts[0], 0, 1) . str_repeat('*', strlen($parts[0]) - 1) . '@' . $parts[1];
    }

    public function verify(): void
    {
        $this->error = null;
        $userId = Session::get('auth.2fa_user_id');
        $user = User::find($userId);

        if (! $user || $user->two_factor_code !== $this->code || now()->greaterThan($user->two_factor_expires_at)) {
            $this->error = 'The verification code is invalid or has expired.';
            return;
        }

        // Clear the code
        $user->update([
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
        ]);

        // Log the user in
        Auth::login($user, Session::get('auth.remember', false));

        Session::forget(['auth.2fa_user_id', 'auth.remember']);
        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    public function resend(): void
    {
        $userId = Session::get('auth.2fa_user_id');
        $user = User::find($userId);

        if ($user) {
            $newCode = rand(100000, 999999);
            $user->update([
                'two_factor_code' => $newCode,
                'two_factor_expires_at' => now()->addMinutes(10),
            ]);

            // Resend the 2FA code via email
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\TwoFactorCodeMail((string) $newCode));
            $this->dispatch('code-resent');
        }
    }
}; ?>

<div>
    <div class="fixed inset-0 bg-[#101722] grayscale-[0.5] brightness-[0.2]" style="background-image: url(https://images.unsplash.com/photo-1511795409834-ef04bbd61622?q=80&w=2069&auto=format&fit=crop); background-size: cover; background-position: center;"></div>
    <div class="fixed inset-0 bg-gradient-to-b from-[#101722]/80 via-[#101722]/95 to-[#101722]"></div>

    <div class="relative flex min-h-screen w-full flex-col">
        <header class="flex items-center justify-between whitespace-nowrap border-b border-white/10 px-6 py-4 lg:px-20 text-white">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center size-10 rounded-lg bg-[#257bf4]/10 text-[#257bf4]">
                    <span class="material-symbols-outlined text-3xl">celebration</span>
                </div>
                <h2 class="text-slate-100 text-xl font-bold tracking-tight">Event Planner</h2>
            </div>
        </header>

        <main class="flex-1 flex items-center justify-center p-6">
            <div class="glass-card w-full max-w-[480px] rounded-xl p-8 lg:p-12 shadow-2xl bg-[#101722]/75 backdrop-blur-[20px] border border-[#257bf4]/20 text-white">
                <div class="flex flex-col items-center text-center mb-10">
                    <div class="size-16 rounded-full bg-[#257bf4]/20 flex items-center justify-center mb-6 border border-[#257bf4]/30">
                        <span class="material-symbols-outlined text-[#257bf4] text-4xl">shield_lock</span>
                    </div>
                    <h1 class="text-white text-3xl font-extrabold tracking-tight mb-3">Two-Factor Verification</h1>
                    <p class="text-slate-400 text-base leading-relaxed">
                        Security first. We've sent a 6-digit code to <br class="hidden sm:block"/> <span class="text-slate-200 font-medium italic">{{ $email }}</span>
                    </p>
                </div>

                <form wire:submit="verify" class="flex flex-col gap-8">
                    <div>
                        <input wire:model="code" class="w-full h-16 text-center text-4xl font-bold bg-white/5 border border-white/10 rounded-xl focus:border-[#257bf4] focus:ring-2 focus:ring-[#257bf4]/20 focus:outline-none transition-all text-white tracking-[0.5em] placeholder:tracking-normal" placeholder="000000" maxlength="6" type="text" required autofocus />
                        
                        @if ($error)
                            <p class="text-red-500 text-sm mt-2 text-center">{{ $error }}</p>
                        @endif
                    </div>

                    <div class="flex flex-col gap-4">
                        <button class="w-full h-14 bg-[#257bf4] hover:bg-[#257bf4]/90 text-white font-bold text-lg rounded-xl shadow-lg shadow-[#257bf4]/20 flex items-center justify-center transition-all group" type="submit">
                            <span wire:loading.remove wire:target="verify">Verify Account</span>
                            <span wire:loading wire:target="verify">Verifying...</span>
                            <span class="material-symbols-outlined ml-2 group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </button>
                        
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-2">
                            <button type="button" wire:click="resend" class="text-slate-400 hover:text-[#257bf4] text-sm font-medium flex items-center gap-1 transition-colors">
                                <span class="material-symbols-outlined text-lg">refresh</span>
                                <span wire:loading.remove wire:target="resend">Resend Code</span>
                                <span wire:loading wire:target="resend">Sending...</span>
                            </button>
                            <a class="text-slate-400 hover:text-white text-sm font-medium flex items-center gap-1 transition-colors" href="{{ route('login') }}" wire:navigate>
                                <span class="material-symbols-outlined text-lg">keyboard_backspace</span>
                                Back to Login
                            </a>
                        </div>
                    </div>
                </form>

                <div class="mt-12 flex items-center justify-center gap-2 text-slate-500 text-xs uppercase tracking-widest">
                    <span class="material-symbols-outlined text-sm">lock</span>
                    SECURE ENCRYPTED VERIFICATION
                </div>
            </div>
        </main>

        <footer class="p-6 text-center">
            <p class="text-slate-500 text-sm">© 2024 Event Planner. All rights reserved.</p>
        </footer>
    </div>
</div>
