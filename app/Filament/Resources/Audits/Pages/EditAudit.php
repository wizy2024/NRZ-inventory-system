<?php

namespace App\Filament\Resources\Audits\Pages;

use App\Filament\Resources\Audits\AuditResource;
use App\Filament\Resources\Pages\Concerns\DisplaysValidationSummary;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAudit extends EditRecord
{
    use DisplaysValidationSummary;

    protected static string $resource = AuditResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
