<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class HeaderSlot extends Model
{
    public const TYPE_BUTTON = 'button';
    public const TYPE_BANNER = 'banner';
    public const POSITION_TOPBAR_AFTER_HOME = 'topbar_after_home';

    protected $fillable = [
        'title',
        'slot_type',
        'is_active',
        'sort_order',
        'display_position',
        'starts_at',
        'ends_at',
        'button_text',
        'button_url',
        'button_target',
        'button_background_color',
        'button_hover_color',
        'button_text_color',
        'button_size',
        'button_radius',
        'button_icon',
        'custom_css_class',
        'banner_image',
        'banner_url',
        'banner_target',
        'banner_width',
        'banner_height',
        'banner_alt',
        'html_code',
        'script_code',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'sort_order' => 'integer',
            'button_radius' => 'integer',
            'banner_width' => 'integer',
            'banner_height' => 'integer',
        ];
    }

    public function scopeVisibleInHeader(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where('display_position', self::POSITION_TOPBAR_AFTER_HOME)
            ->where(function (Builder $query): void {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function (Builder $query): void {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            })
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function isButton(): bool
    {
        return $this->slot_type === self::TYPE_BUTTON;
    }

    public function isBanner(): bool
    {
        return $this->slot_type === self::TYPE_BANNER;
    }

    public function buttonSizeClasses(): string
    {
        return match ($this->button_size) {
            'small' => 'px-2 py-1 text-[11px]',
            'large' => 'px-4 py-2 text-sm',
            default => 'px-3 py-1.5 text-xs',
        };
    }
}
