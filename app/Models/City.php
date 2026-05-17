<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
    'parent_id',
    'name',
    'slug',
    'is_active',
];
}