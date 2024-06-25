<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class FilesActionRequest extends ParentIdBaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'all' => ['sometimes', 'nullable', 'boolean'],
            'ids' => ['required_unless:all,true', 'array'],
            'ids.*' => [
                'required_unless:all,true', 'integer', Rule::exists('files', 'id')->where(function ($query) {
                    $query->where('created_by', $this->user()->id);
                }),
            ],
        ]);
    }
}
