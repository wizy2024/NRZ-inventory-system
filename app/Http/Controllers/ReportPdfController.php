<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Department;
use App\Models\InventoryReport;
use App\Models\Location;
use App\Models\MaintenanceLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class ReportPdfController extends Controller
{
    public function __invoke(): Response
    {
        Gate::authorize('view reports');

        $now = now();
        $assets = Asset::query()
            ->with(['department', 'location'])
            ->orderBy('department_id')
            ->orderBy('asset_tag')
            ->get();

        $pdf = Pdf::loadView('reports.pdf', [
            'generatedAt' => $now,
            'assets' => $assets,
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
        ])->setPaper('a4', 'landscape');

        return $pdf->download('nrz-inventory-report-' . $now->format('Y-m-d-His') . '.pdf');
    }
}
