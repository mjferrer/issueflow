<?php

namespace App\Http\Requests;

use App\Enums\Category;
use App\Enums\Priority;
use App\Enums\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIssueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Issue::class);
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'min:5', 'max:255'],
            'description' => ['required', 'string', 'min:20', 'max:10000'],
            'priority'    => ['required', 'string', Rule::enum(Priority::class)],
            'category'    => ['required', 'string', Rule::enum(Category::class)],
            'status'      => ['sometimes', 'string', Rule::enum(Status::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'       => 'A title is required for the issue.',
            'title.min'            => 'The title must be at least 5 characters.',
            'description.required' => 'Please provide a detailed description of the issue.',
            'description.min'      => 'The description must be at least 20 characters to be useful.',
            'priority.required'    => 'Please select a priority level.',
            'priority.Illuminate\Validation\Rules\Enum' => 'Invalid priority. Choose: low, medium, high, or critical.',
            'category.required'    => 'Please select a category.',
            'category.Illuminate\Validation\Rules\Enum' => 'Invalid category selected.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Default status to open if not provided
        if (!$this->has('status')) {
            $this->merge(['status' => Status::Open->value]);
        }
    }
}