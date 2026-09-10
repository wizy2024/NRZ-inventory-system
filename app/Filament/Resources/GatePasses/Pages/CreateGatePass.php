<?php

namespace App\Filament\Resources\GatePasses\Pages;

use App\Filament\Resources\GatePasses\GatePassResource;
use App\Filament\Resources\GatePasses\Pages\Concerns\ValidatesGatePassData;
use App\Filament\Resources\Pages\Concerns\DisplaysValidationSummary;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateGatePass extends CreateRecord
{
    use DisplaysValidationSummary;
    use ValidatesGatePassData;

    protected static string $resource = GatePassResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = $this->validateGatePassData($data);
        $data['issued_by'] = Auth::id();

        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Gate pass created';
    }

    protected function getRedirectUrl(): string
    {
        return route('gate-passes.print', $this->record);
    }
}
