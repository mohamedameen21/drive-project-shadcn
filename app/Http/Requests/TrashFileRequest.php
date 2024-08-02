<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TrashFileRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'all' => ['sometimes', 'nullable', 'boolean'],
            'ids' => ['required_unless:all,true', 'array'],
            'ids.*' => [
                'required_unless:all,true', 'integer', Rule::exists('files', 'id')
                    ->where(function ($query) {
                        $query->where('created_by', $this->user()->id);
                    }),
            ],
        ];
    }
}
