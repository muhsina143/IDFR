<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SponsorshipEnquiry extends Model
{
    use HasFactory;

    protected $table = 'sponsorship_enquiries';

    protected $fillable = [
        'season_id',
        'season_title',
        'name',
        'email',
        'phone',
        'sponsorship_type',
        'message',
        'status',
        'admin_notes',
        'ip_address',
        'user_agent',
    ];

    public const STATUS_NEW = 'new';

    public const STATUS_CONTACTED = 'contacted';

    public const STATUS_COMPLETED = 'completed';

    public const STATUSES = [
        self::STATUS_NEW => 'New',
        self::STATUS_CONTACTED => 'Contacted',
        self::STATUS_COMPLETED => 'Completed',
    ];

    public const DEFAULT_SPONSORSHIP_TYPES = [
        'Title Sponsor' => 'Title Sponsor',
        'Associate Sponsor' => 'Associate Sponsor',
        'Powered By Sponsor' => 'Powered By Sponsor',
        'Category / Segment Partner' => 'Category / Segment Partner',
        'Official Brand Partner' => 'Official Brand Partner',
        'Runway Lounge / VIP Sponsor' => 'Runway Lounge / VIP Sponsor',
        'Other / Custom Partnership' => 'Other / Custom Partnership',
    ];

    /**
     * Get list of available sponsorship options, combining standard categories and active sponsor tiers.
     *
     * @return array<string, string>
     */
    public static function availableTypes(): array
    {
        $types = self::DEFAULT_SPONSORSHIP_TYPES;

        try {
            $existingTiers = Sponsor::where('is_active', true)
                ->whereNotNull('tier')
                ->pluck('tier')
                ->map(fn ($t) => trim(explode('·', $t)[0]))
                ->filter()
                ->unique();

            foreach ($existingTiers as $tier) {
                if (! empty($tier) && ! isset($types[$tier])) {
                    $types[$tier] = $tier;
                }
            }
        } catch (\Throwable) {
            // Fallback to defaults
        }

        return $types;
    }

    /**
     * Get human-readable status label.
     */
    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst((string) $this->status);
    }

    /**
     * Get CSS badge class for status.
     */
    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_NEW => 'badge-status--new',
            self::STATUS_CONTACTED => 'badge-status--contacted',
            self::STATUS_COMPLETED => 'badge-status--completed',
            default => 'badge-status--new',
        };
    }

    /**
     * Associated runway season if linked.
     */
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class, 'season_id');
    }

    /**
     * Scope for unhandled/new enquiries.
     */
    public function scopeNewOnly($query)
    {
        return $query->where('status', self::STATUS_NEW);
    }

    /**
     * Scope for latest enquiries first.
     */
    public function scopeRecent($query)
    {
        return $query->latest();
    }
}
