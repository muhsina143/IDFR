<?php

namespace App\Http\Requests\Asdreams;

use App\Http\Requests\Concerns\CommonRules;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates ASDreamStudio offer details enquiry submissions.
 */
class AsdreamsOfferEnquiryRequest extends FormRequest
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
     * Prepare incoming offer enquiry data for validation.
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
            'name' => self::nameRules(required: true, min: 2, max: 60),
            'phone' => self::phoneRules(required: true),
            'email' => self::emailRules(required: false, checkDns: true, max: 120),
            'date' => ['nullable', 'date', 'after_or_equal:today'],
            'offer' => ['required_without_all:offer_id,banner_id', 'nullable', 'string', 'max:150'],
            'offer_id' => ['nullable', 'integer'],
            'banner_id' => ['nullable', 'integer'],
            'message' => ['nullable', 'string', 'max:1000'],
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
            'date.after_or_equal' => 'Preferred date cannot be in the past.',
        ]);
    }
}
