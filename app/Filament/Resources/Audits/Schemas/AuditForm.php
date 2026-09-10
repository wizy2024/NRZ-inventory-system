<?php

namespace App\Filament\Resources\Audits\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

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
                Select::make('audited_by')
                    ->label('Checked by')
                    ->relationship('auditor', 'name')
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->default(fn (): ?int => Auth::id())
                    ->required(),
                DateTimePicker::make('checked_at')
                    ->label('Checked at')
                    ->native(false)
                    ->default(now())
                    ->required(),
                Textarea::make('notes')
                    ->label('Audit notes')
                    ->rows(4)
                    ->placeholder('Record condition, location discrepancy, or follow-up action.')
                    ->nullable()
                    ->columnSpanFull(),
            ]);
    }
}
