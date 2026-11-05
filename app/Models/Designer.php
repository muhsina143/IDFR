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
     * Helper to get photo URL.
     */
    public function getPhotoUrlAttribute(): string
    {
        return self::resolveImageUrl($this->profile_photo, 'images/creative/fashion-designer/anjali-thomas.jpg');
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
