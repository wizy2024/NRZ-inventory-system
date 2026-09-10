<?php

namespace App\Filament\Resources\Pages\Concerns;

use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

trait DisplaysValidationSummary
{
    protected function onValidationError(ValidationException $exception): void
    {
        parent::onValidationError($exception);

        $messages = array_values(array_unique($exception->validator->errors()->all()));

        Notification::make()
            ->danger()
            ->title('Please correct the following errors')
            ->body(implode("\n", array_map(
                static fn (string $message): string => '- ' . $message,
                $messages,
            )))
            ->persistent()
            ->send();
    }
}
