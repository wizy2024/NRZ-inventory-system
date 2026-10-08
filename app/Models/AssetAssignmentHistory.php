<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetAssignmentHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'assigned_to_user_id',
        'assigned_to_name',
        'department_id',
        'location_id',
        'changed_by',
        'assigned_at',
        'effective_at',
        'action',
        'assignment_notes',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'effective_at' => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
