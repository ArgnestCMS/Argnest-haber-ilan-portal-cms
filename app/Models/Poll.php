<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;

class Poll extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'subtitle',
        'description',
        'image',
        'topic',
        'is_active',
        'starts_at',
        'ends_at',
        'share_results',
        'show_home_popup',
        'popup_cooldown_minutes',
        'allow_multiple',
        'allow_guests',
        'require_login',
        'duplicate_guard',
        'views',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'share_results' => 'boolean',
        'show_home_popup' => 'boolean',
        'allow_multiple' => 'boolean',
        'allow_guests' => 'boolean',
        'require_login' => 'boolean',
        'popup_cooldown_minutes' => 'integer',
        'views' => 'integer',
    ];

    public function options(): HasMany
    {
        return $this->hasMany(PollOption::class)->orderBy('sort_order')->orderBy('id');
    }

    public function activeOptions(): HasMany
    {
        return $this->options()->where('is_active', true);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(PollVote::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where(function (Builder $query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function (Builder $query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            });
    }

    public function scopePopupActive(Builder $query): Builder
    {
        return $query->active()->where('show_home_popup', true);
    }

    public function totalVotes(): int
    {
        return (int) $this->options->sum('votes_count');
    }

    public function voterKey(Request $request): string
    {
        $userPart = $request->user()?->id ? 'user:' . $request->user()->id : null;
        $sessionPart = 'session:' . $request->session()->getId();
        $ipPart = 'ip:' . hash('sha256', (string) $request->ip());

        return match ($this->duplicate_guard) {
            'user' => $userPart ?? $sessionPart,
            'session' => $sessionPart,
            'ip' => $ipPart,
            default => $userPart ?? ($sessionPart . ':' . $ipPart),
        };
    }

    public function hasVoteFrom(Request $request): bool
    {
        return $this->votes()
            ->where('voter_key', $this->voterKey($request))
            ->exists();
    }
}
