<?php

namespace App\Models\Asdreams;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsdreamsOfferImage extends Model
{
    use HasFactory;

    protected $table = 'asdreams_offer_images';

    protected $fillable = [
        'offer_id',
        'image_path',
        'caption',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function offer(): BelongsTo
    {
        return $this->belongsTo(AsdreamsOffer::class, 'offer_id');
    }

    public function getImageUrlAttribute(): string
    {
        if (empty($this->image_path)) {
            return asset('images/asdreams/hero-01.jpg');
        }

        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path;
        }

        return asset($this->image_path);
    }
}
