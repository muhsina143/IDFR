<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeasonImage extends Model
{
    use HasFactory;

    protected $table = 'season_images';

    protected $fillable = [
        'season_id',
        'image_path',
        'image_title',
        'caption',
        'designer_name',
        'look_number',
        'alt_text',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    /**
     * Relationship to parent runway season.
     */
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class, 'season_id');
    }

    /**
     * Get accessible public URL for the image.
     */
    public function getImageUrlAttribute(): string
    {
        if (empty($this->image_path)) {
            return asset('images/hero/hero-01.jpg');
        }

        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path;
        }

        return asset(ltrim($this->image_path, '/'));
    }
}
