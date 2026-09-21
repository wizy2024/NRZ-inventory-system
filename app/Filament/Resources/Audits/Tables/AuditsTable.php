<?php

namespace App\Filament\Resources\Audits\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AuditsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('asset.asset_tag')
                    ->label('Asset')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('expected_location')
                    ->label('Expected location')
                    ->searchable(),
                TextColumn::make('observed_location')
                    ->label('Observed location')
                    ->searchable(),
                TextColumn::make('expected_assignee')
                    ->label('Expected assignee')
                    ->searchable()
                    ->placeholder('Unassigned'),
                TextColumn::make('result')
                    ->label('Result')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'wrong_location' => 'Wrong location',
                        default => ucfirst($state),
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'found' => 'success',
                        'damaged' => 'warning',
                        'missing' => 'danger',
                        default => 'info',
                    }),
                TextColumn::make('auditor.name')
                    ->label('Checked by')
                    ->searchable(),
                TextColumn::make('checked_at')
                    ->label('Checked at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
