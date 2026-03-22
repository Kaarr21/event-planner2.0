<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Ticket;
use App\Models\TicketType;
use Illuminate\Support\Str;
use App\Mail\TicketConfirmation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TicketService
{
    /**
     * Generate tickets for a confirmed order.
     *
     * @param Order $order
     * @return void
     */
    public function generateTickets(Order $order)
    {
        if ($order->status !== 'confirmed') {
            Log::warning("TicketService: Attempted to generate tickets for unconfirmed order #{$order->id}");
            return;
        }

        DB::transaction(function () use ($order) {
            // Idempotency check
            if ($order->tickets()->count() > 0) {
                return;
            }

            foreach ($order->items as $item) {
                for ($i = 0; $i < $item->quantity; $i++) {
                    $ticketNumber = 'TICK-' . strtoupper(Str::random(10));
                    Ticket::create([
                        'order_id' => $order->id,
                        'ticket_type_id' => $item->ticket_type_id,
                        'user_id' => $order->user_id,
                        'ticket_number' => $ticketNumber,
                        'qr_code_data' => $ticketNumber, // URL can be added later
                    ]);
                }

                $item->ticketType->increment('sold_count', $item->quantity);
            }

            // Send confirmation email after generation
            $recipient = $order->order_email ?: $order->user->email;
            try {
                Mail::to($recipient)->send(new TicketConfirmation($order));
                Log::info("TicketService: Confirmation email sent for order #{$order->id} to {$recipient}");
            } catch (\Exception $e) {
                Log::error("TicketService: Failed to send email for order #{$order->id}: " . $e->getMessage());
            }
        });
    }
}
