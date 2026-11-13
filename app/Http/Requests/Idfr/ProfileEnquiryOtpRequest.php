<?php

namespace App\Http\Requests\Idfr;

use App\Http\Requests\Concerns\CommonRules;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates OTP send requests on profile booking enquiries.
 */
class ProfileEnquiryOtpRequest extends FormRequest
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
     * Prepare incoming OTP request data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->normalizeInputs(emailFields: ['email']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => self::emailRules(required: true, checkDns: true, max: 120),
            'name' => self::nameRules(required: false, min: 2, max: 80),
        ];
    }

    /**
     * Get custom validation error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return $this->sharedValidationMessages();
    }
}
