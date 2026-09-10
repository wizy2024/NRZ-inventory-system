<?php

namespace App\Filament\Resources\GatePasses\Pages;

use App\Filament\Resources\GatePasses\GatePassResource;
use App\Filament\Resources\GatePasses\Pages\Concerns\ValidatesGatePassData;
use App\Filament\Resources\Pages\Concerns\DisplaysValidationSummary;
use Filament\Resources\Pages\EditRecord;

class EditGatePass extends EditRecord
{
    use DisplaysValidationSummary;
    use ValidatesGatePassData;

    protected static string $resource = GatePassResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->validateGatePassData($data);
    }
}
