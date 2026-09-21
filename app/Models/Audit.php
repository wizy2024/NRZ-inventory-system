<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

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
    ];

    protected $casts = [
        'checked_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Audit $audit): void {
            $audit->audited_by ??= auth()->id();
            $audit->checked_at ??= now();
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
}
