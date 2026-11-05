<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeasonRegistrationPrice extends Model
{
    use HasFactory;

    protected $table = 'season_registration_prices';

    protected $fillable = [
        'season_id',
        'price',
        'currency',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * The season this registration fee belongs to.
     */
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class, 'season_id');
    }

    /**
     * Formatted price with currency symbol.
     */
    public function getFormattedPriceAttribute(): string
    {
        return '₹'.number_format((float) $this->price, 0);
    }
}
