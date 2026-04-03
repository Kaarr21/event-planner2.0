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
            return redirect()->guest(route('login'));
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

        if ($order->status !== 'confirmed') {
            $this->dispatch('notify', message: 'Tickets can only be downloaded for confirmed payments.', type: 'warning');
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

<div class="animate-in fade-in slide-in-from-bottom-8 duration-700 space-y-12">
    @if($userOrders->isNotEmpty())
        <div class="bg-white rounded-[4rem] border border-slate-100 shadow-lux overflow-hidden relative">
            <div class="p-12 md:p-14 relative z-10">
                <div class="flex items-center justify-between mb-12">
                    <div class="flex items-center gap-6">
                        <div class="w-16 h-16 rounded-[2rem] bg-emerald-50 flex items-center justify-center text-emerald-600 shadow-sm">
                            <span class="material-symbols-outlined text-3xl font-bold">fact_check</span>
                        </div>
                        <div>
                            <h3 class="text-3xl font-display font-extrabold text-slate-900 uppercase tracking-tight italic leading-none mb-2">Verified Access Passes</h3>
                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.4em] opacity-80 italic">Your authenticated clearance for this experience.</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    @foreach($userOrders as $order)
                        <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between p-10 {{ $order->status === 'confirmed' ? 'bg-emerald-50/30 border-emerald-100' : ($order->status === 'failed' ? 'bg-rose-50/30 border-rose-100' : 'bg-slate-50 border-slate-100') }} rounded-[3rem] border group animate-in slide-in-from-left duration-700" style="animation-delay: {{ $loop->index * 150 }}ms">
                            <div class="flex-1">
                                <div class="flex items-center gap-5 mb-4">
                                    <h4 class="text-2xl font-bold text-slate-900 uppercase italic tracking-tight">Voucher #{{ $order->id }}</h4>
                                    @if($order->status === 'confirmed')
                                        <span class="px-5 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-widest bg-emerald-500 text-white shadow-lg shadow-emerald-100">Authenticated</span>
                                    @elseif($order->status === 'failed')
                                        <span class="px-5 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-widest bg-rose-500 text-white shadow-lg shadow-rose-100">Retracted</span>
                                    @else
                                        <span class="px-5 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-widest bg-indigo-600 text-white shadow-lg shadow-indigo-100">Awaiting Clear</span>
                                    @endif
                                </div>
                                <div class="flex flex-wrap gap-3 mt-6">
                                    @foreach($order->tickets as $ticket)
                                        <span class="px-6 py-2 rounded-2xl text-[11px] font-bold uppercase tracking-widest bg-white border border-slate-100 text-slate-500 italic shadow-sm group-hover:scale-105 transition-transform">
                                            {{ $ticket->ticketType->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            @if($order->status === 'confirmed')
                                <button wire:click="downloadTickets({{ $order->id }})" class="mt-8 lg:mt-0 px-10 py-5 rounded-[1.5rem] bg-indigo-600 text-white text-[11px] font-bold uppercase tracking-widest hover:bg-indigo-700 transition-all flex items-center justify-center gap-4 active:scale-95 shadow-xl shadow-indigo-100 group">
                                    <span class="material-symbols-outlined text-xl group-hover:scale-125 transition-transform">file_download</span>
                                    Retrieve Credentials
                                </button>
                            @else
                                <div class="mt-8 lg:mt-0 px-10 py-5 rounded-[1.5rem] bg-slate-100/50 border border-slate-200 text-[11px] font-bold uppercase tracking-widest text-slate-400 italic text-center">
                                    Pending Cryptographic Review
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-[4rem] border border-slate-100 shadow-lux overflow-hidden relative">
        <!-- Decoration -->
        <div class="absolute -top-32 -right-32 w-64 h-64 bg-indigo-50/50 rounded-full blur-[80px]"></div>
        
        <div class="p-12 md:p-14 relative z-10">
            <div class="flex items-center gap-6 mb-14">
                <div class="w-16 h-16 rounded-[2rem] bg-indigo-50 flex items-center justify-center text-indigo-600 shadow-sm">
                    <span class="material-symbols-outlined text-3xl font-bold">local_activity</span>
                </div>
                <div>
                    <h3 class="text-3xl font-display font-extrabold text-slate-900 uppercase tracking-tight italic leading-none mb-2">Acquire Access</h3>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.4em] opacity-80 italic">Curate your placement within the experience.</p>
                </div>
            </div>

            <div class="space-y-8">
                @forelse($ticketTypes as $type)
                    <div class="p-10 bg-slate-50/50 rounded-[3.5rem] border border-slate-100 hover:border-indigo-100 transition-all duration-700 group shadow-sm hover:shadow-indigo-50/30">
                        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-10">
                            <div class="flex-1">
                                <div class="flex items-center gap-5 mb-4">
                                    <h4 class="text-3xl font-display font-extrabold text-slate-900 uppercase italic tracking-tight leading-none group-hover:text-indigo-600 transition-colors">{{ $type->name }}</h4>
                                    <span class="px-5 py-2 rounded-full text-[9px] font-bold uppercase tracking-[0.3em] border border-indigo-100 text-indigo-600 bg-white shadow-sm">
                                        {{ $type->type }}
                                    </span>
                                </div>
                                @if($type->description)
                                    <p class="text-[15px] text-slate-500 font-medium italic tracking-tight opacity-70 mb-8 leading-relaxed max-w-3xl">{{ $type->description }}</p>
                                @endif
                                <div class="flex flex-wrap items-center gap-10">
                                    <p class="text-4xl font-display font-black text-slate-900 italic tracking-tight">
                                        {{ $type->price > 0 ? 'KES ' . number_format($type->price, 0) : 'COMPLIMENTARY' }}
                                    </p>
                                    @if($type->capacity)
                                        <div class="flex items-center gap-3 px-5 py-2.5 rounded-2xl bg-white border border-slate-100 shadow-sm">
                                            <span class="w-2.5 h-2.5 rounded-full {{ ($type->capacity - $type->sold_count) < 20 ? 'bg-rose-500 animate-pulse' : 'bg-emerald-500' }}"></span>
                                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest italic leading-none">
                                                {{ $type->capacity - $type->sold_count }} entries remaining
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-8 bg-white p-5 rounded-[2.5rem] border border-slate-100 shadow-lux w-full lg:w-auto justify-center">
                                @if($type->sale_start_date && $type->sale_start_date->isFuture())
                                    <div class="px-10 py-3 text-center">
                                        <p class="text-[11px] font-bold text-indigo-600 uppercase tracking-[.3em] leading-none mb-2 italic">Awaiting Release</p>
                                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $type->sale_start_date->format('M d, Y') }}</p>
                                    </div>
                                @elseif($type->sale_end_date && $type->sale_end_date->isPast())
                                    <div class="px-10 py-3 text-center">
                                        <p class="text-[11px] font-bold text-rose-500 uppercase tracking-[.3em] leading-none mb-2 italic">Sales Terminated</p>
                                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $type->sale_end_date->format('M d, Y') }}</p>
                                    </div>
                                @elseif($type->capacity !== null && $type->sold_count >= $type->capacity)
                                    <div class="px-10 py-3 text-center">
                                        <p class="text-[13px] font-bold text-rose-500 uppercase tracking-[.4em] leading-none italic">Sold Out</p>
                                    </div>
                                @else
                                    <button type="button" wire:click="decrement({{ $type->id }})" class="w-14 h-14 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all active:scale-90 border border-slate-100 hover:rotate-[-8deg]">
                                        <span class="material-symbols-outlined font-bold text-2xl">remove</span>
                                    </button>
                                    <span class="text-3xl font-display font-extrabold text-slate-900 w-12 text-center italic tracking-tight">{{ $selectedQuantities[$type->id] ?? 0 }}</span>
                                    <button type="button" wire:click="increment({{ $type->id }})" class="w-14 h-14 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all active:scale-90 border border-slate-100 hover:rotate-[8deg]">
                                        <span class="material-symbols-outlined font-bold text-2xl">add</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-32 bg-slate-50/50 rounded-[4rem] border-2 border-dashed border-slate-200 opacity-60">
                        <div class="w-24 h-24 rounded-[3rem] bg-white shadow-lux flex items-center justify-center text-slate-200 mx-auto mb-10">
                            <span class="material-symbols-outlined text-6xl">inventory_2</span>
                        </div>
                        <p class="text-[15px] font-bold text-slate-400 uppercase tracking-[0.4em] italic leading-relaxed">The box office is presently void of activity.</p>
                    </div>
                @endforelse
            </div>
        </div>

        @if($totalAmount > 0 || collect($selectedQuantities)->some(fn($q) => $q > 0))
            <div class="bg-indigo-600 p-12 md:p-14 shadow-[0_-20px_80px_rgba(67,56,202,0.15)] relative z-20">
                <div class="flex flex-col lg:flex-row justify-between items-center gap-12">
                    <div class="text-center lg:text-left">
                        <p class="text-[11px] font-bold text-indigo-200 uppercase tracking-[.6em] mb-4 italic opacity-80 leading-none">Aggregated Access Fee</p>
                        <h4 class="text-7xl font-display font-extrabold text-white italic tracking-tight leading-none">
                            <span class="text-2xl not-italic opacity-50 mr-4 font-bold tracking-widest">KES</span>{{ number_format($totalAmount, 0) }}
                        </h4>
                    </div>
                    <button wire:click="checkout" class="w-full lg:w-auto px-20 py-8 bg-white text-indigo-600 rounded-[2.5rem] font-display font-extrabold uppercase tracking-[0.3em] text-lg hover:bg-slate-50 transition-all active:scale-95 shadow-2xl hover:-translate-y-1 flex items-center justify-center gap-6 group">
                        {{ auth()->check() ? 'Checkout' : 'Authenticate' }}
                        <span class="material-symbols-outlined text-3xl group-hover:translate-x-3 transition-transform">east</span>
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>
