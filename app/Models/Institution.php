<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'logo',
        'website',
        'is_active',
    ];
}