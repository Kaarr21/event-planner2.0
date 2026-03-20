<?php

namespace Database\Factories;

use App\Models\Invite;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InviteFactory extends Factory
{
    protected $model = Invite::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'inviter_id' => User::factory(),
            'invitee_name' => $this->faker->name,
            'invitee_email' => $this->faker->safeEmail,
            'status' => 'pending',
        ];
    }
}
