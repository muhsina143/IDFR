<?php

namespace App\Http\Requests\Idfr;

use App\Http\Requests\Concerns\CommonRules;
use App\Models\ProfileEnquiry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates public talent/profile booking enquiry modal submissions.
 */
class ProfileEnquiryRequest extends FormRequest
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
     * Prepare incoming profile enquiry data for validation.
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
        return [
            'professional_type' => ['required', 'string', 'in:model,creative_professional,designer'],
            'professional_id' => ['nullable', 'integer', 'min:1'],
            'professional_name' => ['required', 'string', 'max:150'],
            'professional_category' => ['required', 'string', 'max:100'],
            'name' => self::nameRules(required: true, min: 2, max: 150),
            'phone' => self::phoneRules(required: true),
            'email' => self::emailRules(required: true, checkDns: true, max: 255),
            'assignment_type' => ['required', 'string', Rule::in(ProfileEnquiry::ASSIGNMENT_TYPES)],
            'preferred_date' => ['nullable', 'date', 'after_or_equal:today'],
            'location' => ['required', 'string', 'min:2', 'max:200'],
            'brief' => ['required', 'string', 'min:10', 'max:3000'],
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
            'preferred_date.after_or_equal' => 'Preferred date cannot be in the past.',
        ]);
    }
}
