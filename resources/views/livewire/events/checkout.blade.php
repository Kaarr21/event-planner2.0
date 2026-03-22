<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\Event;
use App\Models\TicketType;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\TicketConfirmation;

new #[Layout('layouts.app')] class extends Component {
    public Event $event;
    public array $selection = [];
    public float $totalAmount = 0;
    public string $order_email = '';
    public $ticketDetails = [];

    public function mount(Event $event)
    {
        $this->event = $event;
        $this->selection = session('ticket_selection', []);

        if (empty($this->selection)) {
            return redirect()->route('events.show', $event);
        }

        $this->calculateTotals();
        $this->order_email = auth()->user()->email;
    }

    public function calculateTotals()
    {
        $this->totalAmount = 0;
        $this->ticketDetails = [];
        
        foreach ($this->selection as $typeId => $quantity) {
            $type = TicketType::find($typeId);
            if ($type) {
                $subtotal = $type->price * $quantity;
                $this->totalAmount += $subtotal;
                $this->ticketDetails[] = [
                    'id' => $type->id,
                    'name' => $type->name,
                    'price' => $type->price,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal,
                ];
            }
        }
    }

    public function completePurchase()
    {
        $this->validate([
            'order_email' => 'nullable|email',
        ]);

        try {
            DB::beginTransaction();

            // Re-validate availability
            foreach ($this->selection as $typeId => $quantity) {
                $type = TicketType::lockForUpdate()->find($typeId);
                if (!$type->is_available || ($type->capacity !== null && ($type->sold_count + $quantity) > $type->capacity)) {
                    throw new \Exception("{$type->name} is no longer available in the requested quantity.");
                }
            }

            // Create Order
            $order = Order::create([
                'user_id' => auth()->id(),
                'event_id' => $this->event->id,
                'total_amount' => $this->totalAmount,
                'status' => 'confirmed', // Assuming instant confirmation for now (free or simulated payment)
                'order_email' => $this->order_email,
            ]);

            // Create Tickets
            foreach ($this->selection as $typeId => $quantity) {
                $type = TicketType::find($typeId);
                
                for ($i = 0; $i < $quantity; $i++) {
                    $ticketNumber = 'TICK-' . strtoupper(Str::random(10));
                    Ticket::create([
                        'order_id' => $order->id,
                        'ticket_type_id' => $type->id,
                        'user_id' => auth()->id(),
                        'ticket_number' => $ticketNumber,
                        'qr_code_data' => $ticketNumber, // Simplified for now
                    ]);
                }

                $type->increment('sold_count', $quantity);
            }

            DB::commit();

            // Send confirmation email
            $recipient = $this->order_email ?: auth()->user()->email;
            Mail::to($recipient)->send(new TicketConfirmation($order));

            session()->forget('ticket_selection');
            
            $this->dispatch('notify', message: 'Purchase successful! Your tickets have been issued.', type: 'success');
            
            return redirect()->route('events.show', $this->event);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', message: $e->getMessage(), type: 'error');
        }
    }
}; ?>

<div class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50/50 dark:bg-gray-900/50 min-h-screen">
    <div class="max-w-4xl mx-auto">
        <div class="mb-12">
            <a href="{{ route('events.show', $event) }}" class="inline-flex items-center text-sm font-black text-gray-400 hover:text-brand-orange uppercase tracking-widest transition-colors mb-6 group">
                <span class="material-symbols-outlined text-lg mr-2 group-hover:-translate-x-1 transition-transform">arrow_back</span>
                Back to Experience
            </a>
            <h1 class="text-5xl font-black text-gray-900 dark:text-white uppercase italic tracking-tighter leading-none mb-2">Finalize Reservation</h1>
            <p class="text-xs font-black text-gray-400 uppercase tracking-[0.3em] opacity-60">Review your selection and secure your access.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            <div class="lg:col-span-2 space-y-6">
                <!-- Summary Card -->
                <div class="glass-card dark:glass-card-dark rounded-[2.5rem] border-none shadow-3xl overflow-hidden p-8 md:p-12">
                    <div class="flex items-center gap-4 mb-10">
                        <div class="w-12 h-12 rounded-2xl bg-brand-orange/10 flex items-center justify-center text-brand-orange">
                            <span class="material-symbols-outlined font-black">receipt_long</span>
                        </div>
                        <h3 class="text-2xl font-black text-gray-900 dark:text-white uppercase italic tracking-tight">Order Summary</h3>
                    </div>

                    <div class="divide-y divide-white/5 space-y-6">
                        @foreach($ticketDetails as $item)
                            <div class="pt-6 flex justify-between items-center group">
                                <div>
                                    <h4 class="text-xl font-black text-gray-900 dark:text-white uppercase italic tracking-tighter">{{ $item['name'] }}</h4>
                                    <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">
                                        {{ $item['quantity'] }} × KES {{ number_format($item['price'], 0) }}
                                    </p>
                                </div>
                                <p class="text-xl font-black text-brand-orange italic tracking-tighter">
                                    KES {{ number_format($item['subtotal'], 0) }}
                                </p>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-12 pt-12 border-t border-brand-orange/10">
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="text-[10px] font-black text-brand-orange uppercase tracking-[.4em] mb-1">Total investment</h4>
                                <p class="text-xs font-bold text-gray-500 italic opacity-60">Includes all taxes and priority access.</p>
                            </div>
                            <h4 class="text-5xl font-black text-gray-900 dark:text-white italic tracking-tighter">
                                <span class="text-xl not-italic opacity-40 mr-2">KES</span>{{ number_format($totalAmount, 0) }}
                            </h4>
                        </div>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="p-8 bg-brand-teal/5 rounded-[2.5rem] border border-brand-teal/10 flex gap-6 items-start">
                    <span class="material-symbols-outlined text-brand-teal text-3xl">verified_user</span>
                    <div>
                        <h4 class="text-sm font-black text-brand-teal uppercase tracking-widest mb-2 italic">Secure Transaction</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium italic opacity-70 leading-relaxed">Your order is protected by industrial-grade encryption. Once confirmed, your unique access codes will be generated instantly and linked to your identity.</p>
                    </div>
                </div>
            </div>

            <!-- Action Sidebar -->
            <div class="space-y-6 lg:sticky lg:top-8">
                <div class="glass-card dark:glass-card-dark rounded-[2.5rem] border-none shadow-3xl overflow-hidden p-8">
                    <div class="mb-8">
                        <label for="order_email" class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-4">Confirmation Email (Optional)</label>
                        <div class="relative group">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-brand-orange transition-colors">mail</span>
                            <input type="email" id="order_email" wire:model="order_email" class="w-full bg-white/5 border border-white/10 rounded-2xl py-4 pl-12 pr-4 text-gray-900 dark:text-white font-bold tracking-tight focus:ring-2 focus:ring-brand-orange/20 focus:border-brand-orange/40 transition-all outline-none" placeholder="Enter your email...">
                        </div>
                        @error('order_email') <span class="text-[10px] font-black text-red-500 uppercase tracking-widest mt-2 block pl-2">{{ $message }}</span> @enderror
                    </div>

                    <button wire:click="completePurchase" wire:loading.attr="disabled" class="btn-brand pamoja-gradient w-full py-6 group flex items-center justify-center gap-4 text-lg mb-4">
                        <span wire:loading.remove>Complete Purchase</span>
                        <span wire:loading>Processing...</span>
                        <span class="material-symbols-outlined text-xl group-hover:scale-125 transition-transform" wire:loading.remove>local_mall</span>
                    </button>
                    
                    <p class="text-[9px] font-black text-center text-gray-500 uppercase tracking-widest opacity-60 px-4">
                        By clicking complete, you agree to our terms of service.
                    </p>
                </div>

                <!-- Step Tracker -->
                <div class="px-8 py-6 bg-white/5 rounded-[2rem] border border-white/5 flex flex-col gap-4">
                    <div class="flex items-center gap-4">
                        <span class="w-6 h-6 rounded-full bg-brand-orange flex items-center justify-center text-[10px] font-black text-white">1</span>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest italic line-through opacity-40">Selection</p>
                    </div>
                    <div class="w-px h-4 bg-white/10 ml-3"></div>
                    <div class="flex items-center gap-4">
                        <span class="w-6 h-6 rounded-full bg-brand-orange flex items-center justify-center text-[10px] font-black text-white animate-pulse">2</span>
                        <p class="text-[10px] font-black text-white uppercase tracking-widest italic">Verification</p>
                    </div>
                    <div class="w-px h-4 bg-white/10 ml-3"></div>
                    <div class="flex items-center gap-4 opacity-40">
                        <span class="w-6 h-6 rounded-full bg-white/10 flex items-center justify-center text-[10px] font-black text-gray-500">3</span>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest italic">Success</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
