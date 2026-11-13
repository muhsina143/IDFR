<?php

namespace App\Http\Requests\Concerns;

/**
 * Shared validation rules, regex constants, messages, and input normalization helpers
 * reused across all application FormRequest classes.
 */
trait CommonRules
{
    public const string INTERNATIONAL_PHONE_REGEX = '/^\+?[1-9]\d{6,14}$/';

    public const string NAME_REGEX = '/^[A-Za-z\s.]+$/u';

    public const string ALPHA_REGEX = '/^[A-Za-z\s]+$/u';

    public const string ALPHANUM_REGEX = '/^[A-Za-z0-9\s\-_.,]+$/u';

    public const string PASSWORD_REGEX = '/^(?=.*[A-Za-z])(?=.*\d).+$/';

    /**
     * Build standard E.164 international phone validation rules.
     *
     * @return list<string>
     */
    protected static function phoneRules(bool $required = true): array
    {
        return [
            $required ? 'required' : 'nullable',
            'string',
            'max:25',
            'regex:'.self::INTERNATIONAL_PHONE_REGEX,
        ];
    }

    /**
     * Build standard email validation rules.
     * Enforces RFC+DNS validation in non-testing environments and RFC in automated tests.
     *
     * @return list<string>
     */
    protected static function emailRules(bool $required = true, bool $checkDns = true, int $max = 120): array
    {
        $emailRule = ($checkDns && ! app()->environment('testing')) ? 'email:rfc,dns' : 'email:rfc';

        return [
            $required ? 'required' : 'nullable',
            'string',
            $emailRule,
            'max:'.$max,
        ];
    }

    /**
     * Build standard name validation rules.
     *
     * @return list<string>
     */
    protected static function nameRules(bool $required = true, int $min = 2, int $max = 60, bool $allowCompanyChars = false): array
    {
        $rules = [
            $required ? 'required' : 'nullable',
            'string',
            'min:'.$min,
            'max:'.$max,
        ];

        if (! $allowCompanyChars) {
            $rules[] = 'regex:'.self::NAME_REGEX;
        }

        return $rules;
    }

    /**
     * Build standard password creation/reset validation rules.
     *
     * @return list<string>
     */
    protected static function passwordRules(bool $confirmed = true, int $min = 8, int $max = 64): array
    {
        $rules = [
            'required',
            'string',
            'min:'.$min,
            'max:'.$max,
            'regex:'.self::PASSWORD_REGEX,
        ];

        if ($confirmed) {
            $rules[] = 'confirmed';
        }

        return $rules;
    }

    /**
     * Normalize incoming string inputs before validation runs.
     *
     * @param  list<string>  $phoneFields
     * @param  list<string>  $emailFields
     * @param  list<string>  $skipTrimFields
     */
    protected function normalizeInputs(
        array $phoneFields = [],
        array $emailFields = ['email'],
        array $skipTrimFields = ['password', 'password_confirmation', 'current_password']
    ): void {
        $input = $this->all();
        $normalized = [];

        foreach ($input as $key => $value) {
            if (! is_string($value)) {
                continue;
            }

            if (in_array($key, $skipTrimFields, true)) {
                continue;
            }

            $trimmed = trim($value);

            if (in_array($key, $emailFields, true)) {
                $normalized[$key] = mb_strtolower($trimmed);
            } elseif (in_array($key, $phoneFields, true)) {
                $normalized[$key] = preg_replace('/[\s\-()]+/', '', $trimmed) ?? $trimmed;
            } else {
                $normalized[$key] = $trimmed;
            }
        }

        if (! empty($normalized)) {
            $this->merge($normalized);
        }
    }

    /**
     * Shared human-friendly validation messages across forms.
     *
     * @return array<string, string>
     */
    protected function sharedValidationMessages(): array
    {
        return [
            'name.regex' => 'Name may only contain letters, spaces, and dots.',
            'full_name.regex' => 'Full name may only contain letters, spaces, and dots.',
            'parent_name.regex' => 'Guardian name may only contain letters, spaces, and dots.',
            'phone.regex' => 'Please enter a valid phone number with country code (e.g. +971 54 178 6786 or +91 98765 43210).',
            'whatsapp_number.regex' => 'Please enter a valid phone number with country code (e.g. +971 54 178 6786 or +91 98765 43210).',
            'parent_contact.regex' => 'Please enter a valid phone number with country code (e.g. +971 54 178 6786 or +91 98765 43210).',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.regex' => 'Password must contain at least one letter and one number.',
            'password.confirmed' => 'Passwords do not match.',
            'terms.accepted' => 'You must accept the terms and privacy policy.',
            'email.email' => 'Please enter a valid email address.',
        ];
    }
}

