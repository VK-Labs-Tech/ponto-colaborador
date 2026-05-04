<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class AttendanceAdjustment extends Model
{
    protected $fillable = [
        'company_id',
        'employee_id',
        'reference_date',
        'worked_minutes',
        'reason',
        'created_by',
        'before_punches',
        'after_punches',
        'actor_id',
        'actor_role',
        'status',
        'approved_by',
        'approved_at',
        'approval_reason',
        'adjustment_batch',
    ];

    protected function casts(): array
    {
        return [
            'reference_date' => 'date',
            'before_punches' => 'array',
            'after_punches' => 'array',
            'approved_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
