<?php

namespace App\Models;

use App\Notifications\VerifyEmailTurkish;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [

        'name',
        'email',
        'password',

        'role',
        'role_id',

        'status',
        'is_active',

        'email_verified_at',
        'suspended_until',
        'ban_reason',

        'avatar',
        'bio',

        'facebook',
        'twitter',
        'instagram',
        'youtube',

        'last_seen_at',
        'last_ip_address',
        'last_device',
        'last_browser',
        'last_platform',

    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [

            'email_verified_at' => 'datetime',
            'suspended_until' => 'datetime',
            'last_seen_at' => 'datetime',

            'is_active' => 'boolean',

            'password' => 'hashed',

        ];
    }

    protected static function booted(): void
    {
        static::saving(function (User $user) {

            if ($user->role_id) {

                $role = Role::find($user->role_id);

                if ($role) {
                    $user->role = $role->slug;
                }

            }

        });
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailTurkish());
    }

    /*
    |--------------------------------------------------------------------------
    | ROLE HELPERS
    |--------------------------------------------------------------------------
    */

    public function roleModel(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function hasPermission(string $permission): bool
    {
        if (! $this->roleModel) {
            return false;
        }

        return $this->roleModel->hasPermission($permission);
    }

    public function isAdmin(): bool
    {
        return $this->roleModel?->slug === 'admin';
    }

    public function isEditor(): bool
    {
        return $this->roleModel?->slug === 'editor';
    }

    public function isModerator(): bool
    {
        return $this->roleModel?->slug === 'moderator';
    }

    public function isUser(): bool
    {
        return $this->roleModel?->slug === 'user';
    }

    /*
    |--------------------------------------------------------------------------
    | NOTIFICATIONS
    |--------------------------------------------------------------------------
    */

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class)
            ->latest();
    }

    public function unreadNotifications(): HasMany
    {
        return $this->hasMany(Notification::class)
            ->where('is_read', false)
            ->latest();
    }
}