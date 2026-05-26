<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThemeSetting extends Model
{
    public const DEFAULTS = [
        'primary_color' => '#0878c9',
        'secondary_color' => '#1e293b',
        'topbar_color' => '#0878c9',
        'navbar_color' => '#1e293b',
        'breaking_bar_color' => '#dc2626',
        'announcement_bar_color' => '#0f172a',
        'button_color' => '#1d4ed8',
        'button_hover_color' => '#1e40af',
        'link_color' => '#1d4ed8',
        'heading_color' => '#020617',
        'text_color' => '#0f172a',
        'card_background_color' => '#ffffff',
        'footer_color' => '#0f172a',
    ];

    protected $fillable = [
        'primary_color',
        'secondary_color',
        'topbar_color',
        'navbar_color',
        'breaking_bar_color',
        'announcement_bar_color',
        'button_color',
        'button_hover_color',
        'link_color',
        'heading_color',
        'text_color',
        'card_background_color',
        'footer_color',
    ];

    public static function current(): self
    {
        $setting = static::query()->first();

        if ($setting) {
            return $setting;
        }

        return new static(static::DEFAULTS);
    }

    public function resetToDefaults(): void
    {
        $this->forceFill(static::DEFAULTS)->save();
    }

    public function color(string $key): string
    {
        return filled($this->{$key} ?? null)
            ? $this->{$key}
            : static::DEFAULTS[$key];
    }
}
