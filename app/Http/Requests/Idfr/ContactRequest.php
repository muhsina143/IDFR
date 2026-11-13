<?php

namespace App\Http\Requests\Idfr;

use App\Http\Requests\Concerns\CommonRules;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates public IDFR contact page enquiries.
 */
class ContactRequest extends FormRequest
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
     * Prepare incoming contact data for validation.
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
            'subject' => ['nullable', 'string', 'min:3', 'max:150'],
            'message' => ['nullable', 'string', 'min:10', 'max:2000'],
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
