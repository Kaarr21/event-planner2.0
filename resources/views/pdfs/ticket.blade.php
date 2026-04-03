<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 40px;
            background-color: #f3f4f6;
            color: #1a1a1a;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }
        .header {
            background: #FF5A1F;
            padding: 40px;
            text-align: center;
            color: #fff;
        }
        .ticket-body {
            padding: 40px;
        }
        .ticket-divider {
            border-bottom: 1px dashed #e5e7eb;
        }
        .ticket-info {
            margin-bottom: 30px;
        }
        .label {
            text-transform: uppercase;
            font-weight: bold;
            font-size: 10px;
            color: #9ca3af;
            margin-bottom: 5px;
        }
        .value {
            font-weight: bold;
            font-size: 20px;
            margin: 0;
            color: #111827;
        }
        .tier-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 99px;
            background: #FF5A1F;
            color: #fff;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 5px;
        }
        .qr-section {
            text-align: center;
            float: right;
            width: 120px;
        }
        .footer {
            padding: 30px;
            background: #f9fafb;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            clear: both;
        }
        .clear {
            clear: both;
        }
        .row {
            margin-bottom: 20px;
        }
        .col {
            display: inline-block;
            width: 45%;
            vertical-align: top;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <p style="text-transform: uppercase; letter-spacing: 2px; font-weight: bold; font-size: 12px; margin: 0 0 10px 0;">Official Access Pass</p>
            <h1 style="font-size: 28px; font-weight: bold; margin: 0; text-transform: uppercase;">{{ $order->event->title }}</h1>
        </div>

        @foreach($order->tickets as $ticket)
            <div class="ticket-body {{ !$loop->last ? 'ticket-divider' : '' }}">
                <div class="qr-section">
                    <img src="data:image/svg+xml;base64,{{ base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(120)->margin(0)->generate($ticket->qr_code_data)) }}" width="120" height="120">
                    <p style="font-size: 10px; font-weight: bold; color: #9ca3af; margin-top: 10px; letter-spacing: 1px;">{{ $ticket->ticket_number }}</p>
                </div>

                <div style="margin-right: 140px;">
                    <div class="ticket-info">
                        <p class="label">Ticket Holder</p>
                        <h2 class="value">{{ $order->user->name }}</h2>
                        
                        <p class="label" style="margin-top: 20px;">Tier</p>
                        <span class="tier-badge">
                            {{ $ticket->ticketType->name }}
                        </span>
                    </div>

                    <div class="row">
                        <div class="col">
                            <p class="label">Date</p>
                            <p style="font-weight: bold; font-size: 14px; margin: 0; color: #374151;">{{ ($order->event->start_at ?? $order->event->date)?->format('M d, Y') ?? 'Date Pending' }}</p>
                        </div>
                        <div class="col">
                            <p class="label">Venue</p>
                            <p style="font-weight: bold; font-size: 14px; margin: 0; color: #374151;">{{ $order->event->location ?? 'Main Hall' }}</p>
                        </div>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        @endforeach

        <div class="footer">
            <p style="font-size: 11px; color: #6b7280; margin: 0;">Please present this PDF at the entrance. Each QR code is valid for one-time admission.</p>
        </div>
    </div>
</body>
</html>
