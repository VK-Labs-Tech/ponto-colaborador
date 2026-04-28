<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class TimePunch extends Model
{
    protected $fillable = [
        'company_id',
        'employee_id',
        'action',
        'punched_at',
        'origin',
        'note',
        'adjustment_batch',
        'ip_address',
        'latitude',
        'longitude',
        'device_fingerprint',
    ];

    protected function casts(): array
    {
        return [
            'punched_at' => 'datetime',
            'latitude' => 'float',
            'longitude' => 'float',
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
