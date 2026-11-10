<?php

namespace App\Models\Asdreams;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AsdreamsGalleryCategory extends Model
{
    use HasFactory;

    protected $table = 'asdreams_gallery_categories';

    protected $fillable = [
        'name',
        'slug',
        'tab_label',
        'order',
        'is_active',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function images(): HasMany
    {
        return $this->hasMany(AsdreamsGalleryImage::class, 'category_id')->orderBy('order', 'asc');
    }

    public function publishedImages(): HasMany
    {
        return $this->hasMany(AsdreamsGalleryImage::class, 'category_id')
            ->where('is_published', true)
            ->orderBy('order', 'asc');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order', 'asc');
    }
}
