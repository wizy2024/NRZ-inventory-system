<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_tag',
        'serial_number',
        'mac_address',
        'type',
        'brand',
        'specs',
        'purchase_date',
        'warranty_expiry',
        'department_id',
        'location_id',
        'assigned_to_user_id',
        'assigned_to_name',
        'assigned_at',
        'assignment_notes',
        'status',
        'condemnation_reason',
        'condemned_at',
    ];

    protected $casts = [
        'specs' => 'array',
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
        'condemned_at' => 'date',
        'assigned_at' => 'datetime',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function maintenanceLogs(): HasMany
    {
        return $this->hasMany(MaintenanceLog::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function getAssigneeNameAttribute(): ?string
    {
        return $this->assigned_to_name ?: $this->assignedTo?->name;
    }

    public function audits(): HasMany
    {
        return $this->hasMany(Audit::class);
    }

    public function assignmentHistories(): HasMany
    {
        return $this->hasMany(AssetAssignmentHistory::class)->latest('effective_at');
    }

    protected static function booted(): void
    {
        static::saving(function (Asset $asset): void {
            if ($asset->isDirty('assigned_to_name')) {
                $asset->assigned_to_user_id = null;
            } elseif ($asset->isDirty('assigned_to_user_id')) {
                $asset->assigned_to_name = $asset->assigned_to_user_id
                    ? User::query()->whereKey($asset->assigned_to_user_id)->value('name')
                    : null;
            }
        });

        static::created(function (Asset $asset): void {
            $asset->recordAssignmentHistory('created');
        });

        static::updated(function (Asset $asset): void {
            if ($asset->wasChanged([
                'assigned_to_user_id',
                'assigned_to_name',
                'department_id',
                'location_id',
                'assigned_at',
                'assignment_notes',
            ])) {
                $asset->recordAssignmentHistory('updated');
            }
        });
    }

    public function recordAssignmentHistory(string $action): void
    {
        $this->assignmentHistories()->create([
            'assigned_to_user_id' => $this->assigned_to_user_id,
            'assigned_to_name' => $this->assignee_name,
            'department_id' => $this->department_id,
            'location_id' => $this->location_id,
            'changed_by' => Auth::id(),
            'assigned_at' => $this->assigned_at,
            'effective_at' => now(),
            'action' => $action,
            'assignment_notes' => $this->assignment_notes,
        ]);
    }

}
