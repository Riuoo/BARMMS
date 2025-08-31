<?php

namespace App\Http\Requests;

class LoginRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                'max:255',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:255'
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Email address is too long.',
            'email.regex' => 'Please enter a valid email address format.',
            'password.required' => 'Please enter your password.',
            'password.string' => 'Password must be a string.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.max' => 'Password is too long.',
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
        ];
    }
}
