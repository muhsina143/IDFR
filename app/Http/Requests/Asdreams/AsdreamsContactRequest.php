<?php

namespace App\Http\Requests\Asdreams;

use App\Http\Requests\Concerns\CommonRules;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates ASDreamStudio general contact and academy brand contact enquiries.
 */
class AsdreamsContactRequest extends FormRequest
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
     * Prepare incoming ASDreamStudio contact data for validation.
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
        $isBrandEnquiry = $this->filled('brand');

        return [
            'brand' => ['nullable', 'string', 'max:40'],
            'name' => self::nameRules(required: true, min: 2, max: 60),
            'phone' => self::phoneRules(required: true),
            'email' => self::emailRules(required: false, checkDns: true, max: 120),
            'subject' => ['nullable', 'string', 'min:3', 'max:150'],
            'message' => $isBrandEnquiry
                ? ['nullable', 'string', 'min:10', 'max:2000']
                : ['required', 'string', 'min:10', 'max:2000'],
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
