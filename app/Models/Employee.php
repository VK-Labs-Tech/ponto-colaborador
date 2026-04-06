<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'registration',
        'pin',
        'shift_start',
        'shift_end',
        'is_active',
    ];

    protected $hidden = [
        'pin',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'shift_start' => 'datetime:H:i:s',
            'shift_end' => 'datetime:H:i:s',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function timePunches(): HasMany
    {
        return $this->hasMany(TimePunch::class);
    }
}
