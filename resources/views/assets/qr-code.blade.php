<style>
    @media print {
        body * {
            visibility: hidden !important;
        }

        .qr-print-label,
        .qr-print-label * {
            visibility: visible !important;
        }

        .qr-print-label {
            position: absolute;
            top: 0;
            left: 0;
            width: 3.5in;
            min-height: 4.5in;
            border: 2px solid #111827 !important;
            box-shadow: none !important;
        }

        .qr-print-button,
        .qr-url {
            display: none !important;
        }
    }
</style>

<div class="space-y-4 text-center">
    <div class="qr-print-label mx-auto w-fit rounded-xl border border-gray-300 bg-white p-5 shadow-sm">
        <p class="text-xs font-bold uppercase tracking-[0.2em] text-amber-600">NRZ Inventory</p>
        <p class="mt-2 text-lg font-bold text-gray-950">{{ $asset->asset_tag }}</p>
        <div class="mx-auto mt-4 w-fit rounded-lg border border-gray-200 bg-white p-3">
            {!! $qrCode !!}
        </div>
        <p class="mt-4 text-sm font-semibold text-gray-950">Scan for machine information</p>
        <dl class="mt-4 space-y-1 border-t border-gray-200 pt-3 text-left text-xs text-gray-600">
            <div class="flex justify-between gap-4"><dt>Type</dt><dd class="font-semibold text-gray-950">{{ $asset->type }}</dd></div>
            <div class="flex justify-between gap-4"><dt>Brand</dt><dd class="font-semibold text-gray-950">{{ $asset->brand }}</dd></div>
            <div class="flex justify-between gap-4"><dt>Serial</dt><dd class="font-mono font-semibold text-gray-950">{{ $asset->serial_number }}</dd></div>
        </dl>
        <p class="mt-4 text-[10px] text-gray-500">Attach this label to the machine. Do not remove.</p>
    </div>
    <div class="qr-url">
        <p class="text-sm font-semibold text-gray-950">Scan to view device information</p>
        <p class="mt-1 break-all text-xs text-gray-500">{{ $url }}</p>
    </div>
    <button type="button" onclick="window.print()" class="qr-print-button inline-flex items-center gap-2 rounded-lg bg-primary-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-500">
        <x-filament::icon icon="heroicon-o-printer" class="h-4 w-4" />
        Print label
    </button>
</div>
