<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccomplishedProjectRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'location' => 'nullable|string',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'completion_date' => 'required|date|after:start_date',
            'beneficiaries' => 'nullable|string',
            'impact' => 'nullable|string',
            'funding_source' => 'nullable|string',
            'implementing_agency' => 'nullable|string',
            'is_featured' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_image' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The project title is required.',
            'description.required' => 'The project description is required.',
            'category.required' => 'Please select a project category.',
            'start_date.required' => 'The start date is required.',
            'completion_date.required' => 'The completion date is required.',
            'completion_date.after' => 'The completion date must be after the start date.',
            'budget.numeric' => 'The budget must be a valid number.',
            'budget.min' => 'The budget cannot be negative.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif.',
            'image.max' => 'The image may not be greater than 2MB.',
        ];
    }
} 