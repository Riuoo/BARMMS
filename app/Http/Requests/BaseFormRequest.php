<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

abstract class BaseFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    abstract public function rules(): array;

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'required' => 'The :attribute field is required.',
            'string' => 'The :attribute must be a string.',
            'email' => 'The :attribute must be a valid email address.',
            'max' => 'The :attribute may not be greater than :max characters.',
            'min' => 'The :attribute must be at least :min characters.',
            'numeric' => 'The :attribute must be a number.',
            'date' => 'The :attribute is not a valid date.',
            'image' => 'The :attribute must be an image.',
            'mimes' => 'The :attribute must be a file of type: :values.',
            'unique' => 'The :attribute has already been taken.',
            'exists' => 'The selected :attribute is invalid.',
            'confirmed' => 'The :attribute confirmation does not match.',
            'regex' => 'The :attribute format is invalid.',
            'alpha' => 'The :attribute may only contain letters.',
            'alpha_num' => 'The :attribute may only contain letters and numbers.',
            'alpha_dash' => 'The :attribute may only contain letters, numbers, dashes and underscores.',
            'url' => 'The :attribute format is invalid.',
            'ip' => 'The :attribute must be a valid IP address.',
            'integer' => 'The :attribute must be an integer.',
            'boolean' => 'The :attribute field must be true or false.',
            'array' => 'The :attribute must be an array.',
            'file' => 'The :attribute must be a file.',
            'between' => 'The :attribute must be between :min and :max.',
            'digits' => 'The :attribute must be :digits digits.',
            'digits_between' => 'The :attribute must be between :min and :max digits.',
            'after' => 'The :attribute must be a date after :date.',
            'before' => 'The :attribute must be a date before :date.',
            'after_or_equal' => 'The :attribute must be a date after or equal to :date.',
            'before_or_equal' => 'The :attribute must be a date before or equal to :date.',
            'different' => 'The :attribute and :other must be different.',
            'same' => 'The :attribute and :other must match.',
            'size' => 'The :attribute must be :size.',
            'distinct' => 'The :attribute field has a duplicate value.',
            'filled' => 'The :attribute field must have a value.',
            'present' => 'The :attribute field must be present.',
            'prohibited' => 'The :attribute field is prohibited.',
            'prohibited_if' => 'The :attribute field is prohibited when :other is :value.',
            'prohibited_unless' => 'The :attribute field is prohibited unless :other is in :values.',
            'required_if' => 'The :attribute field is required when :other is :value.',
            'required_unless' => 'The :attribute field is required unless :other is in :values.',
            'required_with' => 'The :attribute field is required when :values is present.',
            'required_with_all' => 'The :attribute field is required when :values are present.',
            'required_without' => 'The :attribute field is required when :values is not present.',
            'required_without_all' => 'The :attribute field is required when none of :values are present.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'email' => 'email address',
            'password' => 'password',
            'password_confirmation' => 'password confirmation',
            'current_password' => 'current password',
            'new_password' => 'new password',
            'first_name' => 'first name',
            'last_name' => 'last name',
            'phone_number' => 'phone number',
            'mobile_number' => 'mobile number',
            'date_of_birth' => 'date of birth',
            'place_of_birth' => 'place of birth',
            'civil_status' => 'civil status',
            'nationality' => 'nationality',
            'religion' => 'religion',
            'occupation' => 'occupation',
            'address' => 'address',
            'barangay' => 'barangay',
            'city' => 'city',
            'province' => 'province',
            'postal_code' => 'postal code',
            'emergency_contact' => 'emergency contact',
            'emergency_contact_number' => 'emergency contact number',
            'relationship' => 'relationship',
            'blood_type' => 'blood type',
            'allergies' => 'allergies',
            'medical_conditions' => 'medical conditions',
            'medications' => 'medications',
            'vaccination_history' => 'vaccination history',
            'last_medical_checkup' => 'last medical checkup',
            'insurance_provider' => 'insurance provider',
            'policy_number' => 'policy number',
            'group_number' => 'group number',
            'preferred_language' => 'preferred language',
            'communication_preference' => 'communication preference',
            'consent_for_communications' => 'consent for communications',
            'consent_for_data_sharing' => 'consent for data sharing',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator): void
    {
        // Log validation failures for security monitoring
        $this->logValidationFailure($validator);

        if ($this->expectsJson()) {
            throw new HttpResponseException(
                response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => $validator->errors(),
                ], 422)
            );
        }

        parent::failedValidation($validator);
    }

    /**
     * Log validation failures for security monitoring.
     */
    protected function logValidationFailure(Validator $validator): void
    {
        $errors = $validator->errors()->toArray();
        $input = $this->except(['password', 'password_confirmation', 'current_password', 'new_password']);
        
        Log::info('Form validation failed', [
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'user_id' => session('user_id'),
            'errors' => $errors,
            'input' => $input,
            'timestamp' => now()
        ]);
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->sanitizeInput();
    }

    /**
     * Sanitize input data before validation.
     */
    protected function sanitizeInput(): void
    {
        $input = $this->all();
        
        foreach ($input as $key => $value) {
            if (is_string($value)) {
                // Remove null bytes
                $value = str_replace("\0", '', $value);
                
                // Trim whitespace
                $value = trim($value);
                
                // Update the input
                $input[$key] = $value;
            }
        }
        
        $this->replace($input);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        $identifier = request()->ip();
        
        if (session()->has('user_id')) {
            $identifier .= ':' . session('user_id');
        }
        
        $routeName = request()->route() ? request()->route()->getName() : 'unknown';
        $identifier .= ':' . $routeName;
        
        return sha1($identifier);
    }
}
