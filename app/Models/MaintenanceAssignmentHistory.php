<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceAssignmentHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_log_id',
        'technician_id',
        'changed_by',
        'action',
        'effective_at',
    ];

    protected $casts = [
        'effective_at' => 'datetime',
    ];

    public function maintenanceLog(): BelongsTo
    {
        return $this->belongsTo(MaintenanceLog::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
