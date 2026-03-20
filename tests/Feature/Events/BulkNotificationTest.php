<?php

namespace Tests\Feature\Events;

use App\Livewire\Events\Show;
use App\Models\Event;
use App\Models\Invite;
use App\Models\User;
use App\Mail\BulkEventNotificationMail;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class BulkNotificationTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function event_owner_can_send_bulk_notifications_to_selected_guests()
    {
        Mail::fake();

        $user = User::factory()->create();
        $event = Event::factory()->create(['user_id' => $user->id]);
        
        $invite1 = Invite::factory()->create(['event_id' => $event->id, 'invitee_email' => 'guest1@example.com']);
        $invite2 = Invite::factory()->create(['event_id' => $event->id, 'invitee_email' => 'guest2@example.com']);
        $invite3 = Invite::factory()->create(['event_id' => $event->id, 'invitee_email' => 'guest3@example.com']);

        Livewire::actingAs($user)
            ->test(Show::class, ['event' => $event])
            ->call('toggleInviteSelection', $invite1->id)
            ->call('toggleInviteSelection', $invite2->id)
            ->set('bulkNotificationMessage', 'This is a test notification message.')
            ->call('sendBulkNotification')
            ->assertSet('selectedInviteIds', [])
            ->assertSet('showBulkNotificationModal', false)
            ->assertSet('bulkNotificationMessage', '');

        Mail::assertSent(BulkEventNotificationMail::class, function ($mail) use ($invite1, $event) {
            return $mail->hasTo('guest1@example.com') && 
                   $mail->event->id === $event->id &&
                   $mail->messageContent === 'This is a test notification message.';
        });

        Mail::assertSent(BulkEventNotificationMail::class, function ($mail) use ($invite2) {
            return $mail->hasTo('guest2@example.com');
        });

        Mail::assertNotSent(BulkEventNotificationMail::class, function ($mail) {
            return $mail->hasTo('guest3@example.com');
        });
    }

    /** @test */
    public function users_without_permission_cannot_send_bulk_notifications()
    {
        Mail::fake();

        $owner = User::factory()->create();
        $event = Event::factory()->create(['user_id' => $owner->id]);
        $otherUser = User::factory()->create();
        
        // Make them an attending guest so they can at least view the page
        $event->rsvps()->create(['user_id' => $otherUser->id, 'status' => 'attending']);
        
        $invite = Invite::factory()->create(['event_id' => $event->id, 'invitee_email' => 'guest@example.com']);

        Livewire::actingAs($otherUser)
            ->test(Show::class, ['event' => $event])
            ->call('toggleInviteSelection', $invite->id)
            ->set('bulkNotificationMessage', 'Illegal message')
            ->call('sendBulkNotification');

        Mail::assertNotSent(BulkEventNotificationMail::class);
    }
}
