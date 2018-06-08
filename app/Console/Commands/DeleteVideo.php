<?php

namespace App\Console\Commands;

use Storage;
use Carbon\Carbon;
use App\Models\Video;

use Illuminate\Console\Command;

class DeleteVideo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:video';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete temporary videos which are not converted into post.';

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
        $oldVideo= Video::where([
            'is_draft' => 'N',
            'schedule_remove' => 'Y'
        ])
        ->where('created_at', '>=', Carbon::now()->subHours(6));
        // Get videos.
        $videos = $oldVideo->get(['id', 'save_name']);

        foreach ($videos as $video) {
            try {
                /* Delete from disk */
                Storage::delete(['/video/' . $video->save_name, '/video/thumbnail/' . $video->save_name]);
            } catch (\Exception $e) {
                // dd($e->getMessage());
            }
        }

        // Delete DB rows.
        $oldVideo->delete();

        $this->info('The temporary videos are deleted from local storage.');
    }
}
