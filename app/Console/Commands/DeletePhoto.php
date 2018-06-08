<?php

namespace App\Console\Commands;

use File;
use Carbon\Carbon;
use App\Models\Photo;

use Illuminate\Console\Command;

class DeletePhoto extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:photo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete temporary photos which are not converted into post.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $oldPhoto = Photo::where([
            'is_draft' => 'N',
            'schedule_remove' => 'Y'
        ])
        ->where('created_at', '>=', Carbon::now()->subHours(6));
        // Get photos.
        $photos = $oldPhoto->get(['id', 'save_name']);
        // Post image path.
        $path = public_path() . '/uploads/post/';
        foreach ($photos as $photo) {
            $photo_path = $path . $photo->save_name;
            if (File::exists($photo_path)) {
                try {
                    /* Delete from local disk */
                    File::Delete($photo_path);
                } catch (\Exception $e) {
                    // dd($e->getMessage());
                }
            }
        }

        // Delete DB rows.
        $oldPhoto->delete();

        $this->info('The temporary photos are deleted from local storage.');
    }
}
