<?php

namespace App\Models\Asdreams;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsdreamsStudioEquipment extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'asdreams_studio_equipments';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'icon',
        'image_path',
        'display_order',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'display_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Available SVG sprite icon keys paired with display labels.
     *
     * @return array<string, string>
     */
    public static function availableIcons(): array
    {
        return [
            'light' => 'Lighting / Bulb',
            'sofa' => 'Sofa / Seating',
            'camera' => 'Camera / Rig',
            'check' => 'Checkmark / Gear',
            'sparkles' => 'Backdrop / Styling',
            'star' => 'Featured Equipment',
        ];
    }

    /**
     * Scope query to only active equipment ordered for display.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->orderBy('display_order', 'asc')
            ->orderBy('id', 'asc');
    }

    /**
     * Scope query ordered for admin display.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('display_order', 'asc')
            ->orderBy('id', 'asc');
    }

    /**
     * Get the web-accessible URL for an optional uploaded equipment image.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->image_path)) {
            return null;
        }

        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path;
        }

        return asset($this->image_path);
    }
}
