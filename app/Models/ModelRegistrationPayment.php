<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModelRegistrationPayment extends Model
{
    use HasFactory;

    protected $table = 'model_registration_payments';

    protected $fillable = [
        'season_model_registration_id',
        'payment_reference',
        'amount',
        'payment_method',
        'transaction_reference',
        'payment_date',
        'payment_status',
        'notes',
        'recorded_by_user_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
    ];

    /**
     * Parent model registration.
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(SeasonModelRegistration::class, 'season_model_registration_id');
    }

    /**
     * Admin user who recorded this payment.
     */
    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id');
    }

    /**
     * Formatted amount with currency symbol.
     */
    public function getFormattedAmountAttribute(): string
    {
        return '₹'.number_format((float) $this->amount, 0);
    }
}
