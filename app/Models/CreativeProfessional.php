<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Schema;
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
        'custom_fields',
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
            'custom_fields' => 'array',
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
        $default = [
            'makeup-artist' => 'Makeup Artist',
            'hairstylist' => 'Hairstylist',
            'photographer' => 'Photographer',
            'videographer' => 'Videographer',
            'fashion-designer' => 'Fashion Designer',
            'drone-pilot' => 'Drone Pilot',
            'photo-video-editor' => 'Photo & Video Editor',
            'jewellery-designer' => 'Jewellery Designer',
        ];

        try {
            if (Schema::hasTable('professional_categories')) {
                $dbCategories = ProfessionalCategory::orderBy('display_order')
                    ->pluck('name', 'slug')
                    ->toArray();

                if (! empty($dbCategories)) {
                    return array_merge($default, $dbCategories);
                }
            }
        } catch (\Throwable $e) {
            // fallback to default
        }

        return $default;
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
     * Resolve any image path (relative, upload, storage, or external) to a fully qualified asset URL.
     */
    public static function resolveImageUrl(?string $path, string $fallback = ''): string
    {
        if (empty($path)) {
            return $fallback ? asset(ltrim($fallback, '/')) : '';
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $clean = ltrim(preg_replace('#^public/#', '', ltrim($path, '/')), '/');

        if (file_exists(public_path($clean))) {
            return asset($clean);
        }

        if (str_starts_with($clean, 'storage/')) {
            $storageRel = preg_replace('#^storage/#', '', $clean);
            if (file_exists(storage_path('app/public/'.$storageRel))) {
                return asset('storage/'.$storageRel);
            }
        }

        if (file_exists(storage_path('app/public/'.$clean)) || file_exists(public_path('storage/'.$clean))) {
            return asset('storage/'.$clean);
        }

        if (str_starts_with($clean, 'uploads/') || str_starts_with($clean, 'images/')) {
            return asset($clean);
        }

        return $fallback ? asset(ltrim($fallback, '/')) : asset($clean);
    }

    /**
     * Helper to get photo URL with category-specific default fallback.
     */
    public function getPhotoUrlAttribute(): string
    {
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

        return self::resolveImageUrl($this->profile_photo, $defaultImage);
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
                return self::resolveImageUrl($path, $fallback);
            }
        }

        return $fallback ? asset(ltrim($fallback, '/')) : '';
    }

    /**
     * Get all portfolio images with resolved URLs and captions.
     *
     * @return array<int, array{index: int, path: string, url: string, caption: string}>
     */
    public function getPortfolioItems(): array
    {
        $items = [];
        $images = $this->portfolio_images;
        if ($images && is_array($images)) {
            foreach ($images as $idx => $item) {
                $path = is_array($item) ? ($item['image'] ?? '') : (string) $item;
                $caption = is_array($item) ? ($item['caption'] ?? '') : '';
                if (! empty($path)) {
                    $items[] = [
                        'index' => $idx,
                        'path' => $path,
                        'url' => self::resolveImageUrl($path),
                        'caption' => $caption ?: 'Portfolio Image '.($idx + 1),
                    ];
                }
            }
        }

        if (empty($items) && $this->profile_photo) {
            $items[] = [
                'index' => 0,
                'path' => $this->profile_photo,
                'url' => $this->photo_url,
                'caption' => $this->name,
            ];
        }

        return $items;
    }

    /**
     * Status check helpers.
     */
    public function isApproved(): bool
    {
        return in_array($this->status, ['published', 'approved'], true);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
