<?php

namespace App\Models\Asdreams;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsdreamsTeamMember extends Model
{
    use HasFactory;

    public const TYPE_FOUNDER = 'founder';

    public const TYPE_CO_FOUNDER = 'co_founder';

    public const TYPE_TEAM_MEMBER = 'team_member';

    /**
     * Supported team hierarchy types mapped to human labels.
     *
     * @var array<string, string>
     */
    public const MEMBER_TYPES = [
        self::TYPE_FOUNDER => 'Founder',
        self::TYPE_CO_FOUNDER => 'Co-Founder',
        self::TYPE_TEAM_MEMBER => 'Team Member',
    ];

    protected $table = 'asdreams_team_members';

    protected $fillable = [
        'name',
        'member_type',
        'designation',
        'bio',
        'photo_path',
        'social_links',
        'display_order',
        'is_active',
        'order',
        'is_published',
    ];

    protected $casts = [
        'social_links' => 'array',
        'display_order' => 'integer',
        'order' => 'integer',
        'is_active' => 'boolean',
        'is_published' => 'boolean',
    ];

    /**
     * Keep legacy and new ordering/visibility attributes synchronized.
     */
    protected static function booted(): void
    {
        static::saving(function (self $member): void {
            if (empty($member->member_type)) {
                $member->member_type = self::TYPE_TEAM_MEMBER;
            }

            if ($member->isDirty('display_order') && ! $member->isDirty('order')) {
                $member->order = (int) ($member->display_order ?? 0);
            } elseif ($member->isDirty('order') && ! $member->isDirty('display_order')) {
                $member->display_order = (int) ($member->order ?? 0);
            } elseif (! $member->exists) {
                $resolvedOrder = (int) ($member->display_order ?? $member->order ?? 0);
                $member->display_order = $resolvedOrder;
                $member->order = $resolvedOrder;
            }

            if ($member->isDirty('is_active') && ! $member->isDirty('is_published')) {
                $member->is_published = (bool) $member->is_active;
            } elseif ($member->isDirty('is_published') && ! $member->isDirty('is_active')) {
                $member->is_active = (bool) $member->is_published;
            } elseif (! $member->exists) {
                $resolvedActive = $member->is_active ?? $member->is_published ?? true;
                $member->is_active = (bool) $resolvedActive;
                $member->is_published = (bool) $resolvedActive;
            }
        });
    }

    /**
     * Scope query to active members.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Backward-compatible published scope ordered for display.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->active()->ordered();
    }

    /**
     * Scope query to founders only.
     */
    public function scopeFounders(Builder $query): Builder
    {
        return $query->where('member_type', self::TYPE_FOUNDER);
    }

    /**
     * Scope query to co-founders only.
     */
    public function scopeCoFounders(Builder $query): Builder
    {
        return $query->where('member_type', self::TYPE_CO_FOUNDER);
    }

    /**
     * Scope query to general studio team members only.
     */
    public function scopeTeamMembers(Builder $query): Builder
    {
        return $query->where('member_type', self::TYPE_TEAM_MEMBER);
    }

    /**
     * Scope query ordered by display order ascending.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('display_order', 'asc')
            ->orderBy('id', 'asc');
    }

    public function getMemberTypeLabelAttribute(): string
    {
        return self::MEMBER_TYPES[$this->member_type] ?? self::MEMBER_TYPES[self::TYPE_TEAM_MEMBER];
    }

    public function getInstagramAttribute(): ?string
    {
        return $this->social_links['instagram'] ?? null;
    }

    public function getLinkedinAttribute(): ?string
    {
        return $this->social_links['linkedin'] ?? null;
    }

    public function getPhotoUrlAttribute(): string
    {
        if (empty($this->photo_path)) {
            return asset('images/founders/tt6.jpg');
        }

        if (str_starts_with($this->photo_path, 'http://') || str_starts_with($this->photo_path, 'https://')) {
            return $this->photo_path;
        }

        return asset($this->photo_path);
    }
}
