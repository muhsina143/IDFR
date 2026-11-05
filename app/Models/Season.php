<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Season extends Model
{
    use HasFactory;

    protected $table = 'seasons';

    protected $fillable = [
        'number',
        'title',
        'hero_heading',
        'hero_highlighted',
        'year',
        'location',
        'dates',
        'status',
        'is_current',
        'description',
        'hero_image',
        'schedule',
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'schedule' => 'array',
    ];

    /**
     * Scope a query to only include the active/current runway season.
     */
    public function scopeActive($query)
    {
        return $query->where('is_current', true);
    }

    /**
     * Get the single active/current runway season across the entire application.
     * Guaranteed single source of truth: checks is_current first, falling back to
     * highest published edition if no season is explicitly marked active.
     */
    public static function current(): ?self
    {
        return static::where('is_current', true)->first()
            ?? static::where('status', 'published')->orderByDesc('number')->first()
            ?? static::orderByDesc('number')->first();
    }

    /**
     * Set this season as the single active season across the application.
     * Automatically deactivates all other seasons.
     */
    public function makeCurrent(): self
    {
        static::where('id', '!=', $this->id)->where('is_current', true)->update(['is_current' => false]);
        $this->update(['is_current' => true]);

        return $this;
    }

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
     * Get the hero main heading with fallback.
     */
    public function getHeroHeadingDisplayAttribute(): string
    {
        return filled($this->hero_heading) ? $this->hero_heading : 'The Runway';
    }

    /**
     * Get the hero highlighted heading with fallback.
     */
    public function getHeroHighlightedDisplayAttribute(): string
    {
        return filled($this->hero_highlighted) ? $this->hero_highlighted : 'Returns';
    }

    /**
     * Get the hero description with fallback.
     */
    public function getHeroDescriptionDisplayAttribute(): string
    {
        return filled($this->description) ? $this->description : 'Couture, heritage and new faces under the lights — three nights of glamour in Kozhikode. Claim your place on the front row.';
    }

    /**
     * Get structured schedule events for the season modal.
     * Returns custom configured events or dynamically generated 3-night showcase.
     *
     * @return array<int, array{number: string, meta: string, title: string, description: string}>
     */
    public function getScheduleEventsAttribute(): array
    {
        if (! empty($this->schedule) && is_array($this->schedule)) {
            $normalized = [];
            foreach ($this->schedule as $idx => $event) {
                if (! is_array($event)) {
                    continue;
                }
                $num = ! empty($event['number']) ? str_pad((string) $event['number'], 2, '0', STR_PAD_LEFT) : str_pad((string) ($idx + 1), 2, '0', STR_PAD_LEFT);
                $normalized[] = [
                    'number' => $num,
                    'meta' => $event['meta'] ?? '',
                    'title' => $event['title'] ?? ('Night '.($idx + 1)),
                    'description' => $event['description'] ?? '',
                ];
            }
            if (! empty($normalized)) {
                return $normalized;
            }
        }

        $datesText = (string) ($this->dates ?: '18 — 20 Dec 2026');
        $seasonTitle = $this->title ?: 'Season '.$this->number;

        $meta1 = '18 DECEMBER · 7:00 PM';
        $meta2 = '19 DECEMBER · 7:00 PM';
        $meta3 = '20 DECEMBER · 7:30 PM';

        if (preg_match('/(\d{1,2})\s*[-–—]\s*(\d{1,2})\s+([A-Za-z]+)(?:\s+(\d{4}))?/u', $datesText, $m)) {
            $startDay = (int) $m[1];
            $endDay = (int) $m[2];
            $month = strtoupper($m[3]);
            $day1 = $startDay;
            $day2 = $startDay + 1;
            $day3 = $endDay;
            $meta1 = "{$day1} {$month} · 7:00 PM";
            $meta2 = "{$day2} {$month} · 7:00 PM";
            $meta3 = "{$day3} {$month} · 7:30 PM";
        }

        return [
            [
                'number' => '01',
                'meta' => $meta1,
                'title' => 'Night One — New Faces',
                'description' => "Debut collections walked by models from the {$seasonTitle} open auditions.",
            ],
            [
                'number' => '02',
                'meta' => $meta2,
                'title' => 'Night Two — Heritage & Handloom',
                'description' => 'Kerala handloom, Kanjeevaram and heritage textiles reimagined for today.',
            ],
            [
                'number' => '03',
                'meta' => $meta3,
                'title' => 'Night Three — The Grand Finale',
                'description' => "Couture showcases, awards and the closing walk of {$seasonTitle}.",
            ],
        ];
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

    /**
     * Registration fee configuration for this season.
     */
    public function registrationPrice(): HasOne
    {
        return $this->hasOne(SeasonRegistrationPrice::class, 'season_id');
    }

    /**
     * Model registrations submitted for this season.
     */
    public function modelRegistrations(): HasMany
    {
        return $this->hasMany(SeasonModelRegistration::class, 'season_id');
    }

    /**
     * Get active registration price or fallback default (9999.00).
     */
    public function getRegistrationFee(): float
    {
        if ($this->relationLoaded('registrationPrice') && $this->registrationPrice && $this->registrationPrice->is_active) {
            return (float) $this->registrationPrice->price;
        }

        $priceModel = $this->registrationPrice()->first();
        if ($priceModel && $priceModel->is_active) {
            return (float) $priceModel->price;
        }

        return 9999.00;
    }

    /**
     * Formatted registration fee (e.g. ₹9,999).
     */
    public function formattedRegistrationFee(): string
    {
        return '₹'.number_format($this->getRegistrationFee(), 0);
    }
}
