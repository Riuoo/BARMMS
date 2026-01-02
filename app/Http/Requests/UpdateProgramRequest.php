<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $programId = $this->route('program');
        
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('programs', 'name')->ignore($programId),
            ],
            'type' => 'required|string|in:employment,health,education,social,safety,custom',
            'description' => 'nullable|string',
            'criteria' => 'required|array',
            'criteria.operator' => 'required|string|in:AND,OR',
            'criteria.conditions' => 'required|array|min:1',
            'target_puroks' => 'nullable|array',
            'target_puroks.*' => 'string',
            'is_active' => 'nullable|boolean',
            'priority' => 'nullable|integer|min:0|max:10',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The program name is required.',
            'name.unique' => 'A program with this name already exists.',
            'type.required' => 'Please select a program type.',
            'type.in' => 'The selected program type is invalid.',
            'criteria.required' => 'Program criteria are required.',
            'criteria.array' => 'Criteria must be a valid structure.',
            'criteria.operator.required' => 'Criteria operator (AND/OR) is required.',
            'criteria.operator.in' => 'Criteria operator must be either AND or OR.',
            'criteria.conditions.required' => 'At least one condition is required.',
            'criteria.conditions.min' => 'At least one condition is required.',
            'priority.integer' => 'Priority must be a number.',
            'priority.min' => 'Priority cannot be negative.',
            'priority.max' => 'Priority cannot exceed 10.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->has('criteria')) {
                $this->validateCriteriaStructure($validator, $this->criteria);
            }
        });
    }

    private function validateCriteriaStructure($validator, $criteria, $path = 'criteria'): void
    {
        if (!is_array($criteria)) {
            return;
        }

        if (isset($criteria['field'])) {
            // Simple condition - must have operator and value
            if (!isset($criteria['operator']) || !isset($criteria['value'])) {
                $validator->errors()->add($path, 'Each condition must have a field, operator, and value.');
            }
        } elseif (isset($criteria['operator'])) {
            // Nested group - must have conditions array
            if (!isset($criteria['conditions']) || !is_array($criteria['conditions']) || empty($criteria['conditions'])) {
                $validator->errors()->add($path, 'Each group must have at least one condition.');
            } else {
                // Recursively validate nested conditions
                foreach ($criteria['conditions'] as $index => $condition) {
                    $this->validateCriteriaStructure($validator, $condition, $path . '.conditions.' . $index);
                }
            }
        }
    }
}

