<?php

namespace App\Http\Requests\Asdreams;

use App\Http\Requests\Concerns\CommonRules;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates ASDreamStudio Book a Shoot modal submissions.
 */
class AsdreamsShootBookingRequest extends FormRequest
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
     * Prepare incoming shoot booking data for validation.
     */
    protected function prepareForValidation(): void
    {
        $aliases = [];

        if (! $this->filled('shoot_date') && $this->filled('date')) {
            $aliases['shoot_date'] = $this->input('date');
        }

        if (! $this->filled('shoot_type')) {
            if ($this->filled('subject')) {
                $aliases['shoot_type'] = $this->input('subject');
            } elseif ($this->filled('service_type')) {
                $aliases['shoot_type'] = $this->input('service_type');
            }
        }

        if (! $this->filled('notes') && $this->filled('message')) {
            $aliases['notes'] = $this->input('message');
        }

        if (! empty($aliases)) {
            $this->merge($aliases);
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
            'name' => self::nameRules(required: true, min: 2, max: 60),
            'phone' => self::phoneRules(required: true),
            'email' => self::emailRules(required: true, checkDns: true, max: 120),
            'shoot_date' => ['required', 'date', 'after_or_equal:today'],
            'shoot_type' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string', 'max:1500'],
            'date' => ['nullable', 'date'],
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['nullable', 'string', 'max:1500'],
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
            'shoot_date.after_or_equal' => 'Shoot date cannot be in the past.',
        ]);
    }
}
