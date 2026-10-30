<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    protected $fillable = [
        'name',
        'tier',
        'is_title',
        'description',
        'image_path',
        'logo_path',
        'logo_style',
        'logo_mark',
        'logo_word',
        'logo_sub',
        'website_url',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_title' => 'boolean',
        'display_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the web-accessible URL for the media/card image if any.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (! empty($this->image_path)) {
            if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
                return $this->image_path;
            }

            return asset($this->image_path);
        }

        return null;
    }

    /**
     * Get the web-accessible URL for the logo if uploaded.
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (! empty($this->logo_path)) {
            if (str_starts_with($this->logo_path, 'http://') || str_starts_with($this->logo_path, 'https://')) {
                return $this->logo_path;
            }

            return asset($this->logo_path);
        }

        return null;
    }

    /**
     * Get structured logo dataset for frontend rendering.
     */
    public function getLogoDataAttribute(): array
    {
        if ($this->logo_url) {
            return ['src' => $this->logo_url];
        }

        return [
            'style' => $this->logo_style ?: 'crown',
            'mark' => $this->logo_mark ?: substr($this->name, 0, 1),
            'word' => $this->logo_word ?: strtoupper($this->name),
            'sub' => $this->logo_sub ?: strtoupper($this->tier),
        ];
    }
}
