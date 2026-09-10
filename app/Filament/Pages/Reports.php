<?php

namespace App\Filament\Pages;

use App\Models\Asset;
use App\Models\Department;
use App\Models\InventoryReport;
use App\Models\Location;
use App\Models\MaintenanceLog;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use UnitEnum;

class Reports extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static string|UnitEnum|null $navigationGroup = 'Management';

    protected static ?string $navigationLabel = 'Reports';

    protected static ?string $title = 'NRZ Inventory Reports';

    protected string $view = 'filament.pages.reports';

    public static function canAccess(): bool
    {
        return Gate::allows('view reports');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createReport')
                ->label('Create report')
                ->icon(Heroicon::OutlinedPlus)
                ->form([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255),
                    Select::make('report_type')
                        ->options([
                            'inventory_summary' => 'Inventory summary',
                            'maintenance' => 'Maintenance report',
                            'warranty' => 'Warranty report',
                        ])
                        ->native(false)
                        ->required(),
                    Textarea::make('notes')
                        ->rows(3)
                        ->maxLength(1000),
                ])
                ->action(function (array $data): void {
                    DB::transaction(function () use ($data): void {
                        $summary = match ($data['report_type']) {
                            'inventory_summary' => [
                                'Total assets' => Asset::count(),
                                'Active assets' => Asset::where('status', 'active')->count(),
                                'In maintenance' => Asset::where('status', 'maintenance')->count(),
                                'Decommissioned' => Asset::where('status', 'decommissioned')->count(),
                                'Departments represented' => Asset::distinct('department_id')->count('department_id'),
                            ],
                            'maintenance' => [
                                'Total maintenance logs' => MaintenanceLog::count(),
                                'Pending' => MaintenanceLog::where('status', 'pending')->count(),
                                'In progress' => MaintenanceLog::where('status', 'in_progress')->count(),
                                'Resolved' => MaintenanceLog::where('status', 'resolved')->count(),
                                'Assets affected' => MaintenanceLog::distinct('asset_id')->count('asset_id'),
                            ],
                            'warranty' => [
                                'Expired warranties' => Asset::whereDate('warranty_expiry', '<', now())->count(),
                                'Expiring within 30 days' => Asset::whereDate('warranty_expiry', '>=', now())
                                    ->whereDate('warranty_expiry', '<=', now()->addDays(30))
                                    ->count(),
                                'No warranty date' => Asset::whereNull('warranty_expiry')->count(),
                                'Covered assets' => Asset::whereDate('warranty_expiry', '>=', now())->count(),
                            ],
                        };

                        InventoryReport::create([
                            ...$data,
                            'summary' => $summary,
                            'generated_by' => Auth::id(),
                            'generated_at' => now(),
                        ]);
                    });
                })
                ->successNotificationTitle('Report created'),
            Action::make('downloadPdf')
                ->label('Download PDF')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->url(route('reports.pdf'))
                ->openUrlInNewTab(),
        ];
    }

    protected function getViewData(): array
    {
        $now = now();

        return [
            'assets' => Asset::query()
                ->with(['department', 'location'])
                ->orderBy('department_id')
                ->orderBy('asset_tag')
                ->get(),
            'assetTypes' => Asset::query()
                ->selectRaw('type, COUNT(*) as total')
                ->groupBy('type')
                ->orderByDesc('total')
                ->get(),
            'departments' => Department::query()
                ->withCount('assets')
                ->orderByDesc('assets_count')
                ->get(),
            'locations' => Location::query()
                ->withCount('assets')
                ->orderByDesc('assets_count')
                ->get(),
            'maintenance' => MaintenanceLog::query()
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->orderByDesc('total')
                ->get(),
            'expiredWarranty' => Asset::query()
                ->whereNotNull('warranty_expiry')
                ->whereDate('warranty_expiry', '<', $now)
                ->count(),
            'expiringWarranty' => Asset::query()
                ->whereDate('warranty_expiry', '>=', $now)
                ->whereDate('warranty_expiry', '<=', $now->copy()->addDays(30))
                ->count(),
            'savedReports' => InventoryReport::query()
                ->with('generatedBy')
                ->latest('generated_at')
                ->limit(10)
                ->get(),
        ];
    }
}
