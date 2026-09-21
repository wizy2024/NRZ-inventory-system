<?php

namespace App\Filament\Resources\Assets\Tables;

use App\Models\Asset;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('asset_tag')
                    ->searchable(),
                TextColumn::make('serial_number')
                    ->searchable(),
                TextColumn::make('mac_address')
                    ->searchable(),
                TextColumn::make('type')
                    ->searchable(),
                TextColumn::make('brand')
                    ->searchable(),
                TextColumn::make('purchase_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('warranty_expiry')
                    ->date()
                    ->sortable(),
                TextColumn::make('department.name')
                    ->searchable(),
                TextColumn::make('location.name')
                    ->searchable(),
                TextColumn::make('assignedTo.name')
                    ->label('Assigned to')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('condemned_at')
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordClasses(fn (Asset $record): ?string => request()->integer('highlight') === $record->getKey()
                ? 'bg-primary-50 ring-2 ring-inset ring-primary-500 dark:bg-primary-400/10'
                : null)
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('qr_code')
                    ->label('QR code')
                    ->icon('heroicon-o-qr-code')
                    ->modalHeading(fn (Asset $record): string => "QR code: {$record->asset_tag}")
                    ->modalContent(function (Asset $record) {
                        $record->loadMissing(['department', 'location', 'assignedTo']);

                        $qrData = implode("\n", [
                            'NRZ INVENTORY ASSET',
                            "Asset tag: {$record->asset_tag}",
                            "Serial number: {$record->serial_number}",
                            "MAC address: " . ($record->mac_address ?: 'N/A'),
                            "Type: {$record->type}",
                            "Brand: {$record->brand}",
                            "Department: " . ($record->department?->name ?: 'N/A'),
                            "Location: " . ($record->location?->name ?: 'N/A'),
                            "Assigned to: " . ($record->assignedTo?->name ?: 'Unassigned'),
                            "Status: {$record->status}",
                            "Purchase date: " . ($record->purchase_date?->format('Y-m-d') ?: 'N/A'),
                            "Warranty expiry: " . ($record->warranty_expiry?->format('Y-m-d') ?: 'N/A'),
                        ]);

                        return view('assets.qr-code', [
                            'asset' => $record,
                            'qrCode' => QrCode::format('svg')
                                ->size(240)
                                ->margin(2)
                                ->errorCorrection('H')
                                ->generate($qrData),
                        ]);
                    })
                    ->modalSubmitAction(false),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
