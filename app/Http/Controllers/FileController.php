<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFolderRequest;
use App\Models\File;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FileController extends Controller
{
    public function myFiles()
    {
        return Inertia::render('MyFiles');
    }

    public function createFolder(StoreFolderRequest $request)
    {
        try {
            $data = $request->validated();
            $parentFolder = $request->parent ?? File::getDefaultRoot(Auth::id());

            $file = new File();
            $file->is_folder = 1;
            $file->name = $data['name'];

            $parentFolder->appendNode($file);

            return back()->with('message', 'Folder created successfully');
        } catch (\Exception $e) {
            return back()->with('message', 'Failed to create folder');
        }
    }
}
