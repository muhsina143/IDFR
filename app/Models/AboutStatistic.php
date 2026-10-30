<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutStatistic extends Model
{
    use HasFactory;

    protected $table = 'about_statistics';

    protected $fillable = [
        'value',
        'title',
        'prefix',
        'suffix',
        'display_order',
        'status',
        'is_active',
    ];

    protected $casts = [
        'display_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Bootstrap model events to ensure status and is_active stay synchronized.
     */
    protected static function booted(): void
    {
        static::saving(function (self $model): void {
            if ($model->isDirty('status') && ! $model->isDirty('is_active')) {
                $model->is_active = ($model->status === 'active');
            } elseif ($model->isDirty('is_active') && ! $model->isDirty('status')) {
                $model->status = $model->is_active ? 'active' : 'inactive';
            }
        });
    }

    /**
     * Scope a query to only include active statistics.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->where('status', 'active');
    }

    /**
     * Scope a query to order statistics by display_order.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('display_order', 'asc')->orderBy('id', 'asc');
    }

    /**
     * Determine if the raw statistic value is purely numeric.
     */
    public function getIsNumericAttribute(): bool
    {
        $clean = str_replace([',', ' '], '', trim((string) $this->value));

        return is_numeric($clean);
    }

    /**
     * Get the clean numeric target value for counter animation.
     */
    public function getCleanNumberAttribute(): ?float
    {
        if (! $this->is_numeric) {
            return null;
        }

        $clean = str_replace([',', ' '], '', trim((string) $this->value));

        return (float) $clean;
    }

    /**
     * Detect if this value requires padding with leading zeros (e.g., '04' -> 2).
     */
    public function getPadLengthAttribute(): int
    {
        $val = trim((string) $this->value);
        if ($this->is_numeric && strlen($val) > 1 && str_starts_with($val, '0')) {
            return strlen($val);
        }

        return 0;
    }

    /**
     * Get formatted value with thousand separators or leading zero preserved.
     */
    public function getFormattedValueAttribute(): string
    {
        $val = trim((string) $this->value);
        if (! $this->is_numeric) {
            return $val;
        }

        if ($this->pad_length > 0) {
            return $val;
        }

        $clean = (float) str_replace([',', ' '], '', $val);

        // If it's a whole number, format with comma
        if (floor($clean) == $clean) {
            return number_format((int) $clean);
        }

        return (string) $clean;
    }

    /**
     * Full display string with prefix and suffix.
     */
    public function getFullDisplayAttribute(): string
    {
        return ($this->prefix ?? '').$this->formatted_value.($this->suffix ?? '');
    }
}
