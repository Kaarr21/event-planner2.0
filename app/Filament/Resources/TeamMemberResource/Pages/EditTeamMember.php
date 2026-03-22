<?php

namespace App\Filament\Resources\TeamMemberResource\Pages;

use App\Filament\Resources\TeamMemberResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTeamMember extends EditRecord
{
    protected static string $resource = TeamMemberResource::class;

    public string $role = 'member';

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $tenantId = filament()->getTenant()->id;
        $pivot = $this->record->organizations()->where('organization_id', $tenantId)->first()->pivot ?? null;
        $data['role'] = $pivot ? $pivot->role : 'member';
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['role'])) {
            $this->role = $data['role'];
            unset($data['role']);
        }
        return $data;
    }

    protected function afterSave(): void
    {
        $tenantId = filament()->getTenant()->id;
        $this->record->organizations()->updateExistingPivot($tenantId, ['role' => $this->role]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
