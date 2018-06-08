<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\sendEmail',
        'App\Console\Commands\generateSitemap',
        'App\Console\Commands\MoveFilesToS3',
        'App\Console\Commands\DeletePhoto',
        'App\Console\Commands\DeleteVideo',
        'App\Console\Commands\BackupDB',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('sitemap:generate')->dailyAt('23:55');

        $schedule->command('delete:photo')->hourly();
        $schedule->command('delete:video')->hourly();

        $schedule->command('s3:move-files')->twiceDaily(1, 13);

       // $schedule->command('database:backup')->hourly();(6-11-17)
        $schedule->command('database:backup')->everyTenMinutes();//(6-11-17)
    }
}
