<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected $commands = [
        Commands\UpdateVehicleLogHistory::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // Schedule the 'vehicle_log:update' command to run hourly
        $schedule->command('vehicle_log:update')->everyMinute();
        $schedule->command('alert:user')->everyMinute();
        // $schedule->command('check:vehicle-log')->everyMinute();
        $schedule->command('update:driver-log')->everyMinute();
        $schedule->command('update:off-duty-log')->everyMinute();
        $schedule->command('nondriving:update')->everyFiveMinutes();
        
        // Retain the existing session check logic
        $schedule->call(function () {
            // Check if 'fiveMin' session exists and if it's older than five minutes
            if (session()->has('fiveMin') && now()->diffInMinutes(session('fiveMin')) >= 5) {
                // Remove the 'fiveMin' session
                Session::forget('fiveMin');
            }
        })->everyMinute(); // Check every minute
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
