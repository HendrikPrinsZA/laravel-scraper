<?php

namespace App\Console;

use App\Actions\Scrapers\FetchBicycles;
use App\Actions\Scrapers\FetchMediumPosts;
use App\Actions\Scrapers\FetchRedditPosts;
use App\Actions\Scrapers\FetchTwitterPosts;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        FetchBicycles::class,
        FetchRedditPosts::class,
        FetchTwitterPosts::class,
        FetchMediumPosts::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
