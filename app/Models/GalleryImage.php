<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GalleryImage extends Model
{
    use HasFactory;

    protected $table = 'gallery_images';

    protected $fillable = [
        'season_id',
        'season_name',
        'title',
        'alt_text',
        'image_path',
        'width',
        'height',
        'display_order',
        'is_featured',
        'status',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'width' => 'integer',
        'height' => 'integer',
        'display_order' => 'integer',
    ];

    /**
     * Relationship to Season.
     */
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class, 'season_id');
    }

    /**
     * Get accessible image URL with fallback.
     */
    public function getImageUrlAttribute(): string
    {
        if (empty($this->image_path)) {
            return asset('images/gallery/s1-01.jpg');
        }

        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path;
        }

        return asset($this->image_path);
    }
}
