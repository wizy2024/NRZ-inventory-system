<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function audits(): HasMany
    {
        return $this->hasMany(Audit::class);
    }

}
