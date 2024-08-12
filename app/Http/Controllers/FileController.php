<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilesActionRequest;
use App\Http\Requests\StoreFileRequest;
use App\Http\Requests\StoreFolderRequest;
use App\Http\Requests\TrashFileRequest;
use App\Http\Resources\FileResource;
use App\Http\Responses\JsonResponse;
use App\Models\File;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class FileController extends Controller
{
    public function myFiles(Request $request, ?string $folderPath = null)
    {
        // because the same file name might be in different folders
        // so, only the path will be unique not name
        $folder = null;
        if ($folderPath) {
            $folder = File::query()
                ->where('path', $folderPath)
                ->where('created_by', Auth::id())
                ->firstOrFail();
        }

        if (! $folderPath) {
            $folder = File::getDefaultRoot(Auth::id());
        }

        $search = $request->input('search');

        $files = File::query()
            ->where('created_by', Auth::id())
            ->when(! empty($search), function ($query) use ($search) {
                $query->where('name', 'like', "%$search%");
            }, function ($query) use ($folder) {
                $query->where('parent_id', $folder->id);
            })
            ->orderBy('is_folder', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id')
            ->paginate(10);

        $files = FileResource::collection($files);

        if ($request->wantsJson()) {
            return $files;
        }

        // destructing the ancestors to array, then appending the current folder at the end
        $ancestors = FileResource::collection([...$folder->ancestors, $folder]);

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

        if (! empty($fileTree)) { // uploading a folder
            $this->saveFileTree($fileTree, $parent, $user);
        } else {
            // uploading files
            foreach ($data['files'] as $file) {
                // here the $file is not Eloquent Model It is a instance of Illuminate/HTTP/UploadedFile that contains store method
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

    public function destroy(FilesActionRequest $request)
    {
        //        dd($request->all());
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $parent = $request->parent ?? File::getDefaultRoot(Auth::id());

            if ($data['all']) {
                $children = $parent->children;

                foreach ($children as $child) {
                    //  this delete also deletes the children of this node hence changing
                    //                    $child->delete();
                    $child->moveToTrash();
                }
            } else {
                foreach ($data['ids'] ?? [] as $id) {
                    $file = File::find($id); // use find instead of findOrFail
                    if ($file) {
                        $file->moveToTrash();
                    } else {
                        Log::info("File with id {$id} not found");
                    }
                }
            }
            DB::commit();

            return response()->json([
                'status' => JsonResponse::SUCCESS,
                'message' => 'Files deleted successfully',
            ], ResponseAlias::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete files', $e->getTrace());

            return response()->json([
                'status' => JsonResponse::SUCCESS,
                'message' => 'Files deleted successfully',
            ], ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

    public function download(FilesActionRequest $request)
    {
        $data = $request->validated();
        $parent = $request->parent ?? File::getDefaultRoot(Auth::id());

        $all = $data['all'] ?? false;
        $ids = $data['ids'] ?? [];

        if (! $all && empty($ids)) {
            return response()->json([
                'status' => JsonResponse::ERROR,
                'message' => 'No files selected to download',
            ], ResponseAlias::HTTP_BAD_REQUEST);
        }

        if ($all) {
            $url = $this->createZip($parent->children);
            $filename = $parent->name.'.zip';
        } else {
            if (count($ids) === 1) {
                $file = File::findOrFail($ids[0]);
                if ($file->is_folder) {
                    if ($file->children->count() === 0) {
                        return response()->json([
                            'status' => JsonResponse::ERROR,
                            'message' => 'Folder is empty',
                        ], ResponseAlias::HTTP_BAD_REQUEST);
                    }

                    $url = $this->createZip($file->children);
                    $filename = $file->name.'.zip';
                } else {
                    $newFilePath = 'public/downloads/'.pathinfo($file->storage_path, PATHINFO_BASENAME);
                    Storage::copy($file->storage_path, $newFilePath);
                    $url = asset(Storage::url($newFilePath));
                    $filename = $file->name;
                }
            } else {
                $files = File::whereIn('id', $ids)->get();
                $url = $this->createZip($files);
                $filename = $parent->name.'.zip';
            }
        }

        return [
            'url' => $url,
            'filename' => $filename,
        ];
    }

    private function createZip($files)
    {
        $hashedFileName = 'downloads/zip/'.Str::random(40).'.zip';
        $publicPath = "public/$hashedFileName";

        if (! is_dir($publicPath)) {
            (Storage::makeDirectory(dirname($publicPath)));
        }

        $fullPath = Storage::path($publicPath);

        $zip = new \ZipArchive();

        if ($zip->open($fullPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            $this->addFilesToZip($zip, $files);
        }

        $zip->close();

        return asset(Storage::url($hashedFileName));
    }

    private function addFilesToZip($zip, $files, $ancestors = '')
    {
        foreach ($files as $file) {
            $path = $ancestors.$file->name;
            if ($file->is_folder) {
                $this->addFilesToZip($zip, $file->children, $path.'/');
            } else {
                $zip->addFile(Storage::path($file->storage_path), $path);
            }
        }
    }

    public function trash(Request $request)
    {
        $files = File::onlyTrashed()
            ->where('created_by', Auth::id())
            ->orderBy('is_folder', 'desc')
            ->orderBy('deleted_at', 'desc')
            ->orderBy('id')
            ->paginate(10);

        $files = FileResource::collection($files);

        if ($request->wantsJson()) {
            return $files;
        }

        return Inertia::render('Trash', compact('files'));
    }

    public function restoreFiles(TrashFileRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            if ($data['all']) {
                $files = File::onlyTrashed()
                    ->where('created_by', Auth::id())
                    ->get();

                foreach ($files as $file) {
                    $file->restore();
                }
            } else {
                $files = File::onlyTrashed()
                    ->where('created_by', Auth::id())
                    ->whereIn('id', $data['ids'])
                    ->get();

                foreach ($files as $file) {
                    $file->restore();
                }
            }

            DB::commit();

            return response()->json([
                'status' => JsonResponse::SUCCESS,
                'message' => 'Files restored successfully',
            ], ResponseAlias::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to restore files', $e->getTrace());

            return response()->json([
                'status' => JsonResponse::ERROR,
                'message' => 'Failed to restore files',
            ], ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

    public function permanentDelete(TrashFileRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            if ($data['all']) {
                $files = File::onlyTrashed()
                    ->where('created_by', Auth::id())
                    ->get();

                foreach ($files as $file) {
                    $file->permanentDelete();
                }
            } else {
                $files = File::onlyTrashed()
                    ->where('created_by', Auth::id())
                    ->whereIn('id', $data['ids'])
                    ->get();

                foreach ($files as $file) {
                    $file->permanentDelete();
                }
            }

            DB::commit();

            return response()->json([
                'status' => JsonResponse::SUCCESS,
                'message' => 'Files deleted permanently',
            ], ResponseAlias::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete files permanently', $e->getTrace());

            return response()->json([
                'status' => JsonResponse::ERROR,
                'message' => 'Failed to delete files permanently',
            ], ResponseAlias::HTTP_BAD_REQUEST);
        }
    }
}
