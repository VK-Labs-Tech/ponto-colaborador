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
    ];

    protected function casts(): array
    {
        return [
            'reference_date' => 'date',
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
