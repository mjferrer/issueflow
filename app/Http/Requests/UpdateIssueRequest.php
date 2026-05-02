<?php

namespace App\Http\Requests;

use App\Enums\Category;
use App\Enums\Priority;
use App\Enums\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateIssueRequest extends FormRequest
{
    public function authorize(): bool
    {
        $issue = $this->route('issue');
        return $this->user()->can('update', $issue);
    }

    public function rules(): array
    {
        $user = $this->user();

        $rules = [
            'title'       => ['sometimes', 'string', 'min:5', 'max:255'],
            'description' => ['sometimes', 'string', 'min:20', 'max:10000'],
            'priority'    => ['sometimes', 'string', Rule::enum(Priority::class)],
            'category'    => ['sometimes', 'string', Rule::enum(Category::class)],
            'note'        => ['nullable', 'string', 'max:1000'],
        ];

        // Only elevated users can change status
        if ($user->hasElevatedAccess()) {
            $rules['status'] = ['sometimes', 'string', Rule::enum(Status::class)];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'title.min'            => 'The title must be at least 5 characters.',
            'description.min'      => 'The description must be at least 20 characters.',
            'priority.Illuminate\Validation\Rules\Enum' => 'Invalid priority selected.',
            'status.Illuminate\Validation\Rules\Enum'   => 'Invalid status selected.',
            'category.Illuminate\Validation\Rules\Enum' => 'Invalid category selected.',
        ];
    }
}