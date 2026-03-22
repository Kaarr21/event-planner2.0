<?php

namespace App\Filament\Pages\Tenancy;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\EditTenantProfile;

class EditOrganizationProfile extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Organization Profile';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Select::make('type')
                    ->options([
                        'Corporate' => 'Corporate',
                        'NGO' => 'NGO',
                        'Church' => 'Church / Religious',
                        'University' => 'University / Education',
                        'Community' => 'Community Group',
                        'Other' => 'Other',
                    ])
                    ->required(),
                TextInput::make('website_url')
                    ->url()
                    ->maxLength(255),
                FileUpload::make('logo_path')
                    ->image()
                    ->directory('organizations/logos'),
                Textarea::make('bio')
                    ->columnSpanFull(),
            ]);
    }
}
