<?php

namespace App\Filament\Resources\Audits\Pages;

use App\Filament\Resources\Audits\AuditResource;
use App\Filament\Resources\Pages\Concerns\DisplaysValidationSummary;
use Filament\Resources\Pages\CreateRecord;

class CreateAudit extends CreateRecord
{
    use DisplaysValidationSummary;

    protected static string $resource = AuditResource::class;
}
