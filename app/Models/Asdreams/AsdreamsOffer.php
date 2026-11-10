<?php

namespace App\Models\Asdreams;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AsdreamsOffer extends Model
{
    use HasFactory;

    protected $table = 'asdreams_offers';

    protected $fillable = [
        'banner_id',
        'title',
        'slug',
        'category_label',
        'heading_main',
        'heading_highlight',
        'lead_text',
        'full_description',
        'image',
        'price',
        'validity',
        'notes',
        'terms',
        'important_notes',
        'enquiry_placeholder',
        'order',
        'status',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    /**
     * Relationship to banner.
     */
    public function banner(): BelongsTo
    {
        return $this->belongsTo(AsdreamsBanner::class, 'banner_id');
    }

    /**
     * Relationship to features.
     */
    public function features(): HasMany
    {
        return $this->hasMany(AsdreamsOfferFeature::class, 'offer_id')->orderBy('order', 'asc');
    }

    /**
     * Relationship to gallery images.
     */
    public function images(): HasMany
    {
        return $this->hasMany(AsdreamsOfferImage::class, 'offer_id')->orderBy('order', 'asc');
    }

    /**
     * Relationship to enquiries.
     */
    public function enquiries(): HasMany
    {
        return $this->hasMany(AsdreamsOfferEnquiry::class, 'offer_id');
    }

    /**
     * Scope published offers.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')->orderBy('order', 'asc');
    }

    /**
     * Get image URL with fallback.
     */
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
}
