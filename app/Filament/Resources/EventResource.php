<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Filament\Resources\EventResource\RelationManagers;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    
    protected static ?string $modelLabel = 'Event';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('General Info')
                        ->schema([
                            Forms\Components\TextInput::make('title')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\Select::make('category_id')
                                ->relationship('category', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Forms\Components\Textarea::make('description')
                                ->required()
                                ->columnSpanFull(),
                            Forms\Components\FileUpload::make('banner_image_path')
                                ->image()
                                ->disk('public')
                                ->directory('event-banners')
                                ->columnSpanFull(),
                        ])->columns(2),

                    Forms\Components\Wizard\Step::make('Schedule')
                        ->schema([
                            Forms\Components\DateTimePicker::make('start_at')
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(fn ($state, callable $set) => $set('date', $state)),
                            Forms\Components\DateTimePicker::make('end_at')
                                ->required()
                                ->afterOrEqual('start_at'),
                            Forms\Components\Select::make('timezone')
                                ->options(collect(timezone_identifiers_list())->mapWithKeys(fn ($tz) => [$tz => $tz]))
                                ->default('UTC')
                                ->searchable()
                                ->required(),
                            
                            Forms\Components\Section::make('Recurring Event')
                                ->schema([
                                    Forms\Components\Toggle::make('is_recurring')
                                        ->label('This is a recurring event')
                                        ->reactive(),
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\Select::make('recurrence_frequency')
                                                ->options([
                                                    Event::RECURRENCE_DAILY => 'Daily',
                                                    Event::RECURRENCE_WEEKLY => 'Weekly',
                                                    Event::RECURRENCE_BI_WEEKLY => 'Bi-Weekly',
                                                    Event::RECURRENCE_MONTHLY => 'Monthly',
                                                    Event::RECURRENCE_YEARLY => 'Yearly',
                                                    Event::RECURRENCE_CUSTOM => 'Custom',
                                                ])
                                                ->reactive()
                                                ->required(fn ($get) => $get('is_recurring')),
                                            Forms\Components\DateTimePicker::make('recurrence_end_at')
                                                ->label('Recur Until')
                                                ->required(fn ($get) => $get('is_recurring'))
                                                ->afterOrEqual('end_at'),
                                            
                                            Forms\Components\TextInput::make('recurrence_interval')
                                                ->numeric()
                                                ->default(1)
                                                ->visible(fn ($get) => $get('recurrence_frequency') === Event::RECURRENCE_CUSTOM)
                                                ->required(fn ($get) => $get('recurrence_frequency') === Event::RECURRENCE_CUSTOM),
                                            Forms\Components\Select::make('recurrence_unit')
                                                ->options([
                                                    Event::UNIT_DAY => 'Days',
                                                    Event::UNIT_WEEK => 'Weeks',
                                                    Event::UNIT_MONTH => 'Months',
                                                    Event::UNIT_YEAR => 'Years',
                                                ])
                                                ->visible(fn ($get) => $get('recurrence_frequency') === Event::RECURRENCE_CUSTOM)
                                                ->required(fn ($get) => $get('recurrence_frequency') === Event::RECURRENCE_CUSTOM),
                                        ])->visible(fn ($get) => $get('is_recurring')),
                                ]),
                        ])->columns(2),

                    Forms\Components\Wizard\Step::make('Location & Capacity')
                        ->schema([
                            Forms\Components\Select::make('venue_type')
                                ->options([
                                    'physical' => 'Physical Venue',
                                    'online' => 'Online Event',
                                ])
                                ->default('physical')
                                ->reactive()
                                ->required(),
                            Forms\Components\TextInput::make('location')
                                ->label('Physical Address')
                                ->visible(fn ($get) => $get('venue_type') === 'physical')
                                ->required(fn ($get) => $get('venue_type') === 'physical')
                                ->maxLength(255),
                            Forms\Components\TextInput::make('online_link')
                                ->url()
                                ->visible(fn ($get) => $get('venue_type') === 'online')
                                ->required(fn ($get) => $get('venue_type') === 'online'),
                            Forms\Components\TextInput::make('capacity')
                                ->numeric()
                                ->label('Max Attendees')
                                ->helperText('Maximum attendees across all ticket types'),
                        ])->columns(2),

                    Forms\Components\Wizard\Step::make('Visibility & Status')
                        ->schema([
                            Forms\Components\Select::make('status')
                                ->options([
                                    Event::STATUS_DRAFT => 'Draft',
                                    Event::STATUS_PUBLISHED => 'Published (Scheduled)',
                                    Event::STATUS_ONGOING => 'Ongoing',
                                    Event::STATUS_ENDED => 'Ended',
                                    Event::STATUS_CANCELLED => 'Cancelled',
                                ])
                                ->default(Event::STATUS_DRAFT)
                                ->required(),
                            Forms\Components\Select::make('visibility')
                                ->options([
                                    Event::VISIBILITY_DRAFT => 'Draft',
                                    Event::VISIBILITY_PUBLISHED => 'Public (Published)',
                                    Event::VISIBILITY_PRIVATE => 'Private (Invite Only)',
                                ])
                                ->default(Event::VISIBILITY_DRAFT)
                                ->required(),
                        ])->columns(2),
                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('banner_image_path')
                    ->label('Banner')
                    ->disk('public')
                    ->circular(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Event::STATUS_PUBLISHED => 'success',
                        Event::STATUS_ONGOING => 'info',
                        Event::STATUS_ENDED => 'gray',
                        Event::STATUS_DRAFT => 'gray',
                        Event::STATUS_CANCELLED => 'danger',
                        default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('visibility')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Event::VISIBILITY_PUBLISHED => 'success',
                        Event::VISIBILITY_PRIVATE => 'warning',
                        Event::VISIBILITY_DRAFT => 'gray',
                        default => 'gray',
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TicketTypesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\EventResource\Pages\ListEvents::route('/'),
            'create' => \App\Filament\Resources\EventResource\Pages\CreateEvent::route('/create'),
            'edit' => \App\Filament\Resources\EventResource\Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
