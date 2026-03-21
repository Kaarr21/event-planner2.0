<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Event;
use App\Models\EventOrganizer;
use App\Models\RSVP;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'edit_event',
            'manage_invites',
            'manage_tasks',
            'assign_tasks',
            'view_rsvps',
            'view_tasks',
            'view_guest_list',
            'manage_files',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign existing permissions
        $ownerRole = Role::firstOrCreate(['name' => 'owner']);
        $ownerRole->givePermissionTo(Permission::all());

        $organizerRole = Role::firstOrCreate(['name' => 'organizer']);
        $guestRole = Role::firstOrCreate(['name' => 'guest']);

        // --- DATA MIGRATION ---

        // 1. Migrate Owners
        $events = Event::all();
        foreach ($events as $event) {
            setPermissionsTeamId($event->id);
            $owner = User::find($event->user_id);
            if ($owner) {
                $owner->assignRole('owner');
            }
        }

        // 2. Migrate Organizers
        $organizers = DB::table('event_organizers')->get();
        foreach ($organizers as $org) {
            setPermissionsTeamId($org->event_id);
            $user = User::find($org->user_id);
            if ($user) {
                $user->assignRole('organizer');
                
                $perms = json_decode($org->permissions, true) ?: [];
                // Map old permission names if they differ, but here they seem identical
                foreach ($perms as $perm) {
                    if (in_array($perm, $permissions)) {
                        $user->givePermissionTo($perm);
                    }
                }
            }
        }

        // 3. Migrate Guests (RSVPs)
        $rsvps = RSVP::all();
        foreach ($rsvps as $rsvp) {
            setPermissionsTeamId($rsvp->event_id);
            $user = User::find($rsvp->user_id);
            if ($user) {
                $user->assignRole('guest');
                
                if ($rsvp->can_view_guests) {
                    $user->givePermissionTo('view_guest_list');
                }
                if ($rsvp->can_view_checklist) {
                    $user->givePermissionTo('view_tasks');
                }
            }
        }
    }
}
