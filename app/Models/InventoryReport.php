<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class InventoryReport extends Model
{
    protected $fillable = [
        'title',
        'report_type',
        'summary',
        'notes',
        'generated_by',
        'generated_at',
    ];

    protected $casts = [
        'summary' => 'array',
        'generated_at' => 'datetime',
    ];

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
