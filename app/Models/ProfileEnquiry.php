<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileEnquiry extends Model
{
    use HasFactory;

    protected $table = 'profile_enquiries';

    protected $fillable = [
        'professional_type',
        'professional_id',
        'professional_name',
        'professional_category',
        'name',
        'email',
        'phone',
        'assignment_type',
        'preferred_date',
        'location',
        'brief',
        'is_email_verified',
        'verified_at',
        'status',
        'admin_notes',
        'ip_address',
        'user_agent',
    ];

    public const STATUS_NEW = 'new';

    public const STATUS_CONTACTED = 'contacted';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    public const ASSIGNMENT_TYPES = [
        'Runway Show',
        'Photoshoot',
        'Fashion Campaign',
        'Commercial Advertisement',
        'Brand Promotion',
        'Event',
        'Other',
    ];

    protected function casts(): array
    {
        return [
            'preferred_date' => 'date',
            'is_email_verified' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    /**
     * Associated Model profile record if target was a model.
     */
    public function modelProfile(): BelongsTo
    {
        return $this->belongsTo(ModelProfile::class, 'professional_id');
    }

    /**
     * Associated Creative Professional profile record.
     */
    public function creativeProfessional(): BelongsTo
    {
        return $this->belongsTo(CreativeProfessional::class, 'professional_id');
    }

    /**
     * Associated Fashion Designer record.
     */
    public function designer(): BelongsTo
    {
        return $this->belongsTo(Designer::class, 'professional_id');
    }

    /**
     * Return formatted human label for the enquiry status.
     */
    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_NEW => 'New',
            self::STATUS_CONTACTED => 'Contacted',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    /**
     * CSS class modifier for status badges in the admin portal.
     */
    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_NEW => 'status-badge--active',
            self::STATUS_CONTACTED => 'status-badge--pending',
            self::STATUS_IN_PROGRESS => 'status-badge--gold',
            self::STATUS_COMPLETED => 'status-badge--success',
            self::STATUS_CANCELLED => 'status-badge--danger',
            default => 'status-badge--pending',
        };
    }
}
