<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class ModelProfile extends Model
{
    use HasFactory;

    protected $table = 'models';

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'gender',
        'level',
        'is_kid',
        'age',
        'height',
        'location',
        'phone',
        'email',
        'experience',
        'profile_photo',
        'types',
        'measurements',
        'skills',
        'specialities',
        'portfolio_images',
        'availability',
        'outstation',
        'international',
        'description',
        'instagram_url',
        'portfolio_url',
        'is_featured',
        'display_order',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'is_kid' => 'boolean',
            'is_featured' => 'boolean',
            'types' => 'array',
            'measurements' => 'array',
            'skills' => 'array',
            'specialities' => 'array',
            'portfolio_images' => 'array',
            'age' => 'integer',
            'display_order' => 'integer',
        ];
    }

    /**
     * User this model profile belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Seasons this model is associated with.
     */
    public function seasons(): BelongsToMany
    {
        return $this->belongsToMany(Season::class, 'model_season', 'model_id', 'season_id');
    }

    /**
     * Generate unique slug on creation if not provided.
     */
    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $baseSlug = Str::slug($model->name);
                $slug = $baseSlug;
                $counter = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $baseSlug.'-'.$counter++;
                }
                $model->slug = $slug;
            }
        });
    }

    /**
     * Map of standard model types to display labels.
     *
     * @return array<string, string>
     */
    public static function typeLabels(): array
    {
        return [
            'commercial' => 'Commercial Model',
            'commercial model' => 'Commercial Model',
            'runway' => 'Runway / Ramp Model',
            'runway / ramp model' => 'Runway / Ramp Model',
            'runway/ramp model' => 'Runway / Ramp Model',
            'editorial' => 'Editorial Model',
            'editorial model' => 'Editorial Model',
            'high-fashion' => 'High Fashion Model',
            'high fashion' => 'High Fashion Model',
            'high fashion model' => 'High Fashion Model',
            'fitness' => 'Fitness Model',
            'fitness model' => 'Fitness Model',
            'glamour' => 'Glamour Model',
            'glamour model' => 'Glamour Model',
            'print' => 'Print Model',
            'print model' => 'Print Model',
            'parts' => 'Parts Model',
            'parts model' => 'Parts Model',
            'promotional' => 'Promotional Model',
            'promotional model' => 'Promotional Model',
            'video' => 'Video Shoot Model',
            'video shoot model' => 'Video Shoot Model',
            'actor' => 'Actor',
        ];
    }

    /**
     * Format a model type string or key to human-readable label.
     */
    public static function formatType(?string $type): string
    {
        if (empty($type)) {
            return '';
        }
        $key = strtolower(trim($type));
        $labels = self::typeLabels();

        return $labels[$key] ?? trim($type);
    }

    /**
     * Get the formatted model types.
     *
     * @return array<int, string>
     */
    public function getFormattedTypesAttribute(): array
    {
        $types = (array) ($this->types ?? []);
        if (empty($types)) {
            return [];
        }

        $formatted = [];
        foreach ($types as $t) {
            $formatted[] = self::formatType((string) $t);
        }

        return array_values(array_filter($formatted));
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

        if (str_starts_with($clean, 'uploads/') || str_starts_with($clean, 'images/')) {
            return asset($clean);
        }

        return $fallback ? asset(ltrim($fallback, '/')) : asset($clean);
    }

    /**
     * Helper to get full profile photo URL.
     */
    public function getPhotoUrlAttribute(): string
    {
        return self::resolveImageUrl($this->profile_photo, 'images/models/model-01.jpg');
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
        $images = $this->portfolio_images;
        if (! is_array($images)) {
            return [];
        }

        $items = [];
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
