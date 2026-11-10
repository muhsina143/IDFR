<?php

namespace App\Models\Asdreams;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsdreamsClient extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'asdreams_clients';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'category',
        'logo_path',
        'logo_style',
        'logo_mark',
        'logo_word',
        'logo_sub',
        'featured_image',
        'description',
        'author_name',
        'author_role',
        'is_featured',
        'display_order',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_featured' => 'boolean',
        'display_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Scope a query to only include active clients ordered for display.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->orderBy('display_order', 'asc')
            ->orderBy('id', 'asc');
    }

    /**
     * Scope a query ordered for admin display.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('display_order', 'asc')
            ->orderBy('id', 'asc');
    }

    /**
     * Get the web-accessible URL for the featured client image.
     */
    public function getFeaturedImageUrlAttribute(): string
    {
        if (! empty($this->featured_image)) {
            if (str_starts_with($this->featured_image, 'http://') || str_starts_with($this->featured_image, 'https://')) {
                return $this->featured_image;
            }

            return asset($this->featured_image);
        }

        return asset('images/clients/client-featured.jpg');
    }

    /**
     * Get the web-accessible URL for the uploaded client logo image.
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (empty($this->logo_path)) {
            return null;
        }

        if (str_starts_with($this->logo_path, 'http://') || str_starts_with($this->logo_path, 'https://')) {
            return $this->logo_path;
        }

        return asset($this->logo_path);
    }

    /**
     * Mark this client as the single initially featured client card.
     */
    public function makeFeatured(): void
    {
        static::where('id', '!=', $this->id)->update(['is_featured' => false]);
        $this->is_featured = true;
        $this->save();
    }
}
