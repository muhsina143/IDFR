<?php

namespace App\Models\Asdreams;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsdreamsTeamMember extends Model
{
    use HasFactory;

    protected $table = 'asdreams_team_members';

    protected $fillable = [
        'name',
        'designation',
        'bio',
        'photo_path',
        'social_links',
        'order',
        'is_published',
    ];

    protected $casts = [
        'social_links' => 'array',
        'order' => 'integer',
        'is_published' => 'boolean',
    ];

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->orderBy('order', 'asc');
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
