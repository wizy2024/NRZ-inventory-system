<?php

namespace App\Filament\Resources\Assets\Tables;

use App\Models\Asset;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\URL;
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
                        $baseUrl = rtrim((string) config('app.qr_base_url', config('app.url')), '/');

                        if (blank($baseUrl) || parse_url($baseUrl, PHP_URL_HOST) === null) {
                            throw new \RuntimeException('QR_BASE_URL must be a complete URL reachable by the device scanning the QR code.');
                        }

                        URL::forceRootUrl($baseUrl);

                        try {
                            $url = URL::signedRoute('assets.info', ['asset' => $record]);
                        } finally {
                            URL::forceRootUrl(null);
                        }

                        return view('assets.qr-code', [
                            'asset' => $record,
                            'url' => $url,
                            'qrCode' => QrCode::format('svg')
                                ->size(240)
                                ->margin(2)
                                ->errorCorrection('H')
                                ->generate($url),
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
