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
        'notes',
    ];

    protected $casts = [
        'checked_at' => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function auditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'audited_by');
    }
}
