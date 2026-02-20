<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\ElectionPeriod;
use Carbon\Carbon;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Check and update election statuses every minute
        $schedule->call(function () {
            $now = Carbon::now('Africa/Blantyre');

            $activeCandidate = ElectionPeriod::where('start_time', '<=', $now)
                ->where('end_time', '>=', $now)
                ->orderBy('start_time', 'desc')
                ->first();

            if ($activeCandidate) {
                ElectionPeriod::where('is_active', true)
                    ->where('id', '!=', $activeCandidate->id)
                    ->update(['is_active' => false]);

                if (!$activeCandidate->is_active) {
                    $activeCandidate->update(['is_active' => true]);
                }
            } else {
                ElectionPeriod::where('is_active', true)->update(['is_active' => false]);
            }

            ElectionPeriod::finalizeEndedElections($now);
        })->everyMinute();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
