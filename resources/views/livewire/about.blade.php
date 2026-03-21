<div>
    <!-- Navigation Bar -->
    <header class="sticky top-0 z-50 w-full border-b border-white/10 bg-[#101722]/80 backdrop-blur-md">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
            <div class="flex items-center gap-3">
                <x-application-logo class="w-10 h-10" />
            </div>
            <nav class="hidden md:flex items-center gap-10">
                <a class="text-sm font-medium text-slate-300 hover:text-[#257bf4] transition-colors"
                    href="{{ route('home') }}">Home</a>
                <a class="text-sm font-medium text-[#257bf4]" href="{{ route('about') }}">About Us</a>
                <a class="text-sm font-medium text-slate-300 hover:text-[#257bf4] transition-colors"
                    href="{{ route('contact') }}">Contact</a>
            </nav>
            <div class="flex items-center gap-4">
                <a href="{{ route('register') }}"
                    class="hidden sm:flex h-10 items-center justify-center rounded-lg bg-[#257bf4] px-6 text-sm font-bold text-white transition-all hover:bg-[#257bf4]/90 active:scale-95">
                    Get Started
                </a>
                <a href="{{ route('login') }}"
                    class="text-sm font-medium text-slate-300 hover:text-[#257bf4] transition-colors">Login</a>
            </div>
        </div>
    </header>

    <main class="flex-grow">
        <!-- Hero Section -->
        <section class="hero-gradient relative flex min-h-[60vh] items-center justify-center px-6 py-20 text-center">
            <div class="max-w-3xl">
                <span
                    class="mb-4 inline-block rounded-full bg-[#257bf4]/20 px-4 py-1.5 text-xs font-bold uppercase tracking-widest text-[#257bf4]">Established
                    2015</span>
                <h1 class="mb-6 text-5xl font-black tracking-tight text-white md:text-7xl">Who We Are</h1>
                <p class="text-lg leading-relaxed text-slate-300 md:text-xl">
                    Crafting unforgettable experiences with precision and passion. Event Planner is your dedicated
                    partner in bringing visions to life, blending creative innovation with flawless execution for every
                    milestone.
                </p>
                <div class="mt-10 flex flex-wrap justify-center gap-4">
                    <button
                        class="flex h-14 items-center justify-center rounded-xl bg-[#257bf4] px-8 text-base font-bold text-white shadow-lg shadow-[#257bf4]/20 transition-all hover:scale-105">
                        Our Story
                    </button>
                    <a href="{{ route('contact') }}"
                        class="flex h-14 items-center justify-center rounded-xl border border-white/20 bg-white/5 px-8 text-base font-bold text-white backdrop-blur-sm transition-all hover:bg-white/10 text-center">
                        Contact Us
                    </a>
                </div>
            </div>
        </section>

        <!-- Our Mission Section -->
        <section class="mx-auto max-w-7xl px-6 py-24">
            <div class="grid grid-cols-1 gap-16 lg:grid-cols-2 lg:items-center">
                <div>
                    <h2 class="mb-6 text-3xl font-bold tracking-tight text-white md:text-4xl text-white">Our Mission
                    </h2>
                    <div class="mb-8 h-1 w-20 rounded-full bg-[#257bf4]"></div>
                    <p class="mb-6 text-lg text-slate-400">
                        To simplify event planning through innovative technology and expert coordination, ensuring every
                        detail of your special occasion is executed flawlessly.
                    </p>
                    <p class="text-slate-400">
                        We believe that every event is a story waiting to be told. Our team of specialists works
                        tirelessly to remove the stress of logistics, allowing you to focus on what truly matters:
                        making memories.
                    </p>
                    <div class="mt-10 grid grid-cols-2 gap-6">
                        <div class="rounded-xl bg-white/5 p-6 border border-white/5">
                            <span class="material-symbols-outlined mb-3 text-3xl text-[#257bf4]">auto_awesome</span>
                            <h4 class="font-bold text-white">Innovation</h4>
                            <p class="text-sm text-slate-500">Leading the industry with smart tech solutions.</p>
                        </div>
                        <div class="rounded-xl bg-white/5 p-6 border border-white/5">
                            <span class="material-symbols-outlined mb-3 text-3xl text-[#257bf4]">groups</span>
                            <h4 class="font-bold text-white">Excellence</h4>
                            <p class="text-sm text-slate-500">Dedicated team of certified professionals.</p>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <div class="absolute -inset-4 rounded-2xl bg-[#257bf4]/10 blur-3xl"></div>
                    <img class="relative rounded-2xl border border-white/10 shadow-2xl"
                        src="https://images.unsplash.com/photo-1511795409834-ef04bbd61622?q=80&w=2069&auto=format&fit=crop" />
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section class="bg-slate-900/50 py-24">
            <div class="mx-auto max-w-7xl px-6">
                <div class="mb-16 text-center text-white">
                    <h2 class="text-3xl font-bold text-white md:text-4xl">Get in Touch</h2>
                    <p class="mt-4 text-slate-400">We're here to help you plan your next big event.</p>
                </div>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    <!-- Contact Card 1 -->
                    <div
                        class="glassmorphism flex flex-col items-center rounded-2xl p-10 text-center transition-transform hover:-translate-y-2">
                        <div
                            class="mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-[#257bf4]/20 text-[#257bf4]">
                            <span class="material-symbols-outlined text-3xl">business</span>
                        </div>
                        <h3 class="mb-2 text-xl font-bold text-white">Name</h3>
                        <p class="text-slate-400">Event Planner</p>
                    </div>
                    <!-- Contact Card 2 -->
                    <div
                        class="glassmorphism flex flex-col items-center rounded-2xl p-10 text-center transition-transform hover:-translate-y-2">
                        <div
                            class="mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-[#257bf4]/20 text-[#257bf4]">
                            <span class="material-symbols-outlined text-3xl">mail</span>
                        </div>
                        <h3 class="mb-2 text-xl font-bold text-white">Email</h3>
                        <a class="text-[#257bf4] hover:underline"
                            href="mailto:karokin35@gmail.com">karokin35@gmail.com</a>
                    </div>
                    <!-- Contact Card 3 -->
                    <div
                        class="glassmorphism flex flex-col items-center rounded-2xl p-10 text-center transition-transform hover:-translate-y-2">
                        <div
                            class="mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-[#257bf4]/20 text-[#257bf4]">
                            <span class="material-symbols-outlined text-3xl">call</span>
                        </div>
                        <h3 class="mb-2 text-xl font-bold text-white">Phone</h3>
                        <a class="text-slate-400 hover:text-white transition-colors"
                            href="tel:0746457368">0746457368</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="border-t border-white/10 bg-[#101722] py-12">
        <div class="mx-auto max-w-7xl px-6 text-white">
            <div class="flex flex-col items-center justify-between gap-8 md:flex-row">
                <div class="flex items-center gap-3 text-white">
                    <x-application-logo class="w-8 h-8" />
                </div>
                <div class="flex gap-8">
                    <a class="text-slate-500 hover:text-white" href="#"><span
                            class="material-symbols-outlined">public</span></a>
                    <a class="text-slate-500 hover:text-white" href="#"><span
                            class="material-symbols-outlined">share</span></a>
                    <a class="text-slate-500 hover:text-white" href="#"><span
                            class="material-symbols-outlined">alternate_email</span></a>
                </div>
                <p class="text-sm text-slate-500">
                    © 2024 Event Planner Inc. All rights reserved.
                </p>
            </div>
        </div>
    </footer>
</div>