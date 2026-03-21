<div>
    <header
        class="sticky top-0 z-50 w-full border-b border-white/10 bg-[#101722]/80 backdrop-blur-md px-6 md:px-20 py-4">
        <div class="max-w-7xl mx-auto flex items-center justify-between text-white">
            <div class="flex items-center gap-3">
                <x-application-logo class="w-10 h-10" />
            </div>
            <nav class="hidden md:flex items-center gap-10">
                <a class="text-sm font-medium hover:text-[#257bf4] transition-colors"
                    href="{{ route('home') }}">Home</a>
                <a class="text-sm font-medium hover:text-[#257bf4] transition-colors"
                    href="{{ route('about') }}">About</a>
                <a class="text-sm font-medium text-[#257bf4]" href="{{ route('contact') }}">Contact</a>
            </nav>
            <div class="flex items-center gap-4">
                <a href="{{ route('register') }}"
                    class="bg-[#257bf4] hover:bg-[#257bf4]/90 text-white text-sm font-bold px-6 py-2 rounded-xl transition-all">
                    Get Started
                </a>
                <a href="{{ route('login') }}"
                    class="text-sm font-medium hover:text-[#257bf4] transition-colors">Login</a>
            </div>
        </div>
    </header>

    <main class="relative flex-1 flex items-center justify-center py-16 px-4 md:px-20 overflow-hidden">
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-gradient-to-br from-[#101722]/90 via-[#101722]/70 to-[#257bf4]/20"></div>
            <img class="h-full w-full object-cover grayscale-[0.5] opacity-30"
                src="https://images.unsplash.com/photo-1511795409834-ef04bbd61622?q=80&w=2069&auto=format&fit=crop" />
        </div>
        <div class="relative z-10 w-full max-w-6xl grid lg:grid-cols-5 gap-8 items-stretch text-white">
            <div
                class="lg:col-span-3 bg-white/5 backdrop-blur-xl border border-white/10 rounded-xl p-8 md:p-12 shadow-2xl">
                <div class="mb-8">
                    <h1
                        class="text-4xl md:text-5xl font-black mb-4 bg-clip-text text-transparent bg-gradient-to-r from-white to-slate-400">
                        Let's Create Magic.</h1>
                    <p class="text-slate-400 text-lg">Have an upcoming premium event? Fill out the form and our
                        concierge team will be in touch within 24 hours.</p>
                </div>

                @if ($submitted)
                    <div class="bg-green-500/10 border border-green-500/20 text-green-500 p-6 rounded-xl mb-8">
                        <p class="font-bold flex items-center gap-2">
                            <span class="material-symbols-outlined">check_circle</span>
                            Thank you! Your message has been sent.
                        </p>
                    </div>
                @endif

                <form wire:submit="submit" class="space-y-6">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold text-slate-300 ml-1">Full Name</label>
                            <input wire:model="name"
                                class="w-full bg-white/5 border border-white/10 rounded-xl px-5 py-4 focus:ring-2 focus:ring-[#257bf4] focus:border-transparent outline-none transition-all placeholder:text-slate-500 text-white"
                                placeholder="John Doe" type="text" required />
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold text-slate-300 ml-1">Email Address</label>
                            <input wire:model="email"
                                class="w-full bg-white/5 border border-white/10 rounded-xl px-5 py-4 focus:ring-2 focus:ring-[#257bf4] focus:border-transparent outline-none transition-all placeholder:text-slate-500 text-white"
                                placeholder="john@example.com" type="email" required />
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-semibold text-slate-300 ml-1">Phone Number</label>
                        <input wire:model="phone"
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-5 py-4 focus:ring-2 focus:ring-[#257bf4] focus:border-transparent outline-none transition-all placeholder:text-slate-500 text-white"
                            placeholder="0746457368" type="tel" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-semibold text-slate-300 ml-1">Your Vision</label>
                        <textarea wire:model="message"
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-5 py-4 focus:ring-2 focus:ring-[#257bf4] focus:border-transparent outline-none transition-all placeholder:text-slate-500 resize-none text-white"
                            placeholder="Tell us about your dream event..." rows="4" required></textarea>
                    </div>
                    <button
                        class="w-full bg-[#257bf4] hover:bg-[#257bf4]/90 text-white font-bold py-5 rounded-xl text-lg shadow-lg shadow-[#257bf4]/20 transition-all flex items-center justify-center gap-2 group"
                        type="submit">
                        Send Message
                        <span
                            class="material-symbols-outlined group-hover:translate-x-1 transition-transform">send</span>
                    </button>
                </form>
            </div>
            <div class="lg:col-span-2 flex flex-col gap-6">
                <div
                    class="flex-1 bg-white/5 backdrop-blur-xl border border-white/10 rounded-xl p-8 flex flex-col justify-between shadow-xl">
                    <div>
                        <h3 class="text-2xl font-bold mb-8">Contact Information</h3>
                        <div class="space-y-8">
                            <div class="flex items-start gap-4">
                                <div class="p-3 rounded-lg bg-[#257bf4]/10 text-[#257bf4] border border-[#257bf4]/20">
                                    <span class="material-symbols-outlined">business_center</span>
                                </div>
                                <div>
                                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-1">Company
                                    </p>
                                    <p class="text-lg font-semibold">Event Planner</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="p-3 rounded-lg bg-[#257bf4]/10 text-[#257bf4] border border-[#257bf4]/20">
                                    <span class="material-symbols-outlined">mail</span>
                                </div>
                                <div>
                                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-1">Email</p>
                                    <p class="text-lg font-semibold text-[#257bf4]">karokin35@gmail.com</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="p-3 rounded-lg bg-[#257bf4]/10 text-[#257bf4] border border-[#257bf4]/20">
                                    <span class="material-symbols-outlined">call</span>
                                </div>
                                <div>
                                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-1">Phone</p>
                                    <p class="text-lg font-semibold text-slate-200">0746457368</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-12">
                        <p class="text-slate-400 mb-4 text-white">Follow our journey</p>
                        <div class="flex gap-4">
                            <a class="size-10 rounded-full bg-white/5 border border-white/10 flex items-center justify-center hover:bg-[#257bf4] transition-colors"
                                href="#"><span class="material-symbols-outlined text-sm">public</span></a>
                            <a class="size-10 rounded-full bg-white/5 border border-white/10 flex items-center justify-center hover:bg-[#257bf4] transition-colors"
                                href="#"><span class="material-symbols-outlined text-sm">share</span></a>
                            <a class="size-10 rounded-full bg-white/5 border border-white/10 flex items-center justify-center hover:bg-[#257bf4] transition-colors"
                                href="#"><span class="material-symbols-outlined text-sm">camera</span></a>
                        </div>
                    </div>
                </div>
                <div class="h-48 rounded-xl bg-white/5 border border-white/10 overflow-hidden relative shadow-xl">
                    <div class="absolute inset-0 grayscale opacity-40 hover:opacity-70 transition-opacity duration-500 bg-cover bg-center"
                        style="background-image: url('https://images.unsplash.com/photo-1524666041070-9d87656c25bb?q=80&w=2070&auto=format&fit=crop')">
                    </div>
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <span class="material-symbols-outlined text-[#257bf4] text-4xl">location_on</span>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <footer class="w-full border-t border-slate-200/10 bg-[#101722] px-6 md:px-20 py-12">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-8 text-white">
            <div class="flex items-center gap-3">
                <x-application-logo class="w-8 h-8" />
            </div>
            <p class="text-slate-500 text-sm">© 2024 Event Planner. All rights reserved.</p>
            <div class="flex gap-8 text-sm font-medium text-slate-400">
                <a class="hover:text-[#257bf4] transition-colors" href="#">Privacy Policy</a>
                <a class="hover:text-[#257bf4] transition-colors" href="#">Terms of Service</a>
            </div>
        </div>
    </footer>
</div>