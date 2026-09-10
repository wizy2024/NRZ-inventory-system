<?php

namespace App\Filament\Resources\Assets\Pages;

use App\Filament\Resources\Assets\AssetResource;
use App\Filament\Resources\Pages\Concerns\DisplaysValidationSummary;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAsset extends EditRecord
{
    use DisplaysValidationSummary;

    protected static string $resource = AssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
