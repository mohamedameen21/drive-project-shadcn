<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFileRequest;
use App\Http\Requests\StoreFolderRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class FileController extends Controller
{
    public function myFiles(?string $folderPath = null)
    {
        // because the same file name might be in different folders
        // so, only the path will be unique not name
        if ($folderPath) {
            $folderPath = File::query()
                ->where('path', $folderPath)
                ->where('created_by', Auth::id())
                ->firstOrFail();
        }

        if (! $folderPath) {
            $folderPath = File::getDefaultRoot(Auth::id());
        }

        $files = File::query()
            ->where('parent_id', $folderPath->id)
            ->where('created_by', Auth::id())
            ->orderBy('is_folder', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $files = FileResource::collection($files);

        // destructing the ancestors to array, then appending the current folder at the end
        $ancestors = FileResource::collection([...$folderPath->ancestors, $folderPath]);

        $folderPath = new FileResource($folderPath);

        return Inertia::render('MyFiles', compact('files', 'folderPath', 'ancestors'));
    }

    public function createFolder(StoreFolderRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->validated();
            $parentFolder = $request->parent ?? File::getDefaultRoot(Auth::id());

            $file = new File();
            $file->is_folder = 1;
            $file->name = $data['name'];

            $parentFolder->appendNode($file);

            //          Note: The path of this folder will be set in the boot method of the File model
            DB::commit();

            return back()->with('message', 'Folder created successfully');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), $e->getTrace());

            return back()->with('message', 'Failed to create folder');
        }
    }

    public function store(StoreFileRequest $request)
    {
        $data = $request->validated();
        $parent = $request->parent ?? File::getDefaultRoot(Auth::id());
        $user = Auth::user();
        $fileTree = $request->file_tree;

        if (! empty($fileTree)) {
            $this->saveFileTree($fileTree, $parent, $user);
        } else {
            foreach ($data['files'] as $file) {
                $path = $file->store('/files/'.$user->id);
                $fileNode = $this->createFile($file, $path);
                $parent->appendNode($fileNode);
            }
        }
    }

    private function saveFileTree($fileTree, $parent, $user)
    {
        foreach ($fileTree as $name => $file) {
            // if this is an array then this is a folder so, creating folder
            if (is_array($file)) {
                $folder = new File();
                $folder->is_folder = 1;
                $folder->name = $name;

                $parent->appendNode($folder);
                $this->saveFileTree($file, $folder, $user);
            } else {
                // it is a file
                $path = $file->store('/files/'.$user->id);
                $fileNode = $this->createFile($file, $path);
                $parent->appendNode($fileNode);
            }
        }
    }

    private function createFile($file, $path)
    {
        $model = new File();
        $model->storage_path = $path;
        $model->name = $file->getClientOriginalName();
        $model->is_folder = false;
        $model->mime = $file->getMimeType();
        $model->size = $file->getSize();

        return $model;
    }
}
