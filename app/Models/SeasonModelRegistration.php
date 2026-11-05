<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SeasonModelRegistration extends Model
{
    use HasFactory;

    protected $table = 'season_model_registrations';

    protected $fillable = [
        'registration_number',
        'season_id',
        'full_name',
        'whatsapp_number',
        'email',
        'age',
        'gender',
        'category',
        'citizenship',
        'parent_name',
        'parent_contact',
        'parent_relationship',
        'additional_info',
        'registration_fee',
        'amount_paid',
        'payment_status',
        'registration_status',
        'admin_notes',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'age' => 'integer',
        'registration_fee' => 'decimal:2',
        'amount_paid' => 'decimal:2',
    ];

    /**
     * Relationship to Season.
     */
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class, 'season_id');
    }

    /**
     * Relationship to Payments.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(ModelRegistrationPayment::class, 'season_model_registration_id')->orderByDesc('payment_date');
    }

    /**
     * Calculate remaining unpaid balance.
     */
    public function getRemainingBalanceAttribute(): float
    {
        $rem = (float) $this->registration_fee - (float) $this->amount_paid;

        return max(0.00, $rem);
    }

    public function getFormattedFeeAttribute(): string
    {
        return '₹'.number_format((float) $this->registration_fee, 0);
    }

    public function getFormattedPaidAttribute(): string
    {
        return '₹'.number_format((float) $this->amount_paid, 0);
    }

    public function getFormattedRemainingAttribute(): string
    {
        return '₹'.number_format($this->remaining_balance, 0);
    }

    /**
     * Check if registration is for a minor.
     */
    public function isKid(): bool
    {
        return strtolower($this->category) === 'kids' || $this->age < 18;
    }

    /**
     * Badge CSS class for registration status.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match (strtolower($this->registration_status)) {
            'approved' => 'badge-success',
            'pending' => 'badge-warning',
            'rejected' => 'badge-danger',
            'cancelled' => 'badge-secondary',
            default => 'badge-light',
        };
    }

    /**
     * Badge CSS class for payment status.
     */
    public function getPaymentBadgeClassAttribute(): string
    {
        return match (strtolower($this->payment_status)) {
            'paid' => 'badge-success',
            'pending' => 'badge-warning',
            'unpaid' => 'badge-danger',
            'failed' => 'badge-danger',
            'refunded' => 'badge-info',
            default => 'badge-light',
        };
    }

    /**
     * Generate unique registration number.
     * E.g. REG-S8-10492
     */
    public static function generateRegistrationNumber(int $seasonNumber = 8): string
    {
        do {
            $code = 'REG-S'.$seasonNumber.'-'.mt_rand(10000, 99999);
        } while (static::where('registration_number', $code)->exists());

        return $code;
    }

    /**
     * Recalculate and synchronize amount_paid and payment_status from payments.
     */
    public function syncPaymentStatus(): void
    {
        $totalPaid = (float) $this->payments()->where('payment_status', 'paid')->sum('amount');
        $this->amount_paid = $totalPaid;

        if ($totalPaid >= (float) $this->registration_fee) {
            $this->payment_status = 'paid';
        } elseif ($totalPaid > 0) {
            $this->payment_status = 'pending';
        } else {
            $this->payment_status = 'unpaid';
        }

        $this->save();
    }
}
