<?php

namespace App\Filament\Resources\Audits\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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
                TextColumn::make('asset.location.name')
                    ->label('Recorded location')
                    ->searchable(),
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
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
