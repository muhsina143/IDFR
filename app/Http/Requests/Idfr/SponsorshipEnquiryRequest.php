<?php

namespace App\Http\Requests\Idfr;

use App\Http\Requests\Concerns\CommonRules;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates Sponsorship & Collaboration modal enquiries.
 */
class SponsorshipEnquiryRequest extends FormRequest
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
     * Prepare incoming sponsorship enquiry data for validation.
     */
    protected function prepareForValidation(): void
    {
        if (! $this->filled('sponsorship_type') && $this->filled('type')) {
            $this->merge(['sponsorship_type' => $this->input('type')]);
        }

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
            'name' => self::nameRules(required: true, min: 2, max: 150, allowCompanyChars: true),
            'phone' => self::phoneRules(required: true),
            'email' => self::emailRules(required: true, checkDns: true, max: 150),
            'sponsorship_type' => ['required', 'string', 'max:120'],
            'message' => ['nullable', 'string', 'max:2000'],
            'season_id' => ['nullable', 'integer', 'exists:seasons,id'],
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
            'name.required' => 'Please provide your full name or company name.',
            'email.required' => 'A valid email address is required so we can reach you.',
            'phone.required' => 'Please provide a valid phone number with country code.',
            'sponsorship_type.required' => 'Please select your preferred type of sponsorship.',
        ]);
    }
}
