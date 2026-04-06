<?php

namespace App\Livewire\Events;

use App\Models\Event;
use App\Models\EventMedia;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class MediaLibrary extends Component
{
    use WithFileUploads;

    public Event $event;
    public $activeFolder = 'team'; // team, shared
    public $uploads = [];
    public $userPermissions = [];
    public $userRole = 'guest';

    // Settings
    public $allowGuestUploads = false;
    public $allowGuestViewShared = true;

    // Restriction Management
    public $selectingAccessFor = null;
    public $selectedGuestIds = [];

    protected $listeners = ['refreshMedia' => '$refresh'];

    public function mount(Event $event, $userPermissions = [], $userRole = 'guest')
    {
        $this->event = $event;
        $this->userPermissions = $userPermissions;
        $this->userRole = $userRole;

        $this->allowGuestUploads = $event->getSetting('allow_guest_uploads', false);
        $this->allowGuestViewShared = $event->getSetting('allow_guest_view_shared', true);
        
        // If guest and can only see shared, force shared
        if ($this->userRole === 'guest' && !$this->hasPermission('manage_files')) {
            $this->activeFolder = 'shared';
        }
    }

    public function hasPermission($permission)
    {
        $user = Auth::user();
        if (!$user) return false;

        setPermissionsTeamId($this->event->id);
        return $user->hasRole('owner') || $user->hasPermissionTo($permission);
    }

    public function updatedUploads()
    {
        $this->validate([
            'uploads.*' => 'image|max:10240', // 10MB limit
        ]);

        foreach ($this->uploads as $upload) {
            $path = $upload->store("events/{$this->event->id}/{$this->activeFolder}", 'public');

            $this->event->media()->create([
                'user_id' => Auth::id(),
                'file_name' => $upload->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $upload->getMimeType(),
                'file_size' => $upload->getSize(),
                'folder_type' => $this->activeFolder,
                'visibility' => $this->activeFolder === 'team' ? 'private' : 'public',
            ]);
        }

        $this->reset('uploads');
        $this->event->refresh();
        session()->flash('media_message', 'Files uploaded successfully!');
    }

    public function setFolder($folder)
    {
        if ($folder === 'team' && !$this->hasPermission('manage_files') && $this->userRole === 'guest') {
            return;
        }
        $this->activeFolder = $folder;
    }

    public function deleteMedia($mediaId)
    {
        $media = EventMedia::findOrFail($mediaId);
        
        // Only owner, uploader or manager can delete
        if ($media->user_id === Auth::id() || Auth::user()->hasRole('owner') || $this->hasPermission('manage_files')) {
            Storage::disk('public')->delete($media->file_path);
            $media->delete();
            $this->event->refresh();
            session()->flash('media_message', 'File deleted.');
        }
    }

    public function toggleGuestUploads()
    {
        if (!$this->hasPermission('owner') && !$this->hasPermission('manage_files')) return;

        $settings = $this->event->settings ?? [];
        $settings['allow_guest_uploads'] = !$this->allowGuestUploads;
        $this->event->update(['settings' => $settings]);
        $this->allowGuestUploads = $settings['allow_guest_uploads'];
    }

    public function toggleGuestView()
    {
        if (!$this->hasPermission('owner') && !$this->hasPermission('manage_files')) return;

        $settings = $this->event->settings ?? [];
        $settings['allow_guest_view_shared'] = !$this->allowGuestViewShared;
        $this->event->update(['settings' => $settings]);
        $this->allowGuestViewShared = $settings['allow_guest_view_shared'];
    }

    public function startRestricting($mediaId)
    {
        if (!$this->hasPermission('owner') && !$this->hasPermission('manage_files')) return;

        $this->selectingAccessFor = $mediaId;
        $media = EventMedia::find($mediaId);
        $this->selectedGuestIds = $media->authorizedUsers()->pluck('users.id')->toArray();
    }

    public function saveAccess()
    {
        if ($this->selectingAccessFor) {
            $media = EventMedia::find($this->selectingAccessFor);
            $media->authorizedUsers()->sync($this->selectedGuestIds);
            
            if (empty($this->selectedGuestIds)) {
                $media->update(['visibility' => $this->activeFolder === 'team' ? 'private' : 'public']);
            } else {
                $media->update(['visibility' => 'restricted']);
            }
        }

        $this->selectingAccessFor = null;
        session()->flash('media_message', 'Access updated.');
    }

    public function makePublic($mediaId)
    {
        if (!$this->hasPermission('owner') && !$this->hasPermission('manage_files')) return;

        $media = EventMedia::find($mediaId);
        $media->update(['visibility' => 'public', 'folder_type' => 'shared']);
        $this->event->refresh();
    }

    public function render()
    {
        if ($this->userRole === 'guest' && !$this->hasPermission('manage_files')) {
            $mediaQuery = $this->event->media();
            
            $mediaQuery->where(function($q) {
                // 1. Files in 'shared' folder that are 'public' (if vision is on)
                if ($this->allowGuestViewShared) {
                    $q->where(function($sq) {
                        $sq->where('folder_type', 'shared')
                          ->where('visibility', 'public');
                    });
                }
                
                // 2. Files I uploaded myself (regardless of folder)
                $q->orWhere('user_id', Auth::id());
                
                // 3. Files specifically restricted to me (regardless of folder)
                $q->orWhereHas('authorizedUsers', function($sq) {
                    $sq->where('user_id', Auth::id());
                });
            });
            
            $media = $mediaQuery->latest()->get();
        } else {
            // Owners/Organizers see everything in the current folder
            $media = $this->event->media()
                ->where('folder_type', $this->activeFolder)
                ->latest()
                ->get();
        }

        return view('livewire.events.media-library', [
            'mediaItems' => $media,
            'allGuests' => $this->event->rsvps()->with('user')->get()->pluck('user'),
        ]);
    }
}
