<?php

namespace App\Filament\Resources\EventResource\RelationManagers;

use App\Models\TicketType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketTypesRelationManager extends RelationManager
{
    protected static string $relationship = 'ticketTypes';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('type')
                            ->options([
                                'regular' => 'Regular',
                                'free' => 'Free',
                                'early_bird' => 'Early Bird',
                                'vip' => 'VIP',
                                'group' => 'Group',
                            ])
                            ->required()
                            ->default('regular'),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Pricing & Capacity')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->numeric()
                            ->prefix('KES')
                            ->default(0)
                            ->required(),
                        Forms\Components\TextInput::make('capacity')
                            ->numeric()
                            ->label('Total Quantity Available')
                            ->helperText('Leave empty for unlimited'),
                        Forms\Components\TextInput::make('sold_count')
                            ->numeric()
                            ->disabled()
                            ->default(0)
                            ->label('Quantity Sold'),
                        Forms\Components\Toggle::make('is_transferable')
                            ->label('Transferable')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('Sales Window & Limits')
                    ->schema([
                        Forms\Components\DateTimePicker::make('sale_start_date')
                            ->label('Sale Start Date'),
                        Forms\Components\DateTimePicker::make('sale_end_date')
                            ->label('Sale End Date'),
                        Forms\Components\TextInput::make('min_per_purchase')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->required(),
                        Forms\Components\TextInput::make('max_per_purchase')
                            ->numeric()
                            ->minValue(1),
                    ])->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'early_bird' => 'warning',
                        'vip' => 'success',
                        'free' => 'info',
                        'group' => 'primary',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('price')
                    ->money('KES')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sold_count')
                    ->label('Sold')
                    ->description(fn (TicketType $record): string => $record->capacity ? "/ {$record->capacity}" : 'unlimited'),
                Tables\Columns\TextColumn::make('sale_end_date')
                    ->dateTime()
                    ->sortable()
                    ->label('Sales End'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }
}
