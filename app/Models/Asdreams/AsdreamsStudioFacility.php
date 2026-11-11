<?php

namespace App\Models\Asdreams;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsdreamsStudioFacility extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'asdreams_studio_facilities';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'icon',
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
            'door' => 'Lobby / Door',
            'sparkles' => 'Makeup / Sparkles',
            'hanger' => 'Changing Room / Wardrobe',
            'cup' => 'Pantry / Refreshments',
            'camera' => 'Photography Room / Studio',
            'restroom' => 'Restrooms',
            'snowflake' => 'Air Conditioning',
            'area' => 'Floor Area / Grid',
            'check' => 'Checkmark / General',
            'star' => 'Premium Feature',
        ];
    }

    /**
     * Scope query to only active facilities ordered for display.
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
}
