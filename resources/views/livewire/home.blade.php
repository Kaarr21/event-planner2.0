<div>
    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-50 w-full border-b border-slate-100 bg-white/90 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <x-application-logo class="w-10 h-10 text-indigo-600" />
                <span class="text-xl font-serif font-bold text-slate-900 tracking-tight">Pearl Pavilion</span>
            </div>
            <nav class="hidden md:flex items-center gap-10">
                <a class="text-xs font-bold text-slate-600 hover:text-indigo-600 uppercase tracking-widest transition-colors" href="{{ route('home') }}">Home</a>
                <a class="text-xs font-bold text-slate-600 hover:text-indigo-600 uppercase tracking-widest transition-colors" href="{{ route('events.discovery') }}">Discover</a>
                <a class="text-xs font-bold text-slate-600 hover:text-indigo-600 uppercase tracking-widest transition-colors" href="{{ route('about') }}">About</a>
                <a class="text-xs font-bold text-slate-600 hover:text-indigo-600 uppercase tracking-widest transition-colors" href="{{ route('contact') }}">Contact</a>
            </nav>
            <div class="flex items-center gap-4">
                <a href="{{ route('login') }}" class="text-xs font-bold text-slate-500 hover:text-indigo-600 uppercase tracking-widest transition-colors px-4">Login</a>
                <a href="{{ route('register') }}" class="btn-lux px-6 py-2 text-xs">
                    Register
                </a>
            </div>
        </div>
    </header>

    <main>
        <!-- Hero Section -->
        <section class="relative min-h-[80vh] flex items-center justify-center overflow-hidden bg-slate-50">
            <div class="absolute inset-0 z-0">
                <div class="absolute inset-0 bg-slate-900/10"></div>
                <img alt="Premium Event Background" class="w-full h-full object-cover"
                    src="{{ asset('images/site/home_hero.png') }}" />
                <div class="absolute inset-0 bg-gradient-to-r from-white via-white/40 to-transparent"></div>
            </div>
            <div class="relative z-10 max-w-7xl w-full mx-auto px-6">
                <div class="max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-indigo-50 border border-indigo-100 text-indigo-600 text-[10px] font-bold uppercase tracking-widest mb-8">
                        <span class="flex h-1.5 w-1.5 rounded-full bg-indigo-600"></span>
                        Quality Service
                    </div>
                    <h1 class="text-5xl md:text-7xl font-serif font-bold text-slate-900 leading-tight mb-8">
                        Plan Your <span class="text-indigo-600 italic">Perfect</span> Event
                    </h1>
                    <p class="text-lg text-slate-600 max-w-lg mb-12 leading-relaxed">
                        Experience simple and professional event coordination for your next gathering.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center gap-4">
                        <a href="{{ route('register') }}" class="btn-lux px-10 py-4 text-sm w-full sm:w-auto text-center">
                            Get Started
                        </a>
                        <a href="{{ route('about') }}" class="w-full sm:w-auto px-10 py-4 bg-white/80 backdrop-blur-md border border-slate-200 text-slate-600 hover:text-slate-900 rounded-lg text-xs font-bold uppercase tracking-widest transition-all text-center">
                            Learn More
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Discovery Preview Section -->
        @if($featuredEvents->isNotEmpty())
            <section class="py-24 bg-white overflow-hidden relative">
                <div class="max-w-7xl mx-auto px-6 relative z-10">
                    <div class="flex flex-col md:flex-row justify-between items-end gap-6 mb-16">
                        <div class="max-w-2xl">
                            <h3 class="text-indigo-600 font-bold text-[10px] uppercase tracking-widest mb-4">Recent Events</h3>
                            <h2 class="text-4xl font-serif font-bold text-slate-900 leading-tight">Shared <span class="text-indigo-600">Events</span> Near You</h2>
                        </div>
                        <a href="{{ route('events.discovery') }}" class="group flex items-center gap-3 text-slate-400 hover:text-indigo-600 font-bold text-xs uppercase tracking-widest transition-all">
                            View All Events
                            <span class="material-symbols-outlined text-lg transition-transform group-hover:translate-x-1">arrow_forward</span>
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        @foreach($featuredEvents as $event)
                            <a href="{{ route('events.show', $event) }}" class="group block bg-white border border-slate-100 rounded-lg overflow-hidden transition-all hover:shadow-xl hover:border-indigo-100">
                                <div class="relative h-64 overflow-hidden">
                                    <img 
                                        src="{{ $event->banner_image_path ? Storage::url($event->banner_image_path) : 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?q=80&w=2070&auto=format&fit=crop' }}" 
                                        alt="{{ $event->title }}"
                                        class="w-full h-full object-cover grayscale-[20%] group-hover:grayscale-0 transition-all duration-700"
                                    >
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/40 via-transparent to-transparent"></div>
                                    
                                    <div class="absolute top-6 left-6">
                                        <span class="px-4 py-1.5 rounded-lg bg-white/90 backdrop-blur-md text-slate-900 text-[9px] font-bold uppercase tracking-widest shadow-sm">
                                            {{ $event->category->name ?? 'Special' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="p-8">
                                    <div class="flex items-center gap-2 mb-4 text-indigo-600">
                                        <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                                        <span class="text-[10px] font-bold uppercase tracking-widest">
                                            {{ $event->start_at ? $event->start_at->format('M d, Y') : ($event->date ? $event->date->format('M d, Y') : 'Date Pending') }}
                                        </span>
                                    </div>

                                    <h3 class="text-xl font-serif font-bold text-slate-900 mb-6 line-clamp-1 group-hover:text-indigo-600 transition-colors">
                                        {{ $event->title }}
                                    </h3>

                                    <div class="flex items-center justify-between pt-6 border-t border-slate-50">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center overflow-hidden">
                                                @if($event->creator->profile_photo_url)
                                                    <img src="{{ $event->creator->profile_photo_url }}" alt="{{ $event->creator->name }}" class="w-full h-full object-cover">
                                                @else
                                                    <span class="material-symbols-outlined text-slate-400 text-lg">person</span>
                                                @endif
                                            </div>
                                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ $event->creator->name }}</span>
                                        </div>
                                        @if($event->ticketTypes->isNotEmpty())
                                            <div class="text-slate-900 font-serif font-bold text-sm">
                                                @if($event->ticketTypes->min('price') == 0)
                                                    FREE
                                                @else
                                                    KES {{ number_format($event->ticketTypes->min('price')) }}
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <!-- CTA Action Section -->
        <section class="py-24 bg-white border-y border-slate-100">
            <div class="max-w-7xl mx-auto px-6">
                <div class="bg-indigo-600 rounded-lg p-12 md:p-20 relative overflow-hidden flex flex-col items-center text-center shadow-2xl shadow-indigo-100">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 blur-3xl rounded-full translate-x-1/2 -translate-y-1/2"></div>
                    <div class="absolute bottom-0 left-0 w-64 h-64 bg-indigo-500/20 blur-3xl rounded-full -translate-x-1/2 translate-y-1/2"></div>
                    
                    <h2 class="text-3xl md:text-5xl font-serif font-bold mb-6 text-white leading-tight">Ready to plan your next event?</h2>
                    <p class="text-indigo-100 text-lg mb-12 max-w-2xl leading-relaxed">Join thousands of planners and individuals using our tools to create perfect moments.</p>
                    
                    <div class="flex flex-col sm:flex-row justify-center gap-4 w-full max-w-md">
                        <a href="{{ route('register') }}" class="w-full sm:w-auto px-10 py-4 bg-white text-indigo-600 font-bold text-xs uppercase tracking-widest rounded-lg hover:bg-indigo-50 transition-all text-center">
                            Get Started
                        </a>
                        <a href="{{ route('login') }}" class="w-full sm:w-auto px-10 py-4 bg-indigo-500 text-white font-bold text-xs uppercase tracking-widest rounded-lg border border-indigo-400 hover:bg-indigo-400 transition-all text-center">
                            Login
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Key Highlights Section -->
        <section class="py-24 bg-white">
            <div class="max-w-7xl mx-auto px-6">
                <div class="flex flex-col md:flex-row justify-between items-end gap-6 mb-16">
                    <div class="max-w-2xl">
                        <h3 class="text-indigo-600 font-bold text-[10px] uppercase tracking-widest mb-4">Features</h3>
                        <h2 class="text-4xl font-serif font-bold leading-tight text-slate-900">Easy Planning for Professional Events</h2>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="p-10 rounded-lg border border-slate-100 hover:border-indigo-100 transition-all group">
                        <div class="w-12 h-12 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 mb-8">
                            <span class="material-symbols-outlined text-2xl">groups</span>
                        </div>
                        <h4 class="text-xl font-serif font-bold mb-4 text-slate-900">Guest List</h4>
                        <p class="text-slate-500 text-sm leading-relaxed mb-6">Effortlessly organize your guest list with simple tools for tagging and tracking.</p>
                        <a class="text-indigo-600 font-bold text-[10px] uppercase tracking-widest flex items-center gap-2" href="#">
                            Learn more <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                    <!-- Feature 2 -->
                    <div class="p-10 rounded-lg border border-slate-100 hover:border-indigo-100 transition-all group shadow-2xl shadow-slate-100 bg-white">
                        <div class="w-12 h-12 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 mb-8">
                            <span class="material-symbols-outlined text-2xl">fact_check</span>
                        </div>
                        <h4 class="text-xl font-serif font-bold mb-4 text-slate-900">Status Updates</h4>
                        <p class="text-slate-500 text-sm leading-relaxed mb-6">Get real-time updates on attendee status with simple tracking and reminders.</p>
                        <a class="text-indigo-600 font-bold text-[10px] uppercase tracking-widest flex items-center gap-2" href="#">
                            Learn more <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                    <!-- Feature 3 -->
                    <div class="p-10 rounded-lg border border-slate-100 hover:border-indigo-100 transition-all group">
                        <div class="w-12 h-12 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 mb-8">
                            <span class="material-symbols-outlined text-2xl">mail</span>
                        </div>
                        <h4 class="text-xl font-serif font-bold mb-4 text-slate-900">Email Invites</h4>
                        <p class="text-slate-500 text-sm leading-relaxed mb-6">Send professional digital invites that match your event's theme perfectly.</p>
                        <a class="text-indigo-600 font-bold text-[10px] uppercase tracking-widest flex items-center gap-2" href="#">
                            Learn more <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="py-24 bg-slate-50">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                    <div class="text-center">
                        <div class="text-4xl md:text-5xl font-serif font-bold text-indigo-600 mb-2">500k+</div>
                        <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Events Planned</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl md:text-5xl font-serif font-bold text-indigo-600 mb-2">12M+</div>
                        <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Guests Managed</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl md:text-5xl font-serif font-bold text-indigo-600 mb-2">99%</div>
                        <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Success Rate</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl md:text-5xl font-serif font-bold text-indigo-600 mb-2">24/7</div>
                        <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Support</div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-indigo-950 pt-20 pb-10">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-20">
                <div class="col-span-1 md:col-span-1">
                    <div class="flex items-center gap-3 mb-6">
                        <x-application-logo class="w-10 h-10 text-white" />
                        <span class="text-xl font-serif font-bold text-white tracking-tight">Pearl Pavilion</span>
                    </div>
                    <p class="text-indigo-200 text-sm leading-relaxed max-w-xs">
                        A professional platform for organizing and managing events with simple and elegant tools.
                    </p>
                </div>
                <div>
                    <h5 class="text-white text-xs font-bold uppercase tracking-widest mb-8">Platform</h5>
                    <ul class="space-y-4">
                        <li><a class="text-indigo-300 hover:text-white text-xs font-bold transition-colors" href="#">How it Works</a></li>
                        <li><a class="text-indigo-300 hover:text-white text-xs font-bold transition-colors" href="#">Features</a></li>
                        <li><a class="text-indigo-300 hover:text-white text-xs font-bold transition-colors" href="#">Pricing</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="text-white text-xs font-bold uppercase tracking-widest mb-8">Company</h5>
                    <ul class="space-y-4">
                        <li><a class="text-indigo-300 hover:text-white text-xs font-bold transition-colors" href="{{ route('about') }}">About Us</a></li>
                        <li><a class="text-indigo-300 hover:text-white text-xs font-bold transition-colors" href="{{ route('contact') }}">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="text-white text-xs font-bold uppercase tracking-widest mb-8">Stay Updated</h5>
                    <p class="text-indigo-300 text-xs mb-6">Join our newsletter for the latest event trends.</p>
                    <div class="flex gap-2">
                        <input class="bg-indigo-900 border-none rounded-lg px-4 py-3 w-full text-xs text-white placeholder-indigo-400 focus:ring-1 focus:ring-indigo-500" placeholder="Email Address" type="email" />
                        <button class="bg-white p-3 rounded-lg text-indigo-950 hover:bg-indigo-50 transition-all">
                            <span class="material-symbols-outlined text-xl">send</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="border-t border-indigo-900 pt-10 flex flex-col md:flex-row justify-between items-center gap-6">
                <p class="text-indigo-400 text-[10px] font-bold uppercase tracking-widest">© 2026 Pearl Pavilion. All rights reserved.</p>
                <div class="flex gap-8">
                    <a class="text-indigo-400 hover:text-white transition-colors" href="#"><span class="material-symbols-outlined text-xl">public</span></a>
                    <a class="text-indigo-400 hover:text-white transition-colors" href="#"><span class="material-symbols-outlined text-xl">alternate_email</span></a>
                    <a class="text-indigo-400 hover:text-white transition-colors" href="#"><span class="material-symbols-outlined text-xl">share</span></a>
                </div>
            </div>
        </div>
    </footer>

</div>