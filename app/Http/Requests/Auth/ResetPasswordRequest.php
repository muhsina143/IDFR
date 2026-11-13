<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Concerns\CommonRules;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates password reset form submissions.
 */
class ResetPasswordRequest extends FormRequest
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
     * Prepare incoming password reset data for validation.
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
            'token' => ['required', 'string'],
            'email' => self::emailRules(required: true, checkDns: false, max: 120),
            'password' => self::passwordRules(confirmed: true, min: 8, max: 64),
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
