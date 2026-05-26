<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    protected $fillable = [
        'title',
        'position',
        'ad_type',
        'device_target',
        'page_target',
        'image',
        'html_code',
        'url',
        'is_active',
        'views',
        'clicks',
        'ctr',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'ctr' => 'decimal:2',
    ];

    public function updateCtr(): void
    {
        $this->ctr = $this->views > 0
            ? round(($this->clicks / $this->views) * 100, 2)
            : 0;

        $this->save();
    }
}