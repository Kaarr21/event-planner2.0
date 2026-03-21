<x-mail::message>
    # Welcome to Event Planner, {{ $user->name }}! 🥂

    We're thrilled to have you join our community of premium event organizers. Whether you're planning a corporate gala,
    a wedding, or an intimate gathering, we're here to help you make it extraordinary.

    <x-mail::panel>
        "The goal is not to live forever, the goal is to create something that will." - *Chuck Palahniuk*
    </x-mail::panel>

    ### Get Started Today
    Your account is ready! Dive into your dashboard to start creating your first gala event.

    <x-mail::button :url="route('dashboard')" color="primary">
        Go to Dashboard
    </x-mail::button>

    ### What's Next?
    * **Create an Event**: Start planning your first event in minutes.
    * **Invite Guests**: Use our easy guest list management.
    * **Track Plans**: Keep everything organized in one place.

    If you have any questions, simply reply to this email. We're always here to help.

    Best regards,<br>
    The {{ config('app.name') }} Team

    ---
    *If you did not create an account, no further action is required.*
</x-mail::message>