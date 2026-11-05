<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProfessionalFormField extends Model
{
    use HasFactory;

    protected $table = 'professional_form_fields';

    protected $fillable = [
        'field_key',
        'label',
        'field_type',
        'placeholder',
        'help_text',
        'default_value',
        'options',
        'validation_rules',
        'is_required',
        'is_common',
        'width',
        'display_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'is_required' => 'boolean',
            'is_common' => 'boolean',
            'display_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Categories this field is assigned to.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ProfessionalCategory::class, 'category_form_fields', 'field_id', 'category_id')
            ->withPivot(['id', 'display_order', 'is_required', 'label_override', 'placeholder_override', 'options_override', 'help_text_override', 'is_active'])
            ->withTimestamps();
    }

    /**
     * Category pivots.
     */
    public function categoryPivots(): HasMany
    {
        return $this->hasMany(CategoryFormField::class, 'field_id');
    }
}
