<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Audit extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'audited_by',
        'result',
        'checked_at',
        'expected_location',
        'observed_location',
        'expected_assignee',
        'notes',
        'follow_up_status',
        'follow_up_owner_id',
        'follow_up_due_at',
        'follow_up_notes',
    ];

    protected $casts = [
        'checked_at' => 'datetime',
        'follow_up_due_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Audit $audit): void {
            $audit->audited_by ??= Auth::id();
            $audit->checked_at ??= now();
            $audit->follow_up_status ??= $audit->result === 'found' ? 'not_required' : 'open';
        });
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function auditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'audited_by');
    }

    public function followUpOwner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'follow_up_owner_id');
    }
}
