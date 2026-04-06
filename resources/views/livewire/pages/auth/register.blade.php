<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.public')] class extends Component {
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
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        event(new Registered($user));

        // Link pending invitations
        $pendingInvites = \App\Models\Invite::where('invitee_email', $user->email)
            ->whereNull('invitee_id')
            ->get();

        foreach ($pendingInvites as $invite) {
            $invite->update(['invitee_id' => $user->id]);

            \App\Models\Notification::create([
                'user_id' => $user->id,
                'sender_id' => $invite->inviter_id,
                'type' => 'invite',
                'title' => 'New Event Invitation',
                'message' => ($invite->inviter->name ?? 'Someone') . " has invited you to: " . $invite->event->title,
                'related_id' => $invite->id,
            ]);
        }

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="relative min-h-screen w-full flex flex-col bg-slate-50 font-serif">
    <header class="flex items-center justify-between px-8 md:px-20 py-6 absolute top-0 w-full z-20">
        <div class="flex items-center gap-3">
            <x-application-logo class="w-10 h-10 text-indigo-600" />
            <span class="text-xl font-bold text-slate-800 tracking-tight">Pearl Pavilion</span>
        </div>
        <nav class="flex items-center gap-8">
            <a class="text-xs font-bold text-slate-500 hover:text-indigo-600 uppercase tracking-widest transition-colors"
                href="{{ route('home') }}" wire:navigate>Home</a>
            <a class="btn-lux px-6 py-2 text-xs"
                href="{{ route('login') }}" wire:navigate>Login</a>
        </nav>
    </header>

    <main class="flex-1 flex items-center justify-center px-4 pt-24 pb-12">
        <div class="w-full max-w-[480px] bg-white border border-slate-100 rounded-lg p-10 md:p-12 shadow-2xl shadow-slate-200/50">
            <div class="mb-10 text-center">
                <h1 class="text-slate-900 text-3xl font-bold mb-3">Register</h1>
                <p class="text-slate-500 text-sm italic">Create your individual account</p>
            </div>

            <form wire:submit="register" class="space-y-6">
                <!-- Name -->
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Full Name</label>
                    <input wire:model="name"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-lg text-slate-900 placeholder:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all font-sans text-sm"
                        placeholder="Enter your full name" type="text" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email Address -->
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Email Address</label>
                    <input wire:model="email"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-lg text-slate-900 placeholder:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all font-sans text-sm"
                        placeholder="name@example.com" type="email" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Password</label>
                        <input wire:model="password"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-100 rounded-lg text-slate-900 placeholder:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all font-sans text-sm"
                            placeholder="••••••••" type="password" required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Confirm</label>
                        <input wire:model="password_confirmation"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-100 rounded-lg text-slate-900 placeholder:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all font-sans text-sm"
                            placeholder="••••••••" type="password" required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>
                </div>

                <div class="pt-2">
                    <button class="btn-lux w-full py-4 text-xs" type="submit">
                        <span wire:loading.remove>Register</span>
                        <span wire:loading>Joining...</span>
                    </button>
                </div>
            </form>

            <div class="mt-10 pt-8 border-t border-slate-50 text-center">
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">
                    Already have an account?
                    <a class="text-indigo-600 hover:text-indigo-700 ml-1 transition-colors underline underline-offset-4"
                        href="{{ route('login') }}" wire:navigate>Login</a>
                </p>
            </div>
        </div>
    </main>

    <footer class="w-full py-8 text-center">
        <p class="text-slate-400 text-[9px] font-bold uppercase tracking-[0.2em]">
            © 2026 Pearl Pavilion. Professional Event Coordination.
        </p>
    </footer>
</div>
