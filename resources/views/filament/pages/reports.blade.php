<x-filament-panels::page>
    @php
        $assetTotal = max(1, $assets->count());
        $assetGroups = $assets->groupBy(fn ($asset) => $asset->department?->name ?? 'Unassigned');
        $statusStyles = [
            'active' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300',
            'decommissioned' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
        ];
        $maintenanceStyles = [
            'pending' => 'border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-400/20 dark:bg-amber-400/10 dark:text-amber-300',
            'in_progress' => 'border-blue-200 bg-blue-50 text-blue-700 dark:border-blue-400/20 dark:bg-blue-400/10 dark:text-blue-300',
            'resolved' => 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-400/20 dark:bg-emerald-400/10 dark:text-emerald-300',
        ];
    @endphp

    <style>
        @media print {
            .fi-sidebar, .fi-topbar, .fi-header, .report-actions { display: none !important; }
            .fi-main { margin: 0 !important; padding: 0 !important; }
            .report-shell { max-width: none !important; }
            .report-section { break-inside: avoid; }
        }
    </style>

    <div class="report-shell space-y-8">
        <header class="flex flex-col gap-5 border-b border-gray-200 pb-6 sm:flex-row sm:items-end sm:justify-between dark:border-white/10">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-primary-600 dark:text-primary-400">Management / intelligence</p>
                <h1 class="mt-2 text-3xl font-bold tracking-tight text-gray-950 dark:text-white">NRZ Inventory Reports</h1>
                <p class="mt-2 max-w-2xl text-sm text-gray-500 dark:text-gray-400">A single view of what is owned, where it is deployed, and what needs attention.</p>
            </div>
            <div class="report-actions flex items-center gap-4">
                <div class="text-right text-xs text-gray-500 dark:text-gray-400"><p>Generated</p><p class="mt-1 font-semibold text-gray-700 dark:text-gray-200">{{ now()->format('d M Y, H:i') }}</p></div>
                <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-500"><x-filament::icon icon="heroicon-o-printer" class="h-4 w-4" />Print report</button>
            </div>
        </header>

        <section class="report-section" aria-labelledby="saved-reports-heading">
            <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div><p class="text-xs font-bold uppercase tracking-[0.16em] text-gray-400">Saved reports</p><h2 id="saved-reports-heading" class="mt-1 text-lg font-semibold text-gray-950 dark:text-white">Recently generated</h2><p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Create a named snapshot of the current inventory position.</p></div>
            </div>
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-white/10 dark:bg-gray-900">
                @forelse ($savedReports as $report)
                    <div class="border-b border-gray-100 px-5 py-4 last:border-b-0 dark:border-white/10"><div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"><div><p class="font-semibold text-gray-950 dark:text-white">{{ $report->title }}</p><p class="text-sm text-gray-500 dark:text-gray-400">{{ str_replace('_', ' ', $report->report_type) }} · {{ $report->generatedBy?->name ?? 'System' }}</p></div><time class="text-sm text-gray-500 dark:text-gray-400">{{ $report->generated_at?->format('d M Y, H:i') }}</time></div><div class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-4">@foreach (($report->summary ?? []) as $label => $value)<div class="rounded-lg bg-gray-50 px-3 py-2 dark:bg-white/5"><p class="text-xs text-gray-500 dark:text-gray-400">{{ $label }}</p><p class="mt-1 text-lg font-semibold text-gray-950 dark:text-white">{{ $value }}</p></div>@endforeach</div>@if ($report->notes)<p class="mt-3 text-sm text-gray-600 dark:text-gray-300">{{ $report->notes }}</p>@endif</div>
                @empty
                    <p class="px-5 py-6 text-sm text-gray-500 dark:text-gray-400">No saved reports yet. Use Create report to generate the first snapshot.</p>
                @endforelse
            </div>
        </section>

        <section class="report-section" aria-labelledby="overview-heading">
            <div class="mb-4"><p class="text-xs font-bold uppercase tracking-[0.16em] text-gray-400">01 / Overview</p><h2 id="overview-heading" class="mt-1 text-lg font-semibold text-gray-950 dark:text-white">Portfolio at a glance</h2></div>
            <div class="grid grid-cols-2 overflow-hidden rounded-xl border border-gray-200 bg-white sm:grid-cols-4 dark:border-white/10 dark:bg-gray-900">
                <div class="border-b border-r border-gray-200 p-5 sm:border-b-0 dark:border-white/10"><p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Total assets</p><p class="mt-2 text-3xl font-bold text-gray-950 dark:text-white">{{ $assets->count() }}</p></div>
                <div class="border-b border-gray-200 p-5 sm:border-b-0 sm:border-r dark:border-white/10"><p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Departments</p><p class="mt-2 text-3xl font-bold text-gray-950 dark:text-white">{{ $departments->count() }}</p></div>
                <div class="border-r border-gray-200 p-5 dark:border-white/10"><p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Expired warranty</p><p class="mt-2 text-3xl font-bold text-rose-600 dark:text-rose-400">{{ $expiredWarranty }}</p></div>
                <div class="p-5"><p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Expiring in 30 days</p><p class="mt-2 text-3xl font-bold text-amber-600 dark:text-amber-400">{{ $expiringWarranty }}</p></div>
            </div>
        </section>

        <section class="report-section" aria-labelledby="assets-heading">
            <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between"><div><p class="text-xs font-bold uppercase tracking-[0.16em] text-gray-400">02 / Ownership</p><h2 id="assets-heading" class="mt-1 text-lg font-semibold text-gray-950 dark:text-white">Asset register</h2><p class="mt-1 text-sm text-gray-500">Every registered asset, grouped by department.</p></div><a href="{{ url('/wizy/assets') }}" class="report-actions text-sm font-semibold text-primary-600 hover:text-primary-500">Open asset register <span aria-hidden="true">&rarr;</span></a></div>
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-white/10 dark:bg-gray-900">
                @forelse ($assetGroups as $departmentName => $departmentAssets)
                    <div class="border-b border-gray-200 last:border-b-0 dark:border-white/10"><div class="flex items-center justify-between bg-gray-50 px-5 py-3 dark:bg-gray-800/60"><h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $departmentName }}</h3><span class="text-xs font-medium text-gray-500">{{ $departmentAssets->count() }} {{ $departmentAssets->count() === 1 ? 'asset' : 'assets' }}</span></div><div class="overflow-x-auto"><table class="w-full min-w-[900px] text-left text-sm"><thead class="border-b border-gray-200 text-xs uppercase tracking-wide text-gray-500 dark:border-white/10"><tr><th class="px-5 py-3 font-semibold">Asset tag</th><th class="px-5 py-3 font-semibold">Type / brand</th><th class="px-5 py-3 font-semibold">Serial number</th><th class="px-5 py-3 font-semibold">Location</th><th class="px-5 py-3 font-semibold">Warranty expiry</th><th class="px-5 py-3 font-semibold">Status</th></tr></thead><tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @foreach ($departmentAssets as $asset)
                            <tr class="text-gray-700 dark:text-gray-200"><td class="whitespace-nowrap px-5 py-3 font-semibold text-gray-950 dark:text-white">{{ $asset->asset_tag }}</td><td class="px-5 py-3"><span class="block font-medium">{{ $asset->type }}</span><span class="text-xs text-gray-500">{{ $asset->brand }}</span></td><td class="whitespace-nowrap px-5 py-3 font-mono text-xs">{{ $asset->serial_number }}</td><td class="whitespace-nowrap px-5 py-3">{{ $asset->location?->name ?? 'Unassigned' }}</td><td class="whitespace-nowrap px-5 py-3">{{ $asset->warranty_expiry?->format('d M Y') ?? 'Not set' }}</td><td class="px-5 py-3"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusStyles[$asset->status] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">{{ str_replace('_', ' ', $asset->status) }}</span></td></tr>
                        @endforeach
                    </tbody></table></div></div>
                @empty
                    <div class="px-5 py-10 text-center text-sm text-gray-500">No assets are available for this report.</div>
                @endforelse
            </div>
        </section>

        <section class="report-section" aria-labelledby="risk-heading">
            <div class="mb-4"><p class="text-xs font-bold uppercase tracking-[0.16em] text-gray-400">03 / Attention</p><h2 id="risk-heading" class="mt-1 text-lg font-semibold text-gray-950 dark:text-white">Operational risk</h2><p class="mt-1 text-sm text-gray-500">Maintenance workload and warranty exposure that may need action.</p></div>
            <div class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-white/10 dark:bg-gray-900"><div class="flex items-start justify-between gap-4"><div><h3 class="font-semibold text-gray-950 dark:text-white">Maintenance workload</h3><p class="mt-1 text-sm text-gray-500">Requests grouped by current status.</p></div><a href="{{ url('/wizy/maintenance-logs') }}" class="report-actions text-sm font-semibold text-primary-600">View logs</a></div><div class="mt-5 space-y-3">@forelse ($maintenance as $item)<div class="flex items-center justify-between rounded-lg border p-4 {{ $maintenanceStyles[$item->status] ?? 'border-gray-200 bg-gray-50 text-gray-700 dark:border-white/10 dark:bg-gray-800 dark:text-gray-200' }}"><span class="text-sm font-semibold capitalize">{{ str_replace('_', ' ', $item->status) }}</span><span class="text-2xl font-bold">{{ $item->total }}</span></div>@empty<p class="text-sm text-gray-500">No maintenance records available.</p>@endforelse</div></div>
                <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-white/10 dark:bg-gray-900"><h3 class="font-semibold text-gray-950 dark:text-white">Warranty watch</h3><p class="mt-1 text-sm text-gray-500">Coverage dates requiring follow-up.</p><div class="mt-5 grid grid-cols-2 gap-3"><div class="rounded-lg bg-rose-50 p-4 dark:bg-rose-400/10"><p class="text-xs font-semibold uppercase tracking-wide text-rose-600 dark:text-rose-300">Expired</p><p class="mt-2 text-3xl font-bold text-rose-700 dark:text-rose-200">{{ $expiredWarranty }}</p></div><div class="rounded-lg bg-amber-50 p-4 dark:bg-amber-400/10"><p class="text-xs font-semibold uppercase tracking-wide text-amber-600 dark:text-amber-300">Next 30 days</p><p class="mt-2 text-3xl font-bold text-amber-700 dark:text-amber-200">{{ $expiringWarranty }}</p></div></div></div>
            </div>
        </section>

        <section class="report-section" aria-labelledby="distribution-heading">
            <div class="mb-4"><p class="text-xs font-bold uppercase tracking-[0.16em] text-gray-400">04 / Distribution</p><h2 id="distribution-heading" class="mt-1 text-lg font-semibold text-gray-950 dark:text-white">Where inventory sits</h2><p class="mt-1 text-sm text-gray-500">Device mix, department ownership, and location coverage.</p></div>
            <div class="grid gap-6 xl:grid-cols-3">
                <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-white/10 dark:bg-gray-900"><h3 class="font-semibold text-gray-950 dark:text-white">Device mix</h3><div class="mt-5 space-y-4">@forelse ($assetTypes as $item) @php($percentage = round(($item->total / $assetTotal) * 100))<div><div class="mb-1.5 flex justify-between text-sm"><span class="font-medium text-gray-700 dark:text-gray-200">{{ $item->type }}</span><span class="font-semibold text-gray-950 dark:text-white">{{ $item->total }} <span class="font-normal text-gray-500">({{ $percentage }}%)</span></span></div><div class="h-2 rounded-full bg-gray-100 dark:bg-gray-800"><div class="h-full rounded-full bg-primary-600" style="width: {{ $percentage }}%"></div></div></div>@empty<p class="text-sm text-gray-500">No asset data available.</p>@endforelse</div></div>
                <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-white/10 dark:bg-gray-900"><h3 class="font-semibold text-gray-950 dark:text-white">Department ownership</h3><div class="mt-5 space-y-4">@forelse ($departments as $department) @php($percentage = round(($department->assets_count / $assetTotal) * 100))<div><div class="mb-1.5 flex justify-between text-sm"><span class="font-medium text-gray-700 dark:text-gray-200">{{ $department->name }}</span><span class="font-semibold text-gray-950 dark:text-white">{{ $department->assets_count }}</span></div><div class="h-1.5 rounded-full bg-gray-100 dark:bg-gray-800"><div class="h-full rounded-full bg-primary-500" style="width: {{ $percentage }}%"></div></div></div>@empty<p class="text-sm text-gray-500">No departments available.</p>@endforelse</div></div>
                <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-white/10 dark:bg-gray-900"><h3 class="font-semibold text-gray-950 dark:text-white">Location coverage</h3><div class="mt-5 divide-y divide-gray-100 dark:divide-white/10">@forelse ($locations as $location)<div class="flex items-center justify-between gap-4 py-3"><span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $location->name }}</span><span class="rounded-full bg-gray-100 px-3 py-1 text-sm font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-200">{{ $location->assets_count }}</span></div>@empty<p class="py-3 text-sm text-gray-500">No locations available.</p>@endforelse</div></div>
            </div>
        </section>
    </div>
</x-filament-panels::page>
