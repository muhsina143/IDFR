<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class CreativeProfessional extends Model
{
    use HasFactory;

    protected $table = 'creative_professionals';

    protected $fillable = [
        'user_id',
        'name',
        'category',
        'brand',
        'professional_title',
        'slug',
        'profile_photo',
        'location',
        'phone',
        'email',
        'experience',
        'biography',
        'equipment',
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
     * User this creative professional profile belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Categories list with formatted labels.
     */
    public static function categories(): array
    {
        return [
            'makeup-artist' => 'Makeup Artist',
            'hairstylist' => 'Hairstylist',
            'photographer' => 'Photographer',
            'videographer' => 'Videographer',
            'fashion-designer' => 'Fashion Designer',
            'drone-pilot' => 'Drone Pilot',
            'photo-video-editor' => 'Photo & Video Editor',
            'jewellery-designer' => 'Jewellery Designer',
        ];
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::categories()[$this->category] ?? ucfirst(str_replace('-', ' ', $this->category));
    }

    /**
     * Seasons this creative professional is associated with.
     */
    public function seasons(): BelongsToMany
    {
        return $this->belongsToMany(Season::class, 'creative_professional_season', 'creative_professional_id', 'season_id');
    }

    /**
     * Auto slug generation.
     */
    protected static function booted(): void
    {
        static::creating(function ($pro) {
            if (empty($pro->slug)) {
                $baseSlug = Str::slug($pro->category.'-'.$pro->name);
                $slug = $baseSlug;
                $counter = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $baseSlug.'-'.$counter++;
                }
                $pro->slug = $slug;
            }
        });
    }

    /**
     * Helper to get photo URL with category-specific default fallback.
     */
    public function getPhotoUrlAttribute(): string
    {
        if (empty($this->profile_photo)) {
            $categoryDefaults = [
                'hairstylist' => 'images/creative/hairstylist/aiswarya-mohan.jpg',
                'photographer' => 'images/creative/photographer/adithya-raj.jpg',
                'videographer' => 'images/creative/videographer/alan-mathew.jpg',
                'fashion-designer' => 'images/creative/fashion-designer/anjali-thomas.jpg',
                'drone-pilot' => 'images/creative/photographer/akhil-joseph.jpg',
                'photo-video-editor' => 'images/creative/videographer/alan-mathew.jpg',
                'jewellery-designer' => 'images/creative/fashion-designer/maya-joseph.jpg',
                'makeup-artist' => 'images/creative/makeup-artist/ananya-menon.jpg',
            ];

            $defaultImage = $categoryDefaults[$this->category] ?? 'images/creative/makeup-artist/ananya-menon.jpg';

            return asset($defaultImage);
        }

        if (str_starts_with($this->profile_photo, 'http://') || str_starts_with($this->profile_photo, 'https://')) {
            return $this->profile_photo;
        }

        $relPath = ltrim($this->profile_photo, '/');
        if (file_exists(public_path($relPath))) {
            return asset($relPath);
        }

        $categoryDefaults = [
            'hairstylist' => 'images/creative/hairstylist/aiswarya-mohan.jpg',
            'photographer' => 'images/creative/photographer/adithya-raj.jpg',
            'videographer' => 'images/creative/videographer/alan-mathew.jpg',
            'fashion-designer' => 'images/creative/fashion-designer/anjali-thomas.jpg',
            'drone-pilot' => 'images/creative/photographer/akhil-joseph.jpg',
            'photo-video-editor' => 'images/creative/videographer/alan-mathew.jpg',
            'jewellery-designer' => 'images/creative/fashion-designer/maya-joseph.jpg',
            'makeup-artist' => 'images/creative/makeup-artist/ananya-menon.jpg',
        ];

        return asset($categoryDefaults[$this->category] ?? 'images/creative/makeup-artist/ananya-menon.jpg');
    }

    /**
     * Helper to get portfolio image URL by index with fallback.
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
