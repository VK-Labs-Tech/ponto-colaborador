<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MirrorSnapshot extends Model
{
    protected $fillable = [
        'company_id',
        'period_from',
        'period_to',
        'employee_id',
        'rows',
        'punch_rows',
        'totals',
        'content_hash',
        'version',
        'signed_by',
        'signed_at',
    ];

    protected function casts(): array
    {
        return [
            'period_from' => 'date',
            'period_to' => 'date',
            'rows' => 'array',
            'punch_rows' => 'array',
            'totals' => 'array',
            'signed_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
