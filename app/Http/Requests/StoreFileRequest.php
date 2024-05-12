<?php

namespace App\Http\Requests;

use App\Models\File;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

class StoreFileRequest extends ParentIdBaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'files' => [
                'required',
                'array',
            ],
            'files.*' => [
                'required',
                'file',
                function ($attribute, $value, $fail) {
                    $fileExists = File::query()->where('name', $value->getClientOriginalName())
                        ->where('parent_id', $this->parent_id ?? File::getDefaultRoot(Auth::id()))
                        ->where('created_by', Auth::id())
                        ->whereNull('deleted_at')
                        ->exists();

                    if ($fileExists) {
                        $fail('File "'.$value->getClientOriginalName().'" already exists');
                    }
                },
            ],
            'folder_name' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    if ($this->folder_name) {
                        $folderExists = File::query()->where('name', $value)
                            ->where('parent_id', $this->parent_id)
                            ->where('created_by', Auth::id())
                            ->whereNull('deleted_at')
                            ->exists();

                        if ($folderExists) {
                            $fail('Folder "'.$value.'" already exists');
                        }
                    }
                },
            ],
        ]);
    }

    protected function prepareForValidation()
    {
        $paths = array_filter($this->relative_paths ?? [], fn ($path) => $path != null);

        $this->merge([
            'file_paths' => $paths,
            'folder_name' => $this->detectFolderName($paths), // taking this root folder name form the paths to check the uniqueness in the current directory
        ]);
    }

    private function detectFolderName($paths)
    {
        if (! $paths) {
            return null;
        }

        $parts = explode('/', $paths[0]);

        return $parts[0];
    }

    protected function passedValidation()
    {
        $data = $this->validated();

        $this->replace([
            'file_tree' => $this->buildFileTree($this->file_paths, $data['files']),
        ]);
    }

    private function buildFileTree($filePaths, $files)
    {
        $filePaths = array_filter($filePaths, fn ($path) => $path != null);

        $tree = [];

        foreach ($filePaths as $index => $filePath) {
            $parts = explode('/', $filePath);

            $currentNode = &$tree;

            foreach ($parts as $i => $part) {
                if (! isset($currentNode[$part])) {
                    $currentNode[$part] = [];
                }

                if ($i === count($parts) - 1) {
                    $currentNode[$part] = $files[$index];
                } else {
                    $currentNode = &$currentNode[$part];
                }
            }
        }

        return $tree;
    }
}
