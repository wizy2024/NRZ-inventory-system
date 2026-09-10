<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $asset->asset_tag }} | NRZ Inventory</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <main class="mx-auto max-w-2xl px-4 py-10 sm:px-6">
        <header class="mb-8 flex items-center gap-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-amber-500 text-lg font-black text-slate-950">NRZ</div>
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-amber-600">National Railways of Zimbabwe</p>
                <h1 class="text-2xl font-bold">Device record</h1>
            </div>
        </header>
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-100 pb-5">
                <div>
                    <p class="text-sm text-slate-500">Asset tag</p>
                    <h2 class="mt-1 text-3xl font-bold tracking-tight">{{ $asset->asset_tag }}</h2>
                </div>
                <span class="rounded-full px-3 py-1 text-sm font-semibold {{ $asset->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                    {{ ucfirst($asset->status) }}
                </span>
            </div>
            <dl class="mt-6 grid gap-5 sm:grid-cols-2">
                @foreach ([
                    'Device type' => $asset->type,
                    'Brand' => $asset->brand,
                    'Location' => $asset->location?->name ?: 'Not assigned',
                    'Warranty expiry' => $asset->warranty_expiry?->format('d M Y') ?: 'Not recorded',
                ] as $label => $value)
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ $label }}</dt>
                        <dd class="mt-1 font-medium">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
            <div class="mt-8 border-t border-slate-100 pt-6">
                <a href="{{ url('/wizy/maintenance-logs/create?asset_id=' . $asset->id) }}" class="inline-flex w-full items-center justify-center rounded-lg bg-amber-500 px-4 py-3 text-sm font-semibold text-slate-950 transition hover:bg-amber-400">
                    Report a maintenance issue
                </a>
                <a href="{{ url('/wizy/audits/create?asset_id=' . $asset->id) }}" class="mt-3 inline-flex w-full items-center justify-center rounded-lg border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    Record an audit check
                </a>
                <p class="mt-2 text-center text-xs text-slate-500">Sign in to submit and track a maintenance request.</p>
            </div>
            <p class="mt-8 border-t border-slate-100 pt-4 text-xs text-slate-500">NRZ Asset Management • Public device identification record</p>
        </section>
    </main>
</body>
</html>
