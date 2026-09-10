<?php

namespace App\Filament\Resources\Assets\Pages;

use App\Filament\Resources\Assets\AssetResource;
use App\Filament\Resources\Pages\Concerns\DisplaysValidationSummary;
use Filament\Resources\Pages\CreateRecord;

class CreateAsset extends CreateRecord
{
    use DisplaysValidationSummary;

    protected static string $resource = AssetResource::class;

    protected function getRedirectUrl(): string
    {
        return AssetResource::getUrl('index', [
            'highlight' => $this->record->getKey(),
        ]);
    }
}
