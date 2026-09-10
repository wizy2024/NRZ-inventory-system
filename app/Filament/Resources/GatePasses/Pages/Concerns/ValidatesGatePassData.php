<?php

namespace App\Filament\Resources\GatePasses\Pages\Concerns;

use App\Models\MaintenanceLog;
use Illuminate\Validation\ValidationException;

trait ValidatesGatePassData
{
    protected function validateGatePassData(array $data): array
    {
        if (filled($data['maintenance_log_id'] ?? null)) {
            $matchesAsset = MaintenanceLog::query()
                ->whereKey($data['maintenance_log_id'])
                ->where('asset_id', $data['asset_id'])
                ->where('status', 'resolved')
                ->exists();

            if (! $matchesAsset) {
                throw ValidationException::withMessages([
                    'maintenance_log_id' => 'The maintenance record must belong to the selected asset and be resolved.',
                ]);
            }
        }

        return $data;
    }
}
