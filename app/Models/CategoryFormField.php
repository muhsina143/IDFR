<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryFormField extends Model
{
    use HasFactory;

    protected $table = 'category_form_fields';

    protected $fillable = [
        'category_id',
        'field_id',
        'display_order',
        'is_required',
        'label_override',
        'placeholder_override',
        'options_override',
        'help_text_override',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'display_order' => 'integer',
            'is_required' => 'boolean',
            'options_override' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProfessionalCategory::class, 'category_id');
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(ProfessionalFormField::class, 'field_id');
    }
}
