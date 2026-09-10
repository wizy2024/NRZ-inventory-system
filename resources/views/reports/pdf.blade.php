<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>NRZ Inventory Report</title>
    <style>
        @page { margin: 28px 30px; }
        body { color: #1f2937; font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        h1, h2, h3, p { margin: 0; }
        h1 { color: #111827; font-size: 22px; }
        h2 { border-bottom: 1px solid #d1d5db; color: #111827; font-size: 14px; margin: 20px 0 8px; padding-bottom: 4px; }
        h3 { color: #374151; font-size: 11px; margin-bottom: 5px; }
        .muted { color: #6b7280; }
        .summary { margin: 14px 0; width: 100%; }
        .summary td { background: #f3f4f6; border-right: 5px solid #fff; padding: 9px; width: 25%; }
        .summary strong { color: #111827; display: block; font-size: 16px; margin-top: 3px; }
        table { border-collapse: collapse; margin-bottom: 12px; width: 100%; }
        th { background: #e5e7eb; color: #374151; font-size: 9px; text-align: left; }
        th, td { border-bottom: 1px solid #e5e7eb; padding: 5px 6px; }
        .columns { width: 100%; }
        .columns td { padding: 0 8px 0 0; vertical-align: top; width: 50%; }
        .columns td:last-child { padding: 0 0 0 8px; }
        .pill { color: #047857; font-weight: bold; }
        .footer { border-top: 1px solid #d1d5db; color: #6b7280; font-size: 9px; margin-top: 18px; padding-top: 6px; }
    </style>
</head>
<body>
    <h1>NRZ Inventory Report</h1>
    <p class="muted">Generated {{ $generatedAt->format('d M Y, H:i') }}</p>

    <table class="summary">
        <tr>
            <td>Total assets<strong>{{ $assets->count() }}</strong></td>
            <td>Departments<strong>{{ $departments->count() }}</strong></td>
            <td>Expired warranty<strong>{{ $expiredWarranty }}</strong></td>
            <td>Expiring within 30 days<strong>{{ $expiringWarranty }}</strong></td>
        </tr>
    </table>

    <h2>Asset register</h2>
    <table>
        <thead>
            <tr>
                <th>Asset tag</th><th>Type / brand</th><th>Serial number</th>
                <th>Department</th><th>Location</th><th>Warranty expiry</th><th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($assets as $asset)
                <tr>
                    <td>{{ $asset->asset_tag }}</td>
                    <td>{{ $asset->type }} / {{ $asset->brand }}</td>
                    <td>{{ $asset->serial_number }}</td>
                    <td>{{ $asset->department?->name ?? 'Unassigned' }}</td>
                    <td>{{ $asset->location?->name ?? 'Unassigned' }}</td>
                    <td>{{ $asset->warranty_expiry?->format('d M Y') ?? 'Not set' }}</td>
                    <td class="pill">{{ str_replace('_', ' ', $asset->status) }}</td>
                </tr>
            @empty
                <tr><td colspan="7">No assets are available.</td></tr>
            @endforelse
        </tbody>
    </table>

    <table class="columns">
        <tr>
            <td>
                <h2>Device mix</h2>
                <table><tbody>
                    @forelse ($assetTypes as $item)
                        <tr><td>{{ $item->type }}</td><td>{{ $item->total }}</td></tr>
                    @empty
                        <tr><td colspan="2">No asset data available.</td></tr>
                    @endforelse
                </tbody></table>
            </td>
            <td>
                <h2>Maintenance workload</h2>
                <table><tbody>
                    @forelse ($maintenance as $item)
                        <tr><td>{{ str_replace('_', ' ', $item->status) }}</td><td>{{ $item->total }}</td></tr>
                    @empty
                        <tr><td colspan="2">No maintenance records available.</td></tr>
                    @endforelse
                </tbody></table>
            </td>
        </tr>
    </table>

    <h2>Department ownership</h2>
    <table><tbody>
        @forelse ($departments as $department)
            <tr><td>{{ $department->name }}</td><td>{{ $department->assets_count }} assets</td></tr>
        @empty
            <tr><td>No departments available.</td></tr>
        @endforelse
    </tbody></table>

    <p class="footer">This report reflects the inventory data available when it was generated.</p>
</body>
</html>
