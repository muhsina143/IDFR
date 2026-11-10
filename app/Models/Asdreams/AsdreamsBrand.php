<?php

namespace App\Models\Asdreams;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsdreamsBrand extends Model
{
    use HasFactory;

    protected $table = 'asdreams_brands';

    protected $fillable = [
        'name',
        'tagline',
        'description',
        'logo_path',
        'cover_image',
        'link_url',
        'tags',
        'css_variant',
        'order',
        'is_active',
    ];

    protected $casts = [
        'tags' => 'array',
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order', 'asc');
    }

    public function getLogoUrlAttribute(): string
    {
        if (empty($this->logo_path)) {
            return asset('images/asdreams/asd-logo.png');
        }

        if (str_starts_with($this->logo_path, 'http://') || str_starts_with($this->logo_path, 'https://')) {
            return $this->logo_path;
        }

        return asset($this->logo_path);
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        if (empty($this->cover_image)) {
            return null;
        }

        if (str_starts_with($this->cover_image, 'http://') || str_starts_with($this->cover_image, 'https://')) {
            return $this->cover_image;
        }

        return asset($this->cover_image);
    }

    public function getLinkUrlAttribute(?string $value): string
    {
        if ($this->name === 'IDFR' || $this->name === 'Indian Designer Fashion Runway') {
            if (empty($value) || $value === '/' || $value === '#') {
                return route('idfr.home');
            }
        }

        if ($this->name === 'ASDreamStudio') {
            if (empty($value) || $value === '/asdreams#home' || $value === '#home' || $value === '#') {
                return route('asd.home');
            }
        }

        if ($this->name === "Athul's Academy") {
            if (empty($value) || $value === '#') {
                return route('asd.contact', ['brand' => 'athuls']);
            }
        }

        if ($this->name === 'Lakme' || $this->name === 'Beauty & Wellness') {
            if (empty($value) || $value === '#') {
                return route('asd.contact', ['brand' => 'lakme']);
            }
        }

        return $value ?? '#';
    }
}
