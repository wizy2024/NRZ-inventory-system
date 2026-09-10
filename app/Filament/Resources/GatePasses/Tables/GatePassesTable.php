<?php

namespace App\Filament\Resources\GatePasses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GatePassesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pass_number')
                    ->label('Pass number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('asset.asset_tag')
                    ->label('Machine')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('collector_name')
                    ->label('Collected by')
                    ->searchable(),
                TextColumn::make('asset.type')
                    ->label('Type')
                    ->toggleable(),
                TextColumn::make('released_at')
                    ->label('Released')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('issuer.name')
                    ->label('Issued by')
                    ->toggleable(),
            ])
            ->filters([
                Filter::make('released_today')
                    ->label('Released today')
                    ->query(fn (Builder $query): Builder => $query->whereDate('released_at', today())),
            ])
            ->recordActions([
                Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->url(fn ($record): string => route('gate-passes.print', $record))
                    ->openUrlInNewTab(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
