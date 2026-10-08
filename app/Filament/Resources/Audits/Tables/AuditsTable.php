<?php

namespace App\Filament\Resources\Audits\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                TextColumn::make('follow_up_status')
                    ->label('Follow-up')
                    ->formatStateUsing(fn (string $state): string => str_replace('_', ' ', ucfirst($state)))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'resolved', 'not_required' => 'success',
                        'in_progress' => 'warning',
                        default => 'danger',
                    }),
                TextColumn::make('follow_up_due_at')
                    ->label('Due')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('result')
                    ->options([
                        'found' => 'Found',
                        'missing' => 'Missing',
                        'damaged' => 'Damaged',
                        'wrong_location' => 'Wrong location',
                    ]),
                SelectFilter::make('follow_up_status')
                    ->options([
                        'not_required' => 'Not required',
                        'open' => 'Open',
                        'in_progress' => 'In progress',
                        'resolved' => 'Resolved',
                    ]),
                \Filament\Tables\Filters\Filter::make('overdue_follow_up')
                    ->label('Overdue follow-up')
                    ->query(fn (Builder $query): Builder => $query
                        ->whereIn('follow_up_status', ['open', 'in_progress'])
                        ->whereNotNull('follow_up_due_at')
                        ->where('follow_up_due_at', '<', now())),
            ])
            ->recordActions([
                Action::make('follow_up')
                    ->label('Update follow-up')
                    ->icon('heroicon-o-arrow-path')
                    ->form([
                        Select::make('follow_up_status')
                            ->options([
                                'not_required' => 'Not required',
                                'open' => 'Open',
                                'in_progress' => 'In progress',
                                'resolved' => 'Resolved',
                            ])
                            ->required(),
                        Select::make('follow_up_owner_id')
                            ->relationship('followUpOwner', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        DateTimePicker::make('follow_up_due_at')->nullable(),
                        Textarea::make('follow_up_notes')->rows(3)->nullable(),
                    ])
                    ->fillForm(fn ($record): array => [
                        'follow_up_status' => $record->follow_up_status,
                        'follow_up_owner_id' => $record->follow_up_owner_id,
                        'follow_up_due_at' => $record->follow_up_due_at,
                        'follow_up_notes' => $record->follow_up_notes,
                    ])
                    ->action(fn ($record, array $data): bool => (bool) $record->update($data)),
            ])
            ->toolbarActions([]);
    }
}
