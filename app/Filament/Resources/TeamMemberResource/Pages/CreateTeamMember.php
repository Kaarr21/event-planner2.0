<?php

namespace App\Filament\Resources\TeamMemberResource\Pages;

use App\Filament\Resources\TeamMemberResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTeamMember extends CreateRecord
{
    protected static string $resource = TeamMemberResource::class;

    public string $role = 'member';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Extract the role from the form
        if (isset($data['role'])) {
            $this->role = $data['role'];
            unset($data['role']);
        }

        // Set a secure random password since this is just an invite/creation from the admin
        if (!isset($data['password'])) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(12));
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $tenantId = filament()->getTenant()->id;
        // Filament Tenancy automatically attaches the record to the default relationship.
        // We just need to update the pivot with the specific role.
        $this->record->organizations()->updateExistingPivot($tenantId, ['role' => $this->role]);
    }
}
