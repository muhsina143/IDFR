<?php

namespace App\Models\Asdreams;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsdreamsOfferEnquiry extends Model
{
    use HasFactory;

    protected $table = 'asdreams_offer_enquiries';

    protected $fillable = [
        'banner_id',
        'offer_id',
        'offer_title',
        'name',
        'phone',
        'email',
        'preferred_date',
        'message',
        'status',
        'admin_notes',
        'ip_address',
    ];

    protected $casts = [
        'preferred_date' => 'date',
    ];

    public function banner(): BelongsTo
    {
        return $this->belongsTo(AsdreamsBanner::class, 'banner_id');
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(AsdreamsOffer::class, 'offer_id');
    }

    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeRecent($query)
    {
        return $query->latest();
    }
}
