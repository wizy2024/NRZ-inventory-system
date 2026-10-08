<div class="space-y-3">
    @forelse ($history as $entry)
        <div class="rounded-lg border border-gray-200 p-3 dark:border-white/10">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <p class="font-semibold text-gray-950 dark:text-white">{{ ucfirst($entry->action) }}</p>
                <time class="text-xs text-gray-500">{{ $entry->effective_at?->format('d M Y, H:i') }}</time>
            </div>
            <dl class="mt-2 grid gap-2 text-sm sm:grid-cols-2">
                <div><dt class="text-xs text-gray-500">Technician</dt><dd>{{ $entry->technician?->name ?? 'Unassigned' }}</dd></div>
                <div><dt class="text-xs text-gray-500">Changed by</dt><dd>{{ $entry->changedBy?->name ?? 'System' }}</dd></div>
            </dl>
        </div>
    @empty
        <p class="text-sm text-gray-500">No technician assignment history recorded.</p>
    @endforelse
</div>
