<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Season extends Model
{
    use HasFactory;

    protected $table = 'seasons';

    protected $fillable = [
        'number',
        'title',
        'year',
        'location',
        'dates',
        'status',
        'description',
        'hero_image',
    ];

    /**
     * Get the hero image public URL.
     */
    public function getHeroImageUrlAttribute(): ?string
    {
        if (! $this->hero_image) {
            return null;
        }

        if (str_starts_with($this->hero_image, 'http://') || str_starts_with($this->hero_image, 'https://')) {
            return $this->hero_image;
        }

        return asset(ltrim($this->hero_image, '/'));
    }

    /**
     * Models associated with this season.
     */
    public function models(): BelongsToMany
    {
        return $this->belongsToMany(ModelProfile::class, 'model_season', 'season_id', 'model_id');
    }

    /**
     * Designers associated with this season.
     */
    public function designers(): BelongsToMany
    {
        return $this->belongsToMany(Designer::class, 'designer_season', 'season_id', 'designer_id');
    }

    /**
     * Images associated with this runway season (hero slider / lookbook).
     */
    public function images(): HasMany
    {
        return $this->hasMany(SeasonImage::class)->orderBy('display_order')->orderBy('id');
    }

    /**
     * Active images associated with this runway season.
     */
    public function activeImages(): HasMany
    {
        return $this->hasMany(SeasonImage::class)->where('is_active', true)->orderBy('display_order')->orderBy('id');
    }

    /**
     * Creative Professionals associated with this season.
     */
    public function creativeProfessionals(): BelongsToMany
    {
        return $this->belongsToMany(CreativeProfessional::class, 'creative_professional_season', 'season_id', 'creative_professional_id');
    }
}
