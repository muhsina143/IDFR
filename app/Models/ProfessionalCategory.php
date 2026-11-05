<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class ProfessionalCategory extends Model
{
    use HasFactory;

    protected $table = 'professional_categories';

    protected $fillable = [
        'slug',
        'name',
        'hero_title',
        'description',
        'form_title',
        'form_lead',
        'default_photo',
        'banner_image',
        'display_order',
        'is_active',
    ];

    /**
     * Get URL for default category photo / thumbnail.
     */
    public function getPhotoUrlAttribute(): string
    {
        if ($this->default_photo) {
            return str_starts_with($this->default_photo, 'http')
                ? $this->default_photo
                : asset(ltrim($this->default_photo, '/'));
        }

        return asset('images/creative/makeup-artist/ananya-menon.jpg');
    }

    /**
     * Get URL for category banner image.
     */
    public function getBannerUrlAttribute(): string
    {
        if ($this->banner_image) {
            return str_starts_with($this->banner_image, 'http')
                ? $this->banner_image
                : asset(ltrim($this->banner_image, '/'));
        }

        return $this->photo_url;
    }

    /**
     * Creative professionals registered under this category.
     */
    public function profiles(): HasMany
    {
        return $this->hasMany(CreativeProfessional::class, 'category', 'slug')
            ->orderBy('display_order')
            ->orderBy('id');
    }

    protected function casts(): array
    {
        return [
            'display_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Fields assigned specifically to this category through the pivot.
     */
    public function categoryFields(): HasMany
    {
        return $this->hasMany(CategoryFormField::class, 'category_id')->orderBy('display_order');
    }

    /**
     * Form fields associated with this category.
     */
    public function fields(): BelongsToMany
    {
        return $this->belongsToMany(ProfessionalFormField::class, 'category_form_fields', 'category_id', 'field_id')
            ->withPivot(['id', 'display_order', 'is_required', 'label_override', 'placeholder_override', 'options_override', 'help_text_override', 'is_active'])
            ->withTimestamps()
            ->orderByPivot('display_order');
    }

    /**
     * Get all active ordered form fields for this category, combining common fields and category-specific overrides.
     *
     * @return Collection<int, object>
     */
    public function getActiveFormFields()
    {
        $pivotFields = $this->fields()
            ->wherePivot('is_active', true)
            ->where('professional_form_fields.is_active', true)
            ->get();

        return $pivotFields->map(function ($field) {
            $pivot = $field->pivot;

            $rawOptions = ! empty($pivot->options_override) ? $pivot->options_override : $field->options;
            $options = $rawOptions;
            if (is_string($options)) {
                $decoded = json_decode($options, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $options = $decoded;
                }
            }

            return (object) [
                'id' => $field->id,
                'pivot_id' => $pivot->id,
                'field_key' => $field->field_key,
                'label' => $pivot->label_override ?: $field->label,
                'field_type' => $field->field_type,
                'placeholder' => $pivot->placeholder_override !== null ? $pivot->placeholder_override : $field->placeholder,
                'help_text' => $pivot->help_text_override ?: $field->help_text,
                'default_value' => $field->default_value,
                'options' => $options,
                'validation_rules' => $field->validation_rules,
                'is_required' => $pivot->is_required !== null ? (bool) $pivot->is_required : (bool) $field->is_required,
                'is_common' => (bool) $field->is_common,
                'width' => $field->width,
                'display_order' => $pivot->display_order ?? $field->display_order,
            ];
        })->sortBy('display_order')->values();
    }
}
