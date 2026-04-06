<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Process pending invitations for this email
        $pendingInvites = \App\Models\Invite::where('invitee_email', $user->email)
            ->whereNull('invitee_id')
            ->get();

        foreach ($pendingInvites as $invite) {
            $invite->update(['invitee_id' => $user->id]);

            \App\Models\Notification::create([
                'user_id' => $user->id,
                'sender_id' => $invite->inviter_id,
                'type' => 'invite',
                'title' => 'New Event Invitation',
                'message' => ($invite->inviter->name ?? 'Someone') . " has invited you to: " . $invite->event->title,
                'related_id' => $invite->id,
            ]);
        }

        // Also check if they were added as an organizer by email (if that logic exists/is added later)
        // For now, we focus on the invitations appearing on dashboard.

        event(new Registered($user));

        Auth::login($user);

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
