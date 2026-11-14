<?php

namespace App\Http\Requests\Profile;

use App\Http\Controllers\ProfileController;
use App\Http\Requests\Concerns\CommonRules;
use App\Models\ProfessionalCategory;
use Database\Seeders\DynamicProfileFormSeeder;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates dynamic creative professional profile form submissions across all creative categories.
 */
class SaveCreativeProfileRequest extends FormRequest
{
    use CommonRules;

    protected ?ProfessionalCategory $resolvedCategory = null;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Resolve the normalized category slug from route parameters or path segments.
     */
    public function resolveCategorySlug(): string
    {
        $slug = $this->route('category');
        if (! is_string($slug) || $slug === '') {
            $slug = basename(trim($this->path(), '/'));
        }

        if ($slug === 'party-event-mnagement') {
            $slug = 'party-event-management';
        }

        return $slug;
    }

    /**
     * Resolve and cache the active category model for this request.
     */
    protected function categoryModel(): ?ProfessionalCategory
    {
        if ($this->resolvedCategory !== null) {
            return $this->resolvedCategory;
        }

        $slug = $this->resolveCategorySlug();
        if ($slug === '') {
            return null;
        }

        $categoryModel = ProfessionalCategory::where('slug', $slug)->first();
        if (! $categoryModel) {
            (new DynamicProfileFormSeeder)->run();
            $categoryModel = ProfessionalCategory::where('slug', $slug)->first();
        }

        $this->resolvedCategory = $categoryModel;

        return $categoryModel;
    }

    /**
     * Prepare incoming creative profile data for validation.
     *
     * For checkbox_group service fields: alias service_* → service[] so the
     * existing array validation path works unchanged.
     * For select/dropdown service fields: leave the raw key as-is; the rules()
     * method will validate it as a scalar string under its own key.
     */
    protected function prepareForValidation(): void
    {
        $this->normalizeInputs(phoneFields: ['phone'], emailFields: ['email']);

        $categoryModel = $this->categoryModel();
        if (! $categoryModel || $this->has('service')) {
            return;
        }

        foreach ($categoryModel->getActiveFormFields() as $field) {
            $key = $field->field_key;
            $isServiceKey = ($key === 'service' || str_starts_with($key, 'service_') || str_contains($key, 'service'));

            if (! $isServiceKey || ! $this->has($key)) {
                continue;
            }

            // Only alias to service[] for checkbox_group (multi-select).
            // select/dropdown fields stay under their own key.
            if (in_array($field->field_type, ['checkbox_group', 'checkboxes'], true)) {
                $this->merge(['service' => $this->input($key)]);
            }

            break;
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => self::nameRules(required: true, min: 2, max: 150),
            'profession' => ['nullable', 'string', 'max:150'],
            'place' => ['required', 'string', 'min:2', 'max:100'],
            'experience' => ['required', 'numeric', 'min:0', 'max:40'],
            'phone' => self::phoneRules(required: true),
            'email' => self::emailRules(required: true, checkDns: true, max: 150),
            'about' => ['required', 'string', 'min:20'],
            'kit' => ['nullable', 'string'],
            'equipment' => ['nullable', 'string'],
            'software' => ['nullable', 'string'],
            'materials' => ['nullable', 'string'],
            'atelier' => ['nullable', 'string'],
            'atelier_notes' => ['nullable', 'string'],
            'service' => ['nullable', 'array'],
            'service.*' => ['string'],
            'outstation' => ['required', 'in:yes,no,Yes,No'],
            'brand' => ['nullable', 'string', 'max:100'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'portfolio_image_1' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'portfolio_image_2' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'portfolio_images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ];

        $categoryModel = $this->categoryModel();
        if (! $categoryModel) {
            return $rules;
        }

        $activeFields = $categoryModel->getActiveFormFields();
        $hasServiceField = false;

        foreach ($activeFields as $field) {
            $key = $field->field_key;
            $isServiceField = ($key === 'service' || str_starts_with($key, 'service_') || str_contains($key, 'service'));

            if ($isServiceField) {
                $hasServiceField = true;
                $options = $field->options;
                if (is_string($options)) {
                    $decoded = json_decode($options, true);
                    $options = is_array($decoded) ? $decoded : [];
                }
                $hasConfiguredOptions = ! empty($options);

                // select/dropdown service fields submit a single scalar value.
                // checkbox_group fields submit an array; keep the existing array path.
                $isSelectType = in_array($field->field_type, ['select', 'dropdown'], true);

                if ($isSelectType) {
                    // Validate under the field's own key (e.g. event_service) as a string.
                    $rules[$key] = ($field->is_required && $hasConfiguredOptions)
                        ? ['required', 'string']
                        : ['nullable', 'string'];
                    // Also allow it in $rules under 'service' as optional (prepareForValidation won't alias it).
                    unset($rules['service'], $rules['service.*']);
                } else {
                    $rules['service'] = ($field->is_required && $hasConfiguredOptions)
                        ? ['required', 'array', 'min:1']
                        : ['nullable', 'array'];
                    $rules['service.*'] = ['string'];

                    if ($key !== 'service') {
                        $rules[$key] = ['nullable', 'array'];
                        $rules["{$key}.*"] = ['string'];
                    }
                }
            } elseif (in_array($key, ['kit', 'equipment', 'software', 'materials', 'atelier'], true)) {
                $rules[$key] = $field->is_required ? ['required', 'string'] : ['nullable', 'string'];
            } elseif (! isset($rules[$key])) {
                $rules[$key] = ProfileController::sanitizeValidationRules(
                    $field->validation_rules,
                    (bool) $field->is_required,
                    (string) $field->field_type
                );
            }
        }

        if (! $hasServiceField) {
            unset($rules['service'], $rules['service.*']);
        }

        return $rules;
    }

    /**
     * Get custom validation error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return array_merge($this->sharedValidationMessages(), [
            'service.required' => 'Please select at least one specialisation or service.',
            'service.array' => 'Please select your specialisations using the checkboxes.',
            'about.min' => 'Please write at least 20 characters in your bio.',
            'place.required' => 'Please select your city or location.',
            // Covers select-type service fields like event_service
            '*.required' => 'This field is required.',
        ]);
    }
}
