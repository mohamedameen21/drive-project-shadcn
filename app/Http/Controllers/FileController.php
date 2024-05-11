<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFileRequest;
use App\Http\Requests\StoreFolderRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class FileController extends Controller
{
    public function myFiles(?string $folder = null)
    {
        if ($folder) {
            $folder = File::query()
                ->where('name', $folder)
                ->where('created_by', Auth::id())
                ->firstOrFail();
        }
        $defaultRootFolder = File::getDefaultRoot(Auth::id());

        if (! $folder) {
            $folder = $defaultRootFolder;
        }

        $files = File::query()
            ->where('parent_id', $folder->id)
            ->where('created_by', Auth::id())
            ->orderBy('is_folder', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $files = FileResource::collection($files);

        // destructing the ancestors to array, then appending the current folder at the end
        $ancestors = FileResource::collection([...$folder->ancestors, $folder]);

        $folder = new FileResource($folder);

        return Inertia::render('MyFiles', compact('files', 'folder', 'ancestors'));
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

            //            Note: The path of this folder will be set in the boot method of the File model

            DB::commit();

            return back()->with('message', 'Folder created successfully');
        } catch (Exception $e) {
            DB::rollBack();

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
            $this->storeFileTree($fileTree, $parent, $user);
        } else {
            foreach ($data['files'] as $file) {
                $path = $file->store('/files/'.$user->id);
                $model = new File();
                $model->name = $file->getClientOriginalName();
                $model->is_folder = false;
                $model->mime = $file->getMimeType();
                $model->size = $file->getSize();
                $parent->appendNode($model);
            }
        }
    }
}
