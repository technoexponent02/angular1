<?php

namespace App\Console\Commands;

use File;
use App\Models\S3MoveFail;

use Illuminate\Console\Command;

class MoveFilesToS3 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 's3:move-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move previously failed files to s3.';

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
        $s3MoveFails = S3MoveFail::all(['id', 'local_path', 's3_dirname']);

        foreach ($s3MoveFails as $fail) {
            if (!empty($fail->local_path)) {
                $file_name = basename($fail->local_path);

                if (File::exists($fail->local_path)){
                    move_to_s3($fail->s3_dirname . '/' . $file_name, $fail->local_path);
                }
                else {
                    // Post::where('image', $file_name)->delete();
                    $fail->delete();
                }
            }
        }
        $this->info('The failed files are moved to s3! There may be some items still remaining.');
    }
}
