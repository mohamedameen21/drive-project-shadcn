<?php

namespace App\Http\Requests;

use App\Models\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreFolderRequest extends ParentIdBaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'name' => ['required',
                Rule::unique(File::class, 'name')
                    ->where('parent_id', $this->parent_id ?? File::getDefaultRoot(Auth::id()))
                    ->whereNull('deleted_at'),
            ],
        ]);
    }

    public function messages()
    {
        return [
            'name.unique' => 'Folder ":input" already exits',
        ];
    }
}
