<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use App\Notifications\MaintenanceAssigned;

class MaintenanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'technician_id',
        'symptom',
        'description',
        'status',
        'resolved_at',
        'resolution_notes',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::created(function (MaintenanceLog $maintenanceLog): void {
            $maintenanceLog->recordAssignmentHistory('created');
            $maintenanceLog->sendTechnicianAlertIfNeeded(true);
        });

        static::updated(function (MaintenanceLog $maintenanceLog): void {
            if ($maintenanceLog->wasChanged('technician_id')) {
                $maintenanceLog->recordAssignmentHistory('transferred');
            }

            $maintenanceLog->sendTechnicianAlertIfNeeded();
        });
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function assignmentHistories(): HasMany
    {
        return $this->hasMany(MaintenanceAssignmentHistory::class)->latest('effective_at');
    }

    public function recordAssignmentHistory(string $action): void
    {
        $this->assignmentHistories()->create([
            'technician_id' => $this->technician_id,
            'changed_by' => Auth::id(),
            'action' => $action,
            'effective_at' => now(),
        ]);
    }

    protected function sendTechnicianAlertIfNeeded(bool $created = false): void
    {
        if (! $this->technician_id || ! in_array($this->status, ['pending', 'in_progress'], true)) {
            return;
        }

        if (! $created && ! $this->wasChanged('technician_id') && ! ($this->wasChanged('status') && $this->status === 'in_progress')) {
            return;
        }

        $this->unsetRelation('technician')->load('technician');
        $this->technician?->notify(new MaintenanceAssigned($this));
    }
}
