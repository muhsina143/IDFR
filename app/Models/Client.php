<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'name',
        'industry',
        'image_path',
        'logo_path',
        'logo_style',
        'logo_mark',
        'logo_word',
        'logo_sub',
        'testimonial_quote',
        'author_name',
        'author_role',
        'company_name',
        'website_url',
        'is_featured',
        'include_in_strip',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'include_in_strip' => 'boolean',
        'display_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Moving logo entries linked to this client.
     */
    public function movingLogos(): HasMany
    {
        return $this->hasMany(MovingLogo::class);
    }

    /**
     * Get the web-accessible URL for the testimonial/campaign image.
     */
    public function getImageUrlAttribute(): string
    {
        if (! empty($this->image_path)) {
            if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
                return $this->image_path;
            }

            return asset($this->image_path);
        }

        return asset('images/clients/client-featured.jpg');
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
            'style' => $this->logo_style ?: 'arch',
            'mark' => $this->logo_mark ?: substr($this->name, 0, 1),
            'word' => $this->logo_word ?: strtoupper($this->name),
            'sub' => $this->logo_sub ?: strtoupper($this->industry ?: 'CLIENT'),
        ];
    }

    /**
     * Aliases for author_title, testimonial_text, and category.
     */
    public function getAuthorTitleAttribute(): ?string
    {
        return $this->author_role;
    }

    public function setAuthorTitleAttribute(?string $value): void
    {
        $this->attributes['author_role'] = $value;
    }

    public function getTestimonialTextAttribute(): ?string
    {
        return $this->testimonial_quote;
    }

    public function setTestimonialTextAttribute(?string $value): void
    {
        $this->attributes['testimonial_quote'] = $value;
    }

    public function getCategoryAttribute(): ?string
    {
        return $this->industry;
    }

    public function setCategoryAttribute(?string $value): void
    {
        $this->attributes['industry'] = $value;
    }

    /**
     * Set this client as the single featured testimonial.
     */
    public function makeFeatured(): void
    {
        static::where('id', '!=', $this->id)->update(['is_featured' => false]);
        $this->is_featured = true;
        $this->save();
    }
}
