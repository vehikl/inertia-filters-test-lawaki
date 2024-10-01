<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShowDashboardRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'brand' => ['sometimes', 'string', 'nullable'],
            'company' => ['sometimes', 'string', 'nullable'],
            'department' => ['sometimes', 'array', 'nullable'],
            'department.*' => ['sometimes', 'string', 'nullable'],
            'page' => ['sometimes', 'integer', 'nullable', 'min:1'],
            'limit' => ['sometimes', 'integer', 'nullable', 'min:1'],
            'employee_id' => ['sometimes', 'integer', 'nullable'],
        ];
    }
}
