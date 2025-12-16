<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccomplishedProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|string|in:project,activity',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'location' => 'nullable|string',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'completion_date' => 'required|date|after_or_equal:start_date',
            'beneficiaries' => 'nullable|string',
            'impact' => 'nullable|string',
            'funding_source' => 'nullable|string',
            'implementing_agency' => 'nullable|string',
            'is_featured' => 'boolean',
            'audience_scope' => 'nullable|string|in:all,purok',
            'audience_purok' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_image' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Please choose if this is a project or activity.',
            'type.in' => 'Type must be either project or activity.',
            'title.required' => 'The title is required.',
            'description.required' => 'The description is required.',
            'category.required' => 'Please select a category.',
            'start_date.required' => 'The start date is required.',
            'completion_date.required' => 'The completion date is required.',
            'completion_date.after_or_equal' => 'The completion date must be on or after the start date.',
            'budget.numeric' => 'The budget must be a valid number.',
            'budget.min' => 'The budget cannot be negative.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif.',
            'image.max' => 'The image may not be greater than 2MB.',
            'audience_scope.in' => 'Audience scope must be All Residents or Specific Purok.',
            'audience_purok.max' => 'The selected Purok value is too long.',
        ];
    }
} 