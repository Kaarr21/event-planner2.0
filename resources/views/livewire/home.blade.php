<div>
    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-50 w-full border-b border-slate-200 dark:border-slate-800 bg-background-light/80 dark:bg-[#101722]/80 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="bg-[#257bf4] p-2 rounded-lg text-white">
                    <span class="material-symbols-outlined text-2xl">celebration</span>
                </div>
                <h2 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">Event Planner</h2>
            </div>
            <nav class="hidden md:flex items-center gap-10 text-white">
                <a class="text-sm font-medium hover:text-[#257bf4] transition-colors" href="{{ route('home') }}">Home</a>
                <a class="text-sm font-medium hover:text-[#257bf4] transition-colors" href="{{ route('about') }}">About Us</a>
                <a class="text-sm font-medium hover:text-[#257bf4] transition-colors" href="{{ route('contact') }}">Contact</a>
            </nav>
            <div class="flex items-center gap-4">
                <a href="{{ route('login') }}" class="px-5 py-2 text-sm font-bold text-slate-700 dark:text-slate-200 hover:text-[#257bf4] transition-colors">Login</a>
                <a href="{{ route('register') }}" class="bg-[#257bf4] hover:bg-[#257bf4]/90 text-white px-6 py-2.5 rounded-lg text-sm font-bold shadow-lg shadow-[#257bf4]/20 transition-all">
                    Register
                </a>
            </div>
        </div>
    </header>

    <main>
        <!-- Hero Section -->
        <section class="relative min-h-[85vh] flex items-center justify-center overflow-hidden">
            <div class="absolute inset-0 z-0">
                <div class="absolute inset-0 bg-gradient-to-b from-[#101722]/40 via-[#101722]/80 to-[#101722]"></div>
                <img alt="Premium Event Background" class="w-full h-full object-cover" src="https://images.unsplash.com/photo-1511795409834-ef04bbd61622?q=80&w=2069&auto=format&fit=crop"/>
            </div>
            <div class="relative z-10 max-w-4xl mx-auto px-6 text-center">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#257bf4]/10 border border-[#257bf4]/20 text-[#257bf4] text-xs font-bold uppercase tracking-widest mb-8">
                    <span class="flex h-2 w-2 rounded-full bg-[#257bf4] animate-pulse"></span>
                    Redefining Excellence
                </div>
                <h1 class="text-5xl md:text-7xl font-black text-white leading-tight tracking-tight mb-8">
                    Crafting <span class="text-[#257bf4]">Unforgettable</span> Moments with Ease
                </h1>
                <p class="text-lg md:text-xl text-slate-300 max-w-2xl mx-auto mb-12 leading-relaxed">
                    Experience the ultimate in premium event coordination with our sophisticated planning suite designed for high-profile gatherings.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('register') }}" class="w-full sm:w-auto bg-[#257bf4] hover:bg-[#257bf4]/90 text-white px-10 py-4 rounded-xl text-lg font-bold shadow-xl shadow-[#257bf4]/30 transition-all text-center">
                        Get Started
                    </a>
                    <a href="{{ route('about') }}" class="w-full sm:w-auto glass hover:bg-white/10 text-white px-10 py-4 rounded-xl text-lg font-bold transition-all border border-white/20 text-center">
                        Learn More
                    </a>
                </div>
            </div>
        </section>

        <!-- CTA Action Section -->
        <section class="py-24 bg-background-light dark:bg-[#101722] border-y border-slate-200 dark:border-slate-800">
            <div class="max-w-7xl mx-auto px-6">
                <div class="glass-card rounded-3xl p-12 md:p-20 relative overflow-hidden flex flex-col items-center text-center">
                    <div class="absolute -top-24 -right-24 w-64 h-64 bg-[#257bf4]/10 blur-[100px] rounded-full"></div>
                    <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-[#257bf4]/5 blur-[100px] rounded-full"></div>
                    <h2 class="text-3xl md:text-5xl font-bold mb-6 text-white">Ready to plan your next event?</h2>
                    <p class="text-slate-400 text-lg mb-12 max-w-2xl">Join thousands of professional event planners and individuals using our tools to create magic.</p>
                    <div class="flex flex-wrap justify-center gap-6 w-full max-w-md">
                        <a href="{{ route('login') }}" class="flex-1 min-w-[180px] py-5 px-8 rounded-2xl glass border-[#257bf4]/30 text-white font-bold text-lg hover:bg-[#257bf4]/20 transition-all text-center">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="flex-1 min-w-[180px] py-5 px-8 rounded-2xl bg-[#257bf4] text-white font-bold text-lg hover:shadow-2xl hover:shadow-[#257bf4]/40 transition-all text-center">
                            Register
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Key Highlights Section -->
        <section class="py-32 relative">
            <div class="max-w-7xl mx-auto px-6">
                <div class="flex flex-col md:flex-row justify-between items-end gap-6 mb-20">
                    <div class="max-w-2xl">
                        <h3 class="text-[#257bf4] font-bold text-sm tracking-[0.2em] uppercase mb-4">Features</h3>
                        <h2 class="text-4xl md:text-5xl font-bold leading-tight text-white">Seamless Coordination for Elite Events</h2>
                    </div>
                    <p class="text-slate-400 max-w-sm mb-2">Everything you need to manage high-end events in one sophisticated, unified workspace.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Card 1 -->
                    <div class="glass-card p-10 rounded-[2rem] hover:border-[#257bf4]/40 transition-all group">
                        <div class="w-16 h-16 rounded-2xl bg-[#257bf4]/10 flex items-center justify-center text-[#257bf4] mb-8 group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-3xl">groups</span>
                        </div>
                        <h4 class="text-2xl font-bold mb-4 text-white">Manage Guests</h4>
                        <p class="text-slate-400 leading-relaxed mb-6">Effortlessly organize your VIP list with our glassmorphic interface and advanced tagging.</p>
                        <a class="text-[#257bf4] font-semibold flex items-center gap-2 group-hover:gap-3 transition-all" href="#">
                            Learn more <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                    <!-- Card 2 -->
                    <div class="glass-card p-10 rounded-[2rem] border-[#257bf4]/20 bg-[#257bf4]/[0.02] hover:border-[#257bf4]/40 transition-all group">
                        <div class="w-16 h-16 rounded-2xl bg-[#257bf4]/10 flex items-center justify-center text-[#257bf4] mb-8 group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-3xl">fact_check</span>
                        </div>
                        <h4 class="text-2xl font-bold mb-4 text-white">Track RSVP</h4>
                        <p class="text-slate-400 leading-relaxed mb-6">Real-time updates on attendee status with elegant tracking tools and automated reminders.</p>
                        <a class="text-[#257bf4] font-semibold flex items-center gap-2 group-hover:gap-3 transition-all" href="#">
                            Learn more <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                    <!-- Card 3 -->
                    <div class="glass-card p-10 rounded-[2rem] hover:border-[#257bf4]/40 transition-all group">
                        <div class="w-16 h-16 rounded-2xl bg-[#257bf4]/10 flex items-center justify-center text-[#257bf4] mb-8 group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-3xl">mail</span>
                        </div>
                        <h4 class="text-2xl font-bold mb-4 text-white">Custom Invitations</h4>
                        <p class="text-slate-400 leading-relaxed mb-6">Design stunning digital invites that match your event's aesthetic perfectly.</p>
                        <a class="text-[#257bf4] font-semibold flex items-center gap-2 group-hover:gap-3 transition-all" href="#">
                            Learn more <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="py-24 bg-slate-100 dark:bg-slate-900/50">
            <div class="max-w-7xl mx-auto px-6 text-white">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                    <div class="text-center">
                        <div class="text-4xl md:text-5xl font-black text-[#257bf4] mb-2">500k+</div>
                        <div class="text-sm font-bold uppercase tracking-widest text-slate-500">Events Planned</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl md:text-5xl font-black text-[#257bf4] mb-2">12M+</div>
                        <div class="text-sm font-bold uppercase tracking-widest text-slate-500">Guests Managed</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl md:text-5xl font-black text-[#257bf4] mb-2">99%</div>
                        <div class="text-sm font-bold uppercase tracking-widest text-slate-500">Success Rate</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl md:text-5xl font-black text-[#257bf4] mb-2">24/7</div>
                        <div class="text-sm font-bold uppercase tracking-widest text-slate-500">Elite Support</div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-background-light dark:bg-[#101722] border-t border-slate-200 dark:border-slate-800 pt-20 pb-10">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-20 text-white">
                <div class="col-span-1 md:col-span-1">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="bg-[#257bf4] p-2 rounded-lg text-white">
                            <span class="material-symbols-outlined text-xl">celebration</span>
                        </div>
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white">Event Planner</h2>
                    </div>
                    <p class="text-slate-400 leading-relaxed">
                        The world's most sophisticated platform for organizing and managing premium events of all scales.
                    </p>
                </div>
                <div>
                    <h5 class="font-bold mb-6">Platform</h5>
                    <ul class="space-y-4">
                        <li><a class="text-slate-400 hover:text-[#257bf4] transition-colors" href="#">How it Works</a></li>
                        <li><a class="text-slate-400 hover:text-[#257bf4] transition-colors" href="#">Features</a></li>
                        <li><a class="text-slate-400 hover:text-[#257bf4] transition-colors" href="#">Pricing</a></li>
                        <li><a class="text-slate-400 hover:text-[#257bf4] transition-colors" href="#">Security</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="font-bold mb-6">Company</h5>
                    <ul class="space-y-4">
                        <li><a class="text-slate-400 hover:text-[#257bf4] transition-colors" href="{{ route('about') }}">About Us</a></li>
                        <li><a class="text-slate-400 hover:text-[#257bf4] transition-colors" href="#">Careers</a></li>
                        <li><a class="text-slate-400 hover:text-[#257bf4] transition-colors" href="{{ route('contact') }}">Contact</a></li>
                        <li><a class="text-slate-400 hover:text-[#257bf4] transition-colors" href="#">Partners</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="font-bold mb-6">Stay Updated</h5>
                    <p class="text-slate-400 text-sm mb-4">Join our newsletter for the latest event trends.</p>
                    <div class="flex gap-2">
                        <input class="bg-slate-100 dark:bg-slate-800 border-none rounded-lg px-4 py-2 w-full text-sm focus:ring-2 focus:ring-[#257bf4] text-white" placeholder="Email" type="email"/>
                        <button class="bg-[#257bf4] p-2 rounded-lg text-white">
                            <span class="material-symbols-outlined">send</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="border-t border-slate-200 dark:border-slate-800 pt-10 flex flex-col md:flex-row justify-between items-center gap-6">
                <p class="text-slate-500 text-sm">© 2024 Event Planner Inc. All rights reserved.</p>
                <div class="flex gap-8">
                    <a class="text-slate-500 hover:text-[#257bf4] transition-colors" href="#"><span class="material-symbols-outlined">public</span></a>
                    <a class="text-slate-500 hover:text-[#257bf4] transition-colors" href="#"><span class="material-symbols-outlined">alternate_email</span></a>
                    <a class="text-slate-500 hover:text-[#257bf4] transition-colors" href="#"><span class="material-symbols-outlined">share</span></a>
                </div>
            </div>
        </div>
    </footer>
</div>
