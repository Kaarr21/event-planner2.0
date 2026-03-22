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
    
    public string $account_type = 'individual';
    public string $organization_name = '';
    public string $organization_type = '';
    public string $organization_website = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'account_type' => ['required', 'string', 'in:individual,organization'],
            'organization_name' => ['required_if:account_type,organization', 'string', 'max:255', 'nullable'],
            'organization_type' => ['required_if:account_type,organization', 'string', 'max:255', 'nullable'],
            'organization_website' => ['nullable', 'string', 'url', 'max:255'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        if ($this->account_type === 'organization') {
            $slug = \Illuminate\Support\Str::slug($this->organization_name) . '-' . uniqid();
            
            $organization = \App\Models\Organization::create([
                'name' => $this->organization_name,
                'type' => $this->organization_type,
                'slug' => $slug,
                'website_url' => $this->organization_website,
            ]);

            $organization->members()->attach($user->id, ['role' => 'owner']);
        }

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

<div class="relative min-h-screen w-full flex flex-col bg-hero-blur"
    style="background-image: linear-gradient(rgba(16, 23, 34, 0.8), rgba(16, 23, 34, 0.95)), url(https://images.unsplash.com/photo-1511795409834-ef04bbd61622?q=80&w=2069&auto=format&fit=crop); background-size: cover; background-position: center;">
    <header class="w-full px-6 py-4">
        <nav
            class="max-w-7xl mx-auto flex items-center justify-between bg-[#101722]/60 backdrop-blur-[12px] border border-white/10 px-8 py-3 rounded-full text-white">
            <div class="flex items-center gap-3">
                <x-application-logo class="w-10 h-10" />
            </div>
            <div class="flex items-center gap-8">
                <div class="hidden md:flex items-center gap-8">
                    <a class="text-sm font-medium hover:text-[#257bf4] transition-colors" href="{{ route('home') }}"
                        wire:navigate>Home</a>
                    <a class="text-sm font-medium hover:text-[#257bf4] transition-colors" href="#">Pricing</a>
                    <a class="text-sm font-medium hover:text-[#257bf4] transition-colors" href="#">Portfolio</a>
                </div>
                <div class="h-6 w-px bg-slate-700 mx-2"></div>
                <div class="flex items-center gap-4">
                    <a class="text-sm font-medium hover:text-[#257bf4] transition-colors" href="{{ route('login') }}"
                        wire:navigate>Login</a>
                    <a href="{{ route('register') }}"
                        class="bg-[#257bf4] hover:bg-[#257bf4]/90 text-white px-6 py-2 rounded-full text-sm font-bold transition-all shadow-lg shadow-[#257bf4]/20">
                        Get Started
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <main class="flex-1 flex items-center justify-center p-6">
        <div
            class="w-full max-w-[520px] bg-[#101722]/60 backdrop-blur-[12px] border border-white/10 rounded-xl p-8 md:p-12 shadow-2xl text-white">
            <div class="mb-10 text-center">
                <h1 class="text-3xl font-black mb-2 tracking-tight">Join Event Planner</h1>
                <p class="text-slate-400 font-normal">Create your premium account to start planning gala events.</p>
            </div>

            <form wire:submit="register" class="space-y-5">
                <!-- Name -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-300 ml-1">Full Name</label>
                    <div class="relative group">
                        <span
                            class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-[#257bf4] transition-colors">person</span>
                        <input wire:model="name"
                            class="w-full bg-slate-900/50 border border-slate-700/50 rounded-xl py-4 pl-12 pr-4 text-white placeholder:text-slate-500 focus:ring-2 focus:ring-[#257bf4]/50 focus:border-[#257bf4] outline-none transition-all"
                            placeholder="Enter your full name" type="text" required autofocus autocomplete="name" />
                    </div>
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email Address -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-300 ml-1">Email Address</label>
                    <div class="relative group">
                        <span
                            class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-[#257bf4] transition-colors">mail</span>
                        <input wire:model="email"
                            class="w-full bg-slate-900/50 border border-slate-700/50 rounded-xl py-4 pl-12 pr-4 text-white placeholder:text-slate-500 focus:ring-2 focus:ring-[#257bf4]/50 focus:border-[#257bf4] outline-none transition-all"
                            placeholder="name@company.com" type="email" required autocomplete="username" />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-300 ml-1">Password</label>
                    <div class="relative group">
                        <span
                            class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-[#257bf4] transition-colors">lock</span>
                        <input wire:model="password"
                            class="w-full bg-slate-900/50 border border-slate-700/50 rounded-xl py-4 pl-12 pr-12 text-white placeholder:text-slate-500 focus:ring-2 focus:ring-[#257bf4]/50 focus:border-[#257bf4] outline-none transition-all"
                            placeholder="Create a strong password" type="password" required
                            autocomplete="new-password" />
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-300 ml-1">Confirm Password</label>
                    <div class="relative group">
                        <span
                            class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-[#257bf4] transition-colors">lock_reset</span>
                        <input wire:model="password_confirmation"
                            class="w-full bg-slate-900/50 border border-slate-700/50 rounded-xl py-4 pl-12 pr-4 text-white placeholder:text-slate-500 focus:ring-2 focus:ring-[#257bf4]/50 focus:border-[#257bf4] outline-none transition-all"
                            placeholder="Repeat your password" type="password" required autocomplete="new-password" />
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <!-- Account Type -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-300 ml-1">Account Type</label>
                    <div class="flex items-center space-x-6 mt-2">
                        <label class="flex items-center text-slate-300 cursor-pointer">
                            <input type="radio" wire:model.live="account_type" value="individual"
                                class="border-slate-700 bg-slate-900/50 text-[#257bf4] focus:ring-[#257bf4]/50 rounded-full" />
                            <span class="ml-2 text-sm">Individual Attendee</span>
                        </label>
                        <label class="flex items-center text-slate-300 cursor-pointer">
                            <input type="radio" wire:model.live="account_type" value="organization"
                                class="border-slate-700 bg-slate-900/50 text-[#257bf4] focus:ring-[#257bf4]/50 rounded-full" />
                            <span class="ml-2 text-sm">Organization / Planner</span>
                        </label>
                    </div>
                </div>

                @if ($account_type === 'organization')
                    <div class="p-6 bg-slate-800/40 border border-[#257bf4]/20 rounded-xl space-y-5">
                        <h3 class="text-white font-semibold text-sm uppercase tracking-wide">Organization Details</h3>
                        
                        <!-- Organization Name -->
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-300 ml-1">Organization Name</label>
                            <input wire:model="organization_name"
                                class="w-full bg-slate-900/50 border border-slate-700/50 rounded-xl py-3 px-4 text-white placeholder:text-slate-500 focus:ring-2 focus:ring-[#257bf4]/50 focus:border-[#257bf4] outline-none transition-all"
                                placeholder="Enter organization name" type="text" required />
                            <x-input-error :messages="$errors->get('organization_name')" class="mt-2" />
                        </div>

                        <!-- Organization Type -->
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-300 ml-1">Organization Type</label>
                            <select wire:model="organization_type"
                                class="w-full bg-slate-900/50 border border-slate-700/50 rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-[#257bf4]/50 focus:border-[#257bf4] outline-none transition-all"
                                required>
                                <option value="">Select Type</option>
                                <option value="Corporate">Corporate</option>
                                <option value="NGO">NGO</option>
                                <option value="Church">Church / Religious</option>
                                <option value="University">University / Education</option>
                                <option value="Community">Community Group</option>
                                <option value="Other">Other</option>
                            </select>
                            <x-input-error :messages="$errors->get('organization_type')" class="mt-2" />
                        </div>

                        <!-- Organization Website -->
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-300 ml-1">Website URL (Optional)</label>
                            <input wire:model="organization_website"
                                class="w-full bg-slate-900/50 border border-slate-700/50 rounded-xl py-3 px-4 text-white placeholder:text-slate-500 focus:ring-2 focus:ring-[#257bf4]/50 focus:border-[#257bf4] outline-none transition-all"
                                placeholder="https://..." type="url" />
                            <x-input-error :messages="$errors->get('organization_website')" class="mt-2" />
                        </div>
                    </div>
                @endif

                <!-- Submit Button -->
                <button
                    class="w-full bg-[#257bf4] hover:bg-[#257bf4]/90 text-white font-bold py-4 rounded-xl transition-all shadow-xl shadow-[#257bf4]/20 transform hover:-translate-y-0.5 mt-4"
                    type="submit">
                    Register Account
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="text-slate-400 text-sm">
                    Already have an account?
                    <a class="text-[#257bf4] font-bold hover:underline ml-1" href="{{ route('login') }}"
                        wire:navigate>Log in here</a>
                </p>
            </div>
        </div>
    </main>

    <footer class="w-full p-8 text-center">
        <p class="text-slate-500 text-xs tracking-widest uppercase">© 2024 Event Planner Premium. All Rights Reserved.
        </p>
    </footer>
</div>