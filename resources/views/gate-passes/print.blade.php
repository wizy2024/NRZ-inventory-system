<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $gatePass->pass_number }} | Gate pass</title>
    @php
        $logoPath = (string) config('branding.logo');
        $logoUrl = file_exists(public_path($logoPath)) ? asset($logoPath) : null;
    @endphp
    <style>
        :root { color-scheme: light; font-family: Arial, Helvetica, sans-serif; }
        * { box-sizing: border-box; }
        body { background: #eef2f7; color: #172033; margin: 0; padding: 24px; }
        .toolbar { display: flex; justify-content: space-between; align-items: center; max-width: 900px; margin: 0 auto 16px; }
        .toolbar a, .toolbar button { border: 0; border-radius: 6px; background: #175ca8; color: #fff; cursor: pointer; font-size: 14px; padding: 10px 14px; text-decoration: none; }
        .copies { max-width: 900px; margin: 0 auto; }
        .copy { background: #fff; border: 1px solid #cbd5e1; margin-bottom: 18px; overflow: hidden; padding: 26px 30px; position: relative; }
        .copy:last-child { margin-bottom: 0; }
        .copy-label { border-bottom: 2px solid #175ca8; color: #175ca8; font-size: 12px; font-weight: 700; letter-spacing: .12em; margin-bottom: 20px; padding-bottom: 8px; text-transform: uppercase; }
        .heading { align-items: flex-start; border-bottom: 1px solid #dbe3ed; display: flex; gap: 20px; justify-content: space-between; padding-bottom: 14px; position: relative; z-index: 1; }
        .heading-main { align-items: flex-start; display: flex; gap: 14px; }
        .company-logo { background: #fff; border: 1px solid #dbe3ed; border-radius: 8px; height: 52px; max-width: 150px; object-fit: contain; padding: 4px; width: 82px; }
        .watermark { height: 70%; inset: 20% 25%; object-fit: contain; opacity: .045; pointer-events: none; position: absolute; width: 50%; }
        h1 { font-size: 22px; margin: 0 0 5px; }
        .muted { color: #64748b; font-size: 12px; margin: 0; }
        .pass-number { color: #175ca8; font-size: 15px; font-weight: 700; }
        .details { border-collapse: collapse; margin-top: 18px; width: 100%; }
        .details td { border-bottom: 1px solid #e5eaf0; font-size: 13px; padding: 9px 6px; vertical-align: top; }
        .details td:first-child { color: #64748b; font-weight: 700; width: 24%; }
        .notice { background: #f5f8fc; border-left: 3px solid #175ca8; font-size: 12px; line-height: 1.5; margin-top: 18px; padding: 10px 12px; }
        .signatures { display: grid; gap: 28px; grid-template-columns: 1fr 1fr; margin-top: 32px; }
        .signature { border-top: 1px solid #64748b; color: #64748b; font-size: 11px; padding-top: 7px; }
        @media print {
            body { background: #fff; padding: 0; }
            .toolbar { display: none; }
            .copies { max-width: none; }
            .copy { border: 0; margin: 0; min-height: 32vh; padding: 16px 0; page-break-inside: avoid; }
            .copy + .copy { border-top: 1px dashed #94a3b8; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <a href="{{ url('/wizy/gate-passes') }}">Back to gate passes</a>
        <button type="button" onclick="window.print()">Print three copies</button>
    </div>

    <main class="copies">
        @foreach ([
            'Office copy',
            'Customer copy',
            'Gate copy',
        ] as $copyLabel)
            <section class="copy">
                <div class="copy-label">{{ $copyLabel }}</div>
                <div class="heading">
                    @if ($logoUrl)
                        <img class="watermark" src="{{ $logoUrl }}" alt="">
                    @endif
                    <div class="heading-main">
                        @if ($logoUrl)
                            <img class="company-logo" src="{{ $logoUrl }}" alt="Company logo">
                        @endif
                    <div>
                        <h1>Machine release gate pass</h1>
                        <p class="muted">NRZ Inventory | Authorised collection of repaired equipment</p>
                    </div>
                    </div>
                    <div class="pass-number">{{ $gatePass->pass_number }}</div>
                </div>
                <table class="details">
                    <tr><td>Machine</td><td>{{ $gatePass->asset->asset_tag }} | {{ $gatePass->asset->type }} | {{ $gatePass->asset->brand }}</td></tr>
                    <tr><td>Serial number</td><td>{{ $gatePass->asset->serial_number }}</td></tr>
                    <tr><td>Location</td><td>{{ $gatePass->asset->location?->name ?? 'Not recorded' }}</td></tr>
                    <tr><td>Collected by</td><td>{{ $gatePass->collector_name }}</td></tr>
                    <tr><td>Contact</td><td>{{ $gatePass->collector_contact ?: 'Not provided' }}</td></tr>
                    <tr><td>ID / employee no.</td><td>{{ $gatePass->collector_id_number ?: 'Not provided' }}</td></tr>
                    <tr><td>Released</td><td>{{ $gatePass->released_at->format('d M Y, H:i') }}</td></tr>
                    <tr><td>Issued by</td><td>{{ $gatePass->issuer->name }}</td></tr>
                </table>
                @if ($gatePass->notes)
                    <div class="notice"><strong>Notes:</strong> {{ $gatePass->notes }}</div>
                @endif
                <div class="notice">This pass authorises the person named above to remove the listed machine after repair. Security should verify the machine tag and serial number before allowing exit.</div>
                <div class="signatures">
                    <div class="signature">Collector signature</div>
                    <div class="signature">Security / issuing officer signature</div>
                </div>
            </section>
        @endforeach
    </main>
</body>
</html>
