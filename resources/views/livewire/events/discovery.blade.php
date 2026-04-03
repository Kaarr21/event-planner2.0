<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Hero Search Section -->
        <div class="relative rounded-3xl overflow-hidden mb-12 bg-brand-dark p-8 md:p-16 text-center">
            <div class="absolute inset-0 z-0 opacity-20">
                <img src="https://images.unsplash.com/photo-1492684223066-81342ee5ff30?q=80&w=2070&auto=format&fit=crop" alt="Discovery" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-b from-brand-dark/20 to-brand-dark"></div>
            </div>
            
            <div class="relative z-10 max-w-2xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-black text-white mb-6 tracking-tight">Discover Shared <span class="text-brand-orange">Experiences</span></h1>
                <p class="text-gray-300 mb-10 text-lg">Join incredible public events in your community or across the globe.</p>
                
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                    <input 
                        wire:model.live.debounce.300ms="search"
                        type="text" 
                        placeholder="Search for events, workshops, concerts..." 
                        class="w-full pl-12 pr-4 py-4 rounded-2xl border-none bg-white/10 backdrop-blur-md text-white placeholder-gray-400 focus:ring-2 focus:ring-brand-orange transition-all"
                    >
                </div>
            </div>
        </div>

        <!-- Filters & Sorting -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-6 mb-12">
            <div class="flex items-center gap-4 overflow-x-auto no-scrollbar w-full md:w-auto pb-2">
                <button 
                    wire:click="$set('categoryId', null)"
                    class="px-6 py-2.5 rounded-full text-sm font-bold transition-all whitespace-nowrap {{ !$categoryId ? 'bg-brand-orange text-white' : 'glass text-gray-400 hover:text-gray-200' }}"
                >
                    All Types
                </button>
                @foreach($categories as $category)
                    <button 
                        wire:click="$set('categoryId', {{ $category->id }})"
                        class="px-6 py-2.5 rounded-full text-sm font-bold transition-all whitespace-nowrap {{ $categoryId == $category->id ? 'bg-brand-orange text-white' : 'glass text-gray-400 hover:text-gray-200' }}"
                    >
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>

            <select wire:model.live="sort" class="glass text-white border-none rounded-xl px-6 py-2.5 font-bold text-sm focus:ring-brand-orange">
                <option value="upcoming" class="bg-brand-dark">Soonest First</option>
                <option value="latest" class="bg-brand-dark">Recently Added</option>
            </select>
        </div>

        <!-- Events Grid -->
        @if($events->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="w-24 h-24 bg-white/5 rounded-full flex items-center justify-center text-gray-500 mb-6">
                    <span class="material-symbols-outlined text-5xl">event_busy</span>
                </div>
                <h3 class="text-2xl font-bold text-white mb-2">No events found</h3>
                <p class="text-gray-400">Try adjusting your filters or search terms.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($events as $event)
                    <a href="{{ route('events.show', $event) }}" class="group block glass-card rounded-[2.5rem] overflow-hidden transition-all hover:-translate-y-2 hover:shadow-2xl hover:shadow-brand-orange/10 border-white/5">
                        <div class="relative h-64 overflow-hidden">
                            <img 
                                src="{{ $event->banner_image_path ? Storage::url($event->banner_image_path) : 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?q=80&w=2070&auto=format&fit=crop' }}" 
                                alt="{{ $event->title }}"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                            >
                            <div class="absolute inset-0 bg-gradient-to-t from-brand-dark via-brand-dark/20 to-transparent opacity-60"></div>
                            
                            <div class="absolute top-6 left-6">
                                <span class="px-4 py-1.5 rounded-full bg-brand-orange/90 backdrop-blur-md text-white text-[10px] font-black uppercase tracking-widest">
                                    {{ $event->category->name ?? 'Event' }}
                                </span>
                            </div>

                            <div x-data="{
                                target: new Date('{{ $event->start_at ? $event->start_at->toIso8601String() : ($event->date ? $event->date->toIso8601String() : '') }}').getTime(),
                                timeLeft: '',
                                update() {
                                    const now = new Date().getTime();
                                    const diff = this.target - now;

                                    if (isNaN(this.target)) return;

                                    if (diff <= 0) {
                                        this.timeLeft = 'Ongoing';
                                        return;
                                    }

                                    const hoursTotal = diff / (1000 * 60 * 60);

                                    if (hoursTotal >= 24) {
                                        const days = Math.ceil(diff / (1000 * 60 * 60 * 24));
                                        this.timeLeft = `Live in ${days} Days`;
                                    } else {
                                        const hours = Math.floor(diff / (1000 * 60 * 60));
                                        const mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                                        this.timeLeft = `${hours}h ${mins}m`;
                                    }
                                }
                            }" 
                            x-init="update(); setInterval(() => update(), 60000)"
                            class="absolute top-6 right-6"
                            >
                                <div x-show="timeLeft" 
                                     class="px-4 py-1.5 rounded-full bg-black/40 backdrop-blur-xl border border-white/10 text-white text-[10px] font-black uppercase tracking-[0.2em] shadow-lg flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[14px]">timer</span>
                                    <span x-text="timeLeft"></span>
                                </div>
                            </div>

                            @if($event->ticketTypes->isNotEmpty())
                                <div class="absolute bottom-6 right-6">
                                    <div class="glass px-4 py-1.5 rounded-full flex items-center gap-2">
                                        <span class="material-symbols-outlined text-brand-orange text-sm">payments</span>
                                        <span class="text-white text-[10px] font-bold">
                                            @if($event->ticketTypes->min('price') == 0)
                                                Free
                                            @else
                                                From KES {{ number_format($event->ticketTypes->min('price')) }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="p-8">
                            <div class="flex items-center gap-3 mb-4 text-brand-orange">
                                <span class="material-symbols-outlined text-sm">calendar_month</span>
                                <span class="text-xs font-black uppercase tracking-widest">
                                    {{ $event->start_at ? $event->start_at->format('M d, Y') : ($event->date ? $event->date->format('M d, Y') : 'Date Pending') }}
                                </span>
                                <span class="text-gray-600 dark:text-gray-400">•</span>
                                <span class="text-xs font-black uppercase tracking-widest">
                                    {{ $event->start_at ? $event->start_at->format('H:i') : ($event->date ? $event->date->format('H:i') : '') }}
                                </span>
                            </div>

                            <h3 class="text-2xl font-black text-white mb-4 group-hover:text-brand-orange transition-colors line-clamp-1">
                                {{ $event->title }}
                            </h3>

                            <div class="flex items-center gap-3 text-gray-400 mb-6">
                                <span class="material-symbols-outlined text-sm">location_on</span>
                                <span class="text-sm truncate font-bold">{{ $event->location ?: 'Online' }}</span>
                            </div>

                            <div class="flex items-center justify-between pt-6 border-t border-white/5">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $event->creator->profile_photo_url }}" alt="{{ $event->creator->name }}" class="w-8 h-8 rounded-full border border-white/10">
                                    <span class="text-xs text-gray-300 font-bold">{{ $event->creator->name }}</span>
                                </div>
                                <div class="flex items-center gap-1.5 text-gray-400">
                                    <span class="material-symbols-outlined text-sm text-[#257bf4]">group</span>
                                    <span class="text-xs font-bold">{{ $event->rsvps_count }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-16">
                {{ $events->links() }}
            </div>
        @endif
    </div>
</div>
