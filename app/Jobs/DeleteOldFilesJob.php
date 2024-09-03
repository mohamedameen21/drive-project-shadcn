<?php

namespace App\Jobs;

use App\Models\File;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteOldFilesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $email = config('services.demoUser.email');
        $user = User::where('email', $email)->first() ?? null;


        $files = File::where('updated_at', '<', now()->subDays(1))
            ->when($user != null, function ($query) use ($user) {
                $query->where('updated_by', '!=', $user->id);
            })
            ->whereNotNull('path')
            ->whereNotNull('storage_path')
            ->get();

        foreach ($files as $file) {
            $file->permanentDelete();
        }
    }
}
