<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiModel extends Model
{
    protected $fillable = [
        'provider',
        'name',
        'capabilities',
        'context_window',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'capabilities' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
