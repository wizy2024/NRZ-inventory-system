<?php

namespace App\Filament\Resources\MaintenanceLogResource\Pages;

use App\Filament\Resources\MaintenanceLogResource;
use App\Filament\Resources\Pages\Concerns\DisplaysValidationSummary;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMaintenanceLog extends EditRecord
{
    use DisplaysValidationSummary;

    protected static string $resource = MaintenanceLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
