<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\Concerns\CommonRules;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates model profile creation and update form submissions.
 */
class SaveModelProfileRequest extends FormRequest
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
     * Prepare incoming model profile data for validation.
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
            'name' => self::nameRules(required: true, min: 2, max: 150),
            'gender' => ['required', 'string', 'in:male,female,other,Male,Female,Other'],
            'place' => ['required', 'string', 'min:2', 'max:100'],
            'age' => ['required', 'integer', 'min:14', 'max:70'],
            'height' => ['required', 'numeric', 'min:100', 'max:230'],
            'category' => ['required', 'string', 'in:experienced,new-face,Experienced,New Face'],
            'experience' => ['required', 'numeric', 'min:0', 'max:50'],
            'skills' => ['required', 'string', 'min:2', 'max:500'],
            'modelType' => ['required', 'array', 'min:1'],
            'modelType.*' => ['string', 'max:60'],
            'phone' => self::phoneRules(required: false),
            'email' => self::emailRules(required: false, checkDns: true, max: 150),
            'about' => ['nullable', 'string', 'max:2000'],
            'bust' => ['nullable', 'string', 'max:30'],
            'waist' => ['nullable', 'string', 'max:30'],
            'hips' => ['nullable', 'string', 'max:30'],
            'shoe' => ['nullable', 'string', 'max:30'],
            'outstation' => ['required', 'in:yes,no,Yes,No'],
            'international' => ['required', 'in:yes,no,Yes,No'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'portfolio_image_1' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'portfolio_image_2' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'portfolio_images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
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
            'modelType.required' => 'Please select at least one model specialization.',
            'modelType.min' => 'Please select at least one model specialization.',
            'category.required' => 'Please select your experience category (Experienced or New Face).',
            'place.required' => 'Please select your city or location.',
        ]);
    }
}
