<?php

namespace App\Models\Asdreams;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AsdreamsBanner extends Model
{
    use HasFactory;

    protected $table = 'asdreams_banners';

    protected $fillable = [
        'title',
        'highlight_title',
        'label',
        'description',
        'image',
        'alt_text',
        'button_text',
        'linked_offer_id',
        'linked_offer_slug',
        'order',
        'is_active',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function linkedOffer(): BelongsTo
    {
        return $this->belongsTo(AsdreamsOffer::class, 'linked_offer_id');
    }

    public function offer(): HasOne
    {
        return $this->hasOne(AsdreamsOffer::class, 'banner_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order', 'asc');
    }

    public function getImageUrlAttribute(): string
    {
        if (empty($this->image)) {
            return asset('images/asdreams/hero-01.jpg');
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        return asset($this->image);
    }

    /**
     * Get target Offer URL.
     */
    public function getOfferUrlAttribute(): string
    {
        if ($this->linkedOffer) {
            return route('asd.offers.show', $this->linkedOffer->slug);
        }

        if ($this->offer) {
            return route('asd.offers.show', $this->offer->slug);
        }

        if (! empty($this->linked_offer_slug)) {
            return route('asd.offers.show', $this->linked_offer_slug);
        }

        return route('asd.offers.index');
    }
}
