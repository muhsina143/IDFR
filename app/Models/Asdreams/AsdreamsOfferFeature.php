<?php

namespace App\Models\Asdreams;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsdreamsOfferFeature extends Model
{
    use HasFactory;

    protected $table = 'asdreams_offer_features';

    protected $fillable = [
        'offer_id',
        'feature_text',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function offer(): BelongsTo
    {
        return $this->belongsTo(AsdreamsOffer::class, 'offer_id');
    }
}
