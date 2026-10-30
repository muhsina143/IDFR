<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnquiryOtp extends Model
{
    use HasFactory;

    protected $table = 'enquiry_otps';

    protected $fillable = [
        'email',
        'otp_hash',
        'session_id',
        'ip_address',
        'attempts',
        'expires_at',
        'sent_at',
        'verified_at',
    ];

    public const EXPIRY_MINUTES = 5;

    public const RESEND_COOLDOWN_SECONDS = 60;

    public const MAX_ATTEMPTS = 5;

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'sent_at' => 'datetime',
            'verified_at' => 'datetime',
            'attempts' => 'integer',
        ];
    }

    public function isExpired(): bool
    {
        return ! $this->expires_at || now()->greaterThan($this->expires_at);
    }

    public function isVerified(): bool
    {
        return ! is_null($this->verified_at);
    }

    public function hasExceededAttempts(): bool
    {
        return $this->attempts >= self::MAX_ATTEMPTS;
    }
}
