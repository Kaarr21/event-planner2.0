<?php

use Livewire\Volt\Component;
use App\Models\Event;
use App\Models\TicketType;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Support\Collection;
use Barryvdh\DomPDF\Facade\Pdf;

new class extends Component {
    public Event $event;
    public array $selectedQuantities = []; // [ticket_type_id => quantity]
    public float $totalAmount = 0;

    public function mount(Event $event)
    {
        $this->event = $event;
        foreach ($this->event->ticketTypes as $type) {
            $this->selectedQuantities[$type->id] = 0;
        }
    }

    public function updatedSelectedQuantities()
    {
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $this->totalAmount = 0;
        foreach ($this->event->ticketTypes as $type) {
            $quantity = (int) ($this->selectedQuantities[$type->id] ?? 0);
            $this->totalAmount += $type->price * $quantity;
        }
    }

    public function increment($typeId)
    {
        $type = TicketType::find($typeId);
        $current = (int) ($this->selectedQuantities[$typeId] ?? 0);
        
        if ($type->max_per_purchase && $current >= $type->max_per_purchase) {
            $this->dispatch('notify', message: "Maximum {$type->max_per_purchase} tickets allowed per purchase.", type: 'warning');
            return;
        }
        
        if ($type->capacity !== null && ($type->sold_count + $current) >= $type->capacity) {
            $this->dispatch('notify', message: "No more tickets available for {$type->name}.", type: 'warning');
            return;
        }

        $this->selectedQuantities[$typeId]++;
        $this->calculateTotal();
    }

    public function decrement($typeId)
    {
        $current = (int) ($this->selectedQuantities[$typeId] ?? 0);
        
        if ($current <= 0) return;

        $this->selectedQuantities[$typeId]--;
        $this->calculateTotal();
    }

    public function checkout()
    {
        $hasSelection = collect($this->selectedQuantities)->some(fn($q) => $q > 0);
        
        if (!$hasSelection) {
            $this->dispatch('notify', message: 'Please select at least one ticket.', type: 'error');
            return;
        }

        // Store selection in session
        session(['ticket_selection' => array_filter($this->selectedQuantities, fn($q) => $q > 0)]);

        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Validate availability again before creating order
        foreach ($this->selectedQuantities as $typeId => $quantity) {
            if ($quantity <= 0) continue;
            
            $type = TicketType::find($typeId);
            if (!$type->is_available) {
                $this->dispatch('notify', message: "{$type->name} is no longer available.", type: 'error');
                return;
            }
            if ($type->capacity !== null && ($type->sold_count + $quantity) > $type->capacity) {
                $this->dispatch('notify', message: "Not enough tickets available for {$type->name}.", type: 'error');
                return;
            }
        }

        return redirect()->route('events.checkout', ['event' => $this->event->id]);
    }

    public function downloadTickets($orderId)
    {
        $order = Order::with(['event', 'tickets.ticketType', 'user'])->find($orderId);
        
        if (!$order || $order->user_id !== auth()->id()) {
            $this->dispatch('notify', message: 'Unauthorized action.', type: 'error');
            return;
        }

        $pdf = Pdf::loadView('pdfs.ticket', ['order' => $order]);
        
        return response()->streamDownload(
            fn () => print($pdf->output()),
            "Tickets-{$order->id}.pdf"
        );
    }

    public function with()
    {
        $userId = auth()->id();
        
        return [
            'ticketTypes' => $this->event->ticketTypes,
            'userOrders' => $userId 
                ? auth()->user()->orders()
                    ->where('event_id', $this->event->id)
                    ->with('tickets.ticketType')
                    ->latest()
                    ->get()
                : collect(),
        ];
    }
}; ?>

<div class="animate-in fade-in slide-in-from-bottom-4 duration-500 space-y-12">
    @if($userOrders->isNotEmpty())
        <div class="glass-card dark:glass-card-dark rounded-[3rem] border-none shadow-3xl overflow-hidden relative">
            <div class="p-10 md:p-14 relative z-10">
                <div class="flex items-center justify-between mb-12">
                    <div class="flex items-center gap-6">
                        <div class="w-16 h-16 rounded-[2rem] bg-brand-teal/10 flex items-center justify-center text-brand-teal shadow-lg shadow-brand-teal/5">
                            <span class="material-symbols-outlined text-3xl font-black">fact_check</span>
                        </div>
                        <div>
                            <h3 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tighter italic leading-none mb-1">Your Access Passes</h3>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest opacity-60 italic">You are confirmed for this experience.</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    @foreach($userOrders as $order)
                        <div class="flex flex-col md:flex-row items-start md:items-center justify-between p-8 bg-brand-teal/5 rounded-[2.5rem] border border-brand-teal/10 group animate-in slide-in-from-left duration-500" style="animation-delay: {{ $loop->index * 100 }}ms">
                            <div class="flex-1">
                                <div class="flex items-center gap-4 mb-2">
                                    <h4 class="text-xl font-black text-gray-900 dark:text-white uppercase italic tracking-tighter">Order #{{ $order->id }}</h4>
                                    <span class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest bg-brand-teal text-white">Confirmed</span>
                                </div>
                                <div class="flex flex-wrap gap-2 mt-4">
                                    @foreach($order->tickets as $ticket)
                                        <span class="px-4 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest bg-white/5 border border-white/10 text-gray-500 italic">
                                            {{ $ticket->ticketType->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            <button wire:click="downloadTickets({{ $order->id }})" class="mt-6 md:mt-0 px-8 py-4 rounded-2xl bg-white/10 border border-white/10 text-xs font-black uppercase tracking-widest text-gray-900 dark:text-white hover:bg-brand-orange hover:text-white hover:border-brand-orange transition-all flex items-center gap-3 active:scale-95 shadow-lg group">
                                <span class="material-symbols-outlined text-xl group-hover:animate-bounce">download</span>
                                Download All Passes
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="glass-card dark:glass-card-dark rounded-[3rem] border-none shadow-3xl overflow-hidden relative">
        <!-- Decoration -->
        <div class="absolute -top-24 -right-24 w-48 h-48 bg-brand-orange/5 rounded-full blur-[60px]"></div>
        
        <div class="p-10 md:p-14 relative z-10">
            <div class="flex items-center gap-6 mb-12">
                <div class="w-16 h-16 rounded-[2rem] bg-brand-orange/10 flex items-center justify-center text-brand-orange shadow-lg shadow-brand-orange/5">
                    <span class="material-symbols-outlined text-3xl font-black">local_activity</span>
                </div>
                <div>
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tighter italic leading-none mb-1">Select Access</h3>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest opacity-60">Choose your tier for this experience.</p>
                </div>
            </div>

            <div class="space-y-6">
                @forelse($ticketTypes as $type)
                    <div class="p-8 bg-white/5 rounded-[2.5rem] border border-white/5 hover:border-brand-orange/20 transition-all duration-500 group">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8">
                            <div class="flex-1">
                                <div class="flex items-center gap-4 mb-3">
                                    <h4 class="text-2xl font-black text-gray-900 dark:text-white uppercase italic tracking-tighter leading-none">{{ $type->name }}</h4>
                                    <span class="px-4 py-1.5 rounded-full text-[8px] font-black uppercase tracking-[0.2em] border border-brand-orange/20 text-brand-orange bg-brand-orange/5">
                                        {{ $type->type }}
                                    </span>
                                </div>
                                @if($type->description)
                                    <p class="text-sm text-gray-500 dark:text-gray-400 font-bold italic tracking-tight opacity-60 mb-6 leading-relaxed max-w-2xl">{{ $type->description }}</p>
                                @endif
                                <div class="flex items-center gap-8">
                                    <p class="text-3xl font-black text-brand-orange italic tracking-tighter">
                                        {{ $type->price > 0 ? 'KES ' . number_format($type->price, 0) : 'COMPLIMENTARY' }}
                                    </p>
                                    @if($type->capacity)
                                        <div class="flex items-center gap-2 px-3 py-1 rounded-lg bg-white/5 border border-white/10">
                                            <span class="w-1.5 h-1.5 rounded-full {{ ($type->capacity - $type->sold_count) < 10 ? 'bg-brand-red animate-pulse' : 'bg-brand-teal' }}"></span>
                                            <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">
                                                {{ $type->capacity - $type->sold_count }} available
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-6 bg-white/5 p-4 rounded-[2rem] border border-white/5 shadow-inner">
                                @if($type->sale_start_date && $type->sale_start_date->isFuture())
                                    <div class="px-6 py-2">
                                        <p class="text-[10px] font-black text-brand-orange uppercase tracking-[.2em] leading-none mb-1">Coming Soon</p>
                                        <p class="text-[8px] font-bold text-gray-500 uppercase tracking-widest">{{ $type->sale_start_date->format('M d, Y') }}</p>
                                    </div>
                                @elseif($type->sale_end_date && $type->sale_end_date->isPast())
                                    <div class="px-6 py-2">
                                        <p class="text-[10px] font-black text-brand-red uppercase tracking-[.2em] leading-none mb-1">Ended</p>
                                        <p class="text-[8px] font-bold text-gray-500 uppercase tracking-widest">{{ $type->sale_end_date->format('M d, Y') }}</p>
                                    </div>
                                @elseif($type->capacity !== null && $type->sold_count >= $type->capacity)
                                    <div class="px-6 py-2">
                                        <p class="text-[10px] font-black text-brand-red uppercase tracking-[.2em] leading-none">Sold Out</p>
                                    </div>
                                @else
                                    <button type="button" wire:click="decrement({{ $type->id }})" class="w-12 h-12 rounded-[1.25rem] bg-white/5 flex items-center justify-center text-gray-400 hover:text-brand-orange hover:bg-white/10 transition-all active:scale-90 border border-white/5">
                                        <span class="material-symbols-outlined font-black">remove</span>
                                    </button>
                                    <span class="text-2xl font-black text-gray-900 dark:text-white w-10 text-center italic tracking-tighter">{{ $selectedQuantities[$type->id] ?? 0 }}</span>
                                    <button type="button" wire:click="increment({{ $type->id }})" class="w-12 h-12 rounded-[1.25rem] bg-white/5 flex items-center justify-center text-gray-400 hover:text-brand-orange hover:bg-white/10 transition-all active:scale-90 border border-white/5">
                                        <span class="material-symbols-outlined font-black">add</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-24 bg-white/5 rounded-[3rem] border border-dashed border-white/10 opacity-60">
                        <span class="material-symbols-outlined text-7xl text-brand-orange mb-6 opacity-20">inventory_2</span>
                        <p class="text-sm font-black text-gray-500 uppercase tracking-[0.3em] italic">The box office is currently closed.</p>
                    </div>
                @endforelse
            </div>
        </div>

        @if($totalAmount > 0 || collect($selectedQuantities)->some(fn($q) => $q > 0))
            <div class="bg-brand-orange/10 p-10 md:p-14 border-t border-brand-orange/10 backdrop-blur-md">
                <div class="flex flex-col md:flex-row justify-between items-center gap-10">
                    <div class="text-center md:text-left">
                        <p class="text-[10px] font-black text-brand-orange uppercase tracking-[.4em] mb-2">Total Access Fee</p>
                        <h4 class="text-6xl font-black text-gray-900 dark:text-white italic tracking-tighter leading-none">
                            <span class="text-2xl not-italic opacity-40 mr-2">KES</span>{{ number_format($totalAmount, 0) }}
                        </h4>
                    </div>
                    <button wire:click="checkout" class="btn-brand pamoja-gradient py-6 px-20 group w-full md:w-auto text-xl shadow-2xl shadow-brand-orange/20">
                        {{ auth()->check() ? 'Proceed to Checkout' : 'Login to Secure Access' }}
                        <span class="material-symbols-outlined text-2xl ml-4 group-hover:translate-x-2 transition-transform">arrow_forward</span>
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>
