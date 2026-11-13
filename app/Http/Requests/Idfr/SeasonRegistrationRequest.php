<?php

namespace App\Http\Requests\Idfr;

use App\Http\Requests\Concerns\CommonRules;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates IDFR Season Model Registration submissions including conditional guardian fields.
 */
class SeasonRegistrationRequest extends FormRequest
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
     * Prepare incoming season registration data for validation.
     */
    protected function prepareForValidation(): void
    {
        $aliases = [];

        if ($this->filled('name') && ! $this->filled('full_name')) {
            $aliases['full_name'] = $this->input('name');
        }

        if ($this->filled('phone') && ! $this->filled('whatsapp_number')) {
            $aliases['whatsapp_number'] = $this->input('phone');
        }

        if (! $this->filled('age')) {
            $aliases['age'] = 21;
        }

        if (! $this->filled('gender')) {
            $aliases['gender'] = 'female';
        }

        if (! $this->filled('category')) {
            $aliases['category'] = 'adult';
        }

        if (! $this->filled('citizenship')) {
            $aliases['citizenship'] = 'Indian';
        }

        if (! empty($aliases)) {
            $this->merge($aliases);
        }

        $this->normalizeInputs(
            phoneFields: ['whatsapp_number', 'parent_contact'],
            emailFields: ['email']
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $category = strtolower((string) $this->input('category', ''));
        $ageValue = $this->input('age');
        $isMinor = $category === 'kids' || (is_numeric($ageValue) && (int) $ageValue > 0 && (int) $ageValue < 18);

        return [
            'season_id' => ['required', 'exists:seasons,id'],
            'season_number' => ['nullable', 'string', 'max:40'],
            'full_name' => self::nameRules(required: true, min: 2, max: 150),
            'whatsapp_number' => self::phoneRules(required: true),
            'email' => self::emailRules(required: false, checkDns: true, max: 150),
            'age' => ['required', 'integer', 'min:3', 'max:99'],
            'gender' => ['required', 'string', 'in:male,female,other,Male,Female,Other'],
            'category' => ['required', 'string', 'in:adult,kids,Adult,Kids'],
            'citizenship' => ['required', 'string', 'min:2', 'max:100'],
            'parent_name' => $isMinor ? self::nameRules(required: true, min: 2, max: 150) : ['nullable', 'string', 'max:150'],
            'parent_contact' => $isMinor ? self::phoneRules(required: true) : ['nullable', 'string', 'max:50'],
            'parent_relationship' => ['nullable', 'string', 'max:50'],
            'additional_info' => ['nullable', 'string', 'max:2000'],
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
            'parent_name.required' => 'Parent or guardian full name is required for minor/kids applicants.',
            'parent_contact.required' => 'Parent or guardian contact number is required for minor/kids applicants.',
        ]);
    }
}
