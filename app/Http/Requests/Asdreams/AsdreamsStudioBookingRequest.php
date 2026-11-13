<?php

namespace App\Http\Requests\Asdreams;

use App\Http\Requests\Concerns\CommonRules;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates ASDreamStudio Studio Rental Booking modal submissions.
 */
class AsdreamsStudioBookingRequest extends FormRequest
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
     * Prepare incoming studio booking data for validation.
     */
    protected function prepareForValidation(): void
    {
        $aliases = [];

        if ($this->filled('production_type') && ! $this->has('purpose')) {
            $aliases['purpose'] = $this->input('production_type');
        }

        $startValue = $this->input('start') ?: $this->input('hire_date');

        if ($this->filled('hire_date') && ! $this->has('start')) {
            $aliases['start'] = $this->input('hire_date');
        }

        if ($startValue && ! $this->has('end')) {
            $aliases['end'] = $startValue;
        }

        if ($this->filled('duration') && ! $this->has('days')) {
            $aliases['days'] = 1;
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
            'name' => self::nameRules(required: true, min: 2, max: 100, allowCompanyChars: true),
            'phone' => self::phoneRules(required: true),
            'email' => self::emailRules(required: true, checkDns: true, max: 120),
            'days' => ['required', 'integer', 'min:1', 'max:60'],
            'purpose' => ['required', 'string', 'min:2', 'max:150'],
            'start' => ['required', 'date', 'after_or_equal:today'],
            'end' => ['required', 'date', 'after_or_equal:start'],
            'notes' => ['nullable', 'string', 'max:1000'],
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
            'start.after_or_equal' => 'Start date cannot be in the past.',
            'end.after_or_equal' => 'End date must be on or after the start date.',
        ]);
    }
}
