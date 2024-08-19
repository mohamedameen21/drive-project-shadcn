<?php

use App\Http\Controllers\DemoUserController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ProfileController;
use App\Models\User;
use App\Models\File;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/demo-user-generation', [DemoUserController::class, 'storeDemoUserToken'])->middleware(['guest'])->name('demo-user-generation');
Route::get('authenticate/{token}', [DemoUserController::class, 'authenticateDemoUser'])->middleware(['guest', 'signed'])->name('authenticate');

Route::controller(FileController::class)
    ->middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/my-files/{folderPath?}', 'myFiles')
            ->where('folderPath', '(.*)')->name('myFiles');

        Route::get('/trash', 'trash')->name('trash');
        Route::delete('/trash', 'permanentDelete')->name('trash.delete');
        Route::post('/trash', 'restoreFiles')->name('trash.restore');

        Route::post('/folder', 'createFolder')->name('folder.store');

        Route::post('/file', 'store')->name('file.store');
        Route::delete('/file', 'destroy')->name('file.destroy');

        Route::post('download', 'download')->name('file.download');
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/shared-with-me', function () {
    })->name('shared-with-me');

    Route::get('/shared-by-me', function () {
    })->name('shared-by-me');
});

require __DIR__.'/auth.php';
