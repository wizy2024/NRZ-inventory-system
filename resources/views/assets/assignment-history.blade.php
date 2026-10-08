<div class="space-y-3">
    @forelse ($history as $entry)
        <div class="rounded-lg border border-gray-200 p-3 dark:border-white/10">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <p class="font-semibold text-gray-950 dark:text-white">{{ ucfirst($entry->action) }}</p>
                <time class="text-xs text-gray-500">{{ $entry->effective_at?->format('d M Y, H:i') }}</time>
            </div>
            <dl class="mt-2 grid gap-2 text-sm sm:grid-cols-3">
                <div><dt class="text-xs text-gray-500">Assigned to</dt><dd>{{ $entry->assigned_to_name ?: ($entry->assignedTo?->name ?? 'Unassigned') }}</dd></div>
                <div><dt class="text-xs text-gray-500">Department</dt><dd>{{ $entry->department?->name ?? 'Unassigned' }}</dd></div>
                <div><dt class="text-xs text-gray-500">Location</dt><dd>{{ $entry->location?->name ?? 'Unassigned' }}</dd></div>
            </dl>
            @if ($entry->assignment_notes)
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ $entry->assignment_notes }}</p>
            @endif
            <p class="mt-2 text-xs text-gray-500">Changed by {{ $entry->changedBy?->name ?? 'System' }}</p>
        </div>
    @empty
        <p class="text-sm text-gray-500">No assignment history recorded.</p>
    @endforelse
</div>
