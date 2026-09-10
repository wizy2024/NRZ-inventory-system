<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Filament\Resources\Pages\Concerns\DisplaysValidationSummary;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    use DisplaysValidationSummary;

    protected static string $resource = UserResource::class;
}
