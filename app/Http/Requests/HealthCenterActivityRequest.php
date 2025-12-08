<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HealthCenterActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'activity_name' => 'required|string|max:255',
            'activity_type' => 'required|string|in:Vaccination,Health Check-up,Health Education,Medical Consultation,Emergency Response,Preventive Care,Maternal Care,Child Care,Other',
            'activity_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'location' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'objectives' => 'nullable|string|max:2000',
            'target_participants' => 'nullable|integer|min:1',
            'organizer' => 'nullable|string|max:255',
            'materials_needed' => 'nullable|string|max:1000',
            'budget' => 'nullable|numeric|min:0',
            'status' => 'required|string|in:Planned,Ongoing,Completed,Cancelled',
            'is_featured' => 'nullable|boolean',
        ];

        // For update, add additional fields
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['actual_participants'] = 'nullable|integer|min:0';
            $rules['outcomes'] = 'nullable|string|max:2000';
            $rules['challenges'] = 'nullable|string|max:2000';
            $rules['recommendations'] = 'nullable|string|max:2000';
            $rules['remove_image'] = 'nullable|boolean';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'activity_name.required' => 'The activity name is required.',
            'activity_type.required' => 'Please select an activity type.',
            'activity_type.in' => 'The selected activity type is invalid.',
            'activity_date.required' => 'The activity date is required.',
            'activity_date.date' => 'The activity date must be a valid date.',
            'start_time.date_format' => 'The start time must be in the format HH:mm.',
            'end_time.date_format' => 'The end time must be in the format HH:mm.',
            'end_time.after' => 'The end time must be after the start time.',
            'location.required' => 'The location is required.',
            'description.required' => 'The description is required.',
            'description.max' => 'The description may not be greater than 2000 characters.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif.',
            'image.max' => 'The image may not be greater than 2MB.',
            'target_participants.integer' => 'The target participants must be a number.',
            'target_participants.min' => 'The target participants must be at least 1.',
            'actual_participants.integer' => 'The actual participants must be a number.',
            'actual_participants.min' => 'The actual participants cannot be negative.',
            'budget.numeric' => 'The budget must be a valid number.',
            'budget.min' => 'The budget cannot be negative.',
            'status.required' => 'The status is required.',
            'status.in' => 'The selected status is invalid.',
        ];
    }
}
