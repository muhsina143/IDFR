<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovingLogo extends Model
{
    protected $fillable = [
        'client_id',
        'sponsor_id',
        'company_name',
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
        'client_id' => 'integer',
        'sponsor_id' => 'integer',
        'display_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Associated client if selected.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Associated sponsor if selected.
     */
    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(Sponsor::class);
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

        if ($this->client && $this->client->logo_url) {
            return $this->client->logo_url;
        }

        if ($this->sponsor && $this->sponsor->logo_url) {
            return $this->sponsor->logo_url;
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

        if ($this->client && ! $this->logo_style) {
            return $this->client->logo_data;
        }

        if ($this->sponsor && ! $this->logo_style) {
            return $this->sponsor->logo_data;
        }

        return [
            'style' => $this->logo_style ?: 'circle',
            'mark' => $this->logo_mark ?: substr($this->company_name, 0, 1),
            'word' => $this->logo_word ?: strtoupper($this->company_name),
            'sub' => $this->logo_sub ?: '',
        ];
    }

    /**
     * Display name accessor and mutator.
     */
    public function getNameAttribute(): string
    {
        return $this->company_name ?: ($this->client?->name ?: ($this->sponsor?->name ?: 'Logo'));
    }

    public function setNameAttribute(?string $value): void
    {
        $this->attributes['company_name'] = $value;
    }
}
