<?php

namespace App\Filament\Resources\MaintenanceLogResource\Pages;

use App\Filament\Resources\MaintenanceLogResource;
use App\Filament\Resources\Pages\Concerns\DisplaysValidationSummary;
use Filament\Resources\Pages\CreateRecord;

class CreateMaintenanceLog extends CreateRecord
{
    use DisplaysValidationSummary;

    protected static string $resource = MaintenanceLogResource::class;
}
