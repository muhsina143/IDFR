<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Designer extends Model
{
    use HasFactory;

    protected $table = 'designers';

    protected $fillable = [
        'user_id',
        'name',
        'brand_name',
        'slug',
        'profile_photo',
        'brand_logo',
        'location',
        'phone',
        'email',
        'experience',
        'short_intro',
        'biography',
        'collection_title',
        'collection_description',
        'atelier_notes',
        'services',
        'outstation',
        'portfolio_images',
        'website_url',
        'instagram_url',
        'other_social_links',
        'is_featured',
        'display_order',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'services' => 'array',
            'portfolio_images' => 'array',
            'other_social_links' => 'array',
            'display_order' => 'integer',
        ];
    }

    /**
     * User this designer profile belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Seasons this designer is associated with.
     */
    public function seasons(): BelongsToMany
    {
        return $this->belongsToMany(Season::class, 'designer_season', 'designer_id', 'season_id');
    }

    /**
     * Auto slug generation.
     */
    protected static function booted(): void
    {
        static::creating(function ($designer) {
            if (empty($designer->slug)) {
                $baseSlug = Str::slug($designer->name.($designer->brand_name ? '-'.$designer->brand_name : ''));
                $slug = $baseSlug;
                $counter = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $baseSlug.'-'.$counter++;
                }
                $designer->slug = $slug;
            }
        });
    }

    /**
     * Helper to get photo URL.
     */
    public function getPhotoUrlAttribute(): string
    {
        if (empty($this->profile_photo)) {
            return asset('images/creative/fashion-designer/anjali-thomas.jpg');
        }
        if (str_starts_with($this->profile_photo, 'http://') || str_starts_with($this->profile_photo, 'https://')) {
            return $this->profile_photo;
        }

        $relPath = ltrim($this->profile_photo, '/');
        if (file_exists(public_path($relPath))) {
            return asset($relPath);
        }

        return asset('images/creative/fashion-designer/anjali-thomas.jpg');
    }

    /**
     * Helper to get lookbook image URL by index with fallback.
     */
    public function getPortfolioUrl(int $index, string $fallback = ''): string
    {
        $images = $this->portfolio_images;
        if (is_array($images) && isset($images[$index])) {
            $item = $images[$index];
            $path = is_array($item) ? ($item['image'] ?? '') : (string) $item;
            if (! empty($path)) {
                if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                    return $path;
                }

                $relPath = ltrim($path, '/');
                if (file_exists(public_path($relPath))) {
                    return asset($relPath);
                }
            }
        }

        return $fallback ? asset(ltrim($fallback, '/')) : '';
    }
}
