<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GatePass extends Model
{
    use HasFactory;

    protected $fillable = [
        'pass_number',
        'asset_id',
        'maintenance_log_id',
        'collector_name',
        'collector_contact',
        'collector_id_number',
        'issued_by',
        'released_at',
        'notes',
    ];

    protected $casts = [
        'released_at' => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function maintenanceLog(): BelongsTo
    {
        return $this->belongsTo(MaintenanceLog::class);
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }
}
