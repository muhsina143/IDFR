<?php

namespace App\Models\Asdreams;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsdreamsGalleryImage extends Model
{
    use HasFactory;

    protected $table = 'asdreams_gallery_images';

    protected $fillable = [
        'category_id',
        'title',
        'image_path',
        'width',
        'height',
        'order',
        'is_published',
    ];

    protected $casts = [
        'width' => 'integer',
        'height' => 'integer',
        'order' => 'integer',
        'is_published' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(AsdreamsGalleryCategory::class, 'category_id');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->orderBy('order', 'asc');
    }

    public function getImageUrlAttribute(): string
    {
        if (empty($this->image_path)) {
            return asset('images/asdreams/gallery/wedding-01.jpg');
        }

        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path;
        }

        return asset($this->image_path);
    }
}
