<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\ElectionPeriod;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Check and update election statuses every minute
        $schedule->call(function () {
            $elections = ElectionPeriod::where('is_active', true)->get();
            
            foreach ($elections as $election) {
                if ($election->hasEnded()) {
                    $election->update(['is_active' => false]);
                }
            }
        })->everyMinute();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}