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
     * Helper to get full image URL.
     */
    public function getPhotoUrlAttribute(): string
    {
        if (empty($this->profile_photo)) {
            return asset('images/models/model-01.jpg');
        }
        if (str_starts_with($this->profile_photo, 'http://') || str_starts_with($this->profile_photo, 'https://')) {
            return $this->profile_photo;
        }

        $relPath = ltrim($this->profile_photo, '/');
        if (file_exists(public_path($relPath))) {
            return asset($relPath);
        }

        return asset('images/models/model-01.jpg');
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
