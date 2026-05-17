<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'group',
        'description',
    ];
    public function role(): BelongsTo
{
    return $this->belongsTo(Role::class);
}

public function hasPermission(string $permission): bool
{
    if (! $this->role) {
        return false;
    }

    return $this->role->hasPermission($permission);
}

public function isAdmin(): bool
{
    return $this->role?->slug === 'admin';
}

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)
            ->withTimestamps();
    }
}