<?php

namespace App\Filament\Resources\GatePasses\Schemas;

use App\Models\Asset;
use App\Models\MaintenanceLog;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class GatePassForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('pass_number')
                    ->label('Gate pass number')
                    ->default(fn (): string => 'GP-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(4)))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->readOnly(),
                Select::make('asset_id')
                    ->label('Machine collected')
                    ->native(false)
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search): array => Asset::query()
                        ->where('asset_tag', 'like', "%{$search}%")
                        ->orWhere('serial_number', 'like', "%{$search}%")
                        ->orderBy('asset_tag')
                        ->limit(50)
                        ->get()
                        ->mapWithKeys(fn (Asset $asset): array => [
                            $asset->id => $asset->asset_tag . ' - ' . $asset->type . ' / ' . $asset->brand,
                        ])
                        ->all())
                    ->getOptionLabelUsing(fn ($value): ?string => Asset::find($value)?->asset_tag)
                    ->required(),
                Select::make('maintenance_log_id')
                    ->label('Maintenance record')
                    ->native(false)
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search): array => MaintenanceLog::query()
                        ->with('asset')
                        ->where('status', 'resolved')
                        ->whereHas('asset', fn ($query) => $query
                            ->where('asset_tag', 'like', "%{$search}%"))
                        ->latest()
                        ->limit(50)
                        ->get()
                        ->mapWithKeys(fn (MaintenanceLog $log): array => [
                            $log->id => $log->asset?->asset_tag . ' - ' . $log->symptom,
                        ])
                        ->all())
                    ->getOptionLabelUsing(fn ($value): ?string => MaintenanceLog::with('asset')->find($value)?->asset?->asset_tag)
                    ->nullable()
                    ->helperText('Select the repair record when one is available.'),
                TextInput::make('collector_name')
                    ->label('Collected by')
                    ->placeholder('Full name of the person collecting the machine')
                    ->required()
                    ->maxLength(255),
                TextInput::make('collector_contact')
                    ->label('Contact number')
                    ->maxLength(255),
                TextInput::make('collector_id_number')
                    ->label('ID / employee number')
                    ->maxLength(255),
                DateTimePicker::make('released_at')
                    ->label('Release date and time')
                    ->default(now())
                    ->native(false)
                    ->required(),
                Textarea::make('notes')
                    ->label('Notes')
                    ->placeholder('Optional handover notes or conditions')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
