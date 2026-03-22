@extends('layouts.public')

@section('content')
<div style="font-family: 'Inter', sans-serif; padding: 40px; color: #1a1a1a;">
    <div style="max-width: 600px; margin: 0 auto; background: #fff; border-radius: 24px; overflow: hidden; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
        <div style="background: #FF5A1F; padding: 40px; text-align: center; color: #fff;">
            <p style="text-transform: uppercase; letter-spacing: 2px; font-weight: 900; font-size: 12px; margin-bottom: 10px;">Official Access Pass</p>
            <h1 style="font-size: 32px; font-weight: 900; margin: 0; text-transform: uppercase; font-style: italic;">{{ $order->event->title }}</h1>
        </div>

        @foreach($order->tickets as $ticket)
            <div style="padding: 40px; {{ !$loop->last ? 'border-bottom: 1px dashed #e5e7eb;' : '' }}">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 30px;">
                    <div style="flex: 1;">
                        <p style="text-transform: uppercase; font-weight: 900; font-size: 10px; color: #9ca3af; margin-bottom: 5px;">Ticket Holder</p>
                        <h2 style="font-weight: 900; font-size: 20px; margin: 0; color: #111827; text-transform: uppercase; font-style: italic;">{{ $order->user->name }}</h2>
                        
                        <p style="text-transform: uppercase; font-weight: 900; font-size: 10px; color: #9ca3af; margin: 20px 0 5px 0;">Tier</p>
                        <span style="display: inline-block; padding: 4px 12px; border-radius: 99px; background: #FF5A1F10; color: #FF5A1F; font-size: 10px; font-weight: 900; text-transform: uppercase; border: 1px solid #FF5A1F20;">
                            {{ $ticket->ticketType->name }}
                        </span>
                    </div>
                    <div style="text-align: center; margin-left: 20px;">
                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(120)->margin(0)->generate($ticket->qr_code_data) !!}
                        <p style="font-size: 10px; font-weight: 900; color: #9ca3af; margin-top: 10px; letter-spacing: 1px;">{{ $ticket->ticket_number }}</p>
                    </div>
                </div>

                <div style="display: flex; gap: 40px; margin-bottom: 20px;">
                    <div>
                        <p style="text-transform: uppercase; font-weight: 900; font-size: 10px; color: #9ca3af; margin-bottom: 5px;">Date</p>
                        <p style="font-weight: 700; font-size: 14px; margin: 0; color: #374151;">{{ $order->event->start_date->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p style="text-transform: uppercase; font-weight: 900; font-size: 10px; color: #9ca3af; margin-bottom: 5px;">Venue</p>
                        <p style="font-weight: 700; font-size: 14px; margin: 0; color: #374151;">{{ $order->event->location ?? 'Main Hall' }}</p>
                    </div>
                </div>
            </div>
        @endforeach

        <div style="padding: 30px; background: #f9fafb; text-align: center; border-top: 1px solid #e5e7eb;">
            <p style="font-size: 12px; color: #6b7280; margin: 0;">Please present this PDF at the entrance. Each QR code is valid for one-time admission.</p>
        </div>
    </div>
</div>
@endsection
