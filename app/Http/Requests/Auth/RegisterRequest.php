<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Concerns\CommonRules;
use App\Models\ProfessionalCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates public user registration submissions.
 */
class RegisterRequest extends FormRequest
{
    use CommonRules;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare incoming registration data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->normalizeInputs(phoneFields: ['phone'], emailFields: ['email']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $allowedRoles = array_values(array_unique(array_merge(
            [
                'model',
                'fashion-designer',
                'makeup-artist',
                'hairstylist',
                'photographer',
                'videographer',
                'drone-pilot',
                'photo-video-editor',
                'jewellery-designer',
                'stylist',
                'party-event-management',
                'party-event-mnagement',
            ],
            ProfessionalCategory::where('is_active', true)->pluck('slug')->toArray()
        )));

        return [
            'name' => self::nameRules(required: true, min: 2, max: 60),
            'email' => self::emailRules(required: true, checkDns: true, max: 255),
            'phone' => self::phoneRules(required: true),
            'role' => ['required', 'string', Rule::in($allowedRoles)],
            'model_type' => ['nullable', 'string', 'max:100'],
            'password' => self::passwordRules(confirmed: true, min: 8, max: 64),
            'terms' => ['accepted'],
        ];
    }

    /**
     * Get custom validation error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return array_merge($this->sharedValidationMessages(), [
            'role.required' => 'Please select a category or role.',
            'role.in' => 'Please select a valid registration role.',
            'terms.accepted' => 'You must accept the terms and privacy policy to register.',
        ]);
    }
}
