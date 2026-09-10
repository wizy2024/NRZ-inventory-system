<?php

namespace App\Filament\Resources\GatePasses\Pages;

use App\Filament\Resources\GatePasses\GatePassResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGatePasses extends ListRecords
{
    protected static string $resource = GatePassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
