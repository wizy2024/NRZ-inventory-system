<?php

namespace App\Filament\Resources\Audits\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AuditForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('asset_id')
                    ->label('Asset')
                    ->relationship('asset', 'asset_tag')
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->default(fn (): ?int => request()->integer('asset_id') ?: null)
                    ->required(),
                Select::make('result')
                    ->label('Verification result')
                    ->options([
                        'found' => 'Found and verified',
                        'missing' => 'Missing',
                        'damaged' => 'Damaged',
                        'wrong_location' => 'Wrong location',
                    ])
                    ->native(false)
                    ->required()
                    ->default('found'),
                TextInput::make('observed_location')
                    ->label('Observed location')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Where the asset was found'),
                DateTimePicker::make('checked_at')
                    ->label('Checked at')
                    ->native(false)
                    ->default(now())
                    ->maxDate(now())
                    ->required(),
                Textarea::make('notes')
                    ->label('Audit notes')
                    ->rows(4)
                    ->placeholder('Record condition, location discrepancy, or follow-up action.')
                    ->requiredIf('result', 'missing')
                    ->requiredIf('result', 'damaged')
                    ->requiredIf('result', 'wrong_location')
                    ->nullable()
                    ->columnSpanFull(),
            ]);
    }
}
