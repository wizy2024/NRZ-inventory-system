<?php

namespace App\Filament\Resources\Audits\Pages;

use App\Filament\Resources\Audits\AuditResource;
use App\Models\Asset;
use App\Filament\Resources\Pages\Concerns\DisplaysValidationSummary;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateAudit extends CreateRecord
{
    use DisplaysValidationSummary;

    protected static string $resource = AuditResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $asset = Asset::with(['location', 'assignedTo'])->findOrFail($data['asset_id']);

        $data['audited_by'] = Auth::id();
        $data['checked_at'] = now();
        $data['expected_location'] = $asset->location?->name;
        $data['expected_assignee'] = $asset->assignedTo?->name;

        return $data;
    }
}
