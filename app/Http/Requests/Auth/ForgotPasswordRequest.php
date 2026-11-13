<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Concerns\CommonRules;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates forgot-password link requests.
 */
class ForgotPasswordRequest extends FormRequest
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
     * Prepare incoming forgot-password data for validation.
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
            'email' => self::emailRules(required: true, checkDns: false, max: 120),
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
