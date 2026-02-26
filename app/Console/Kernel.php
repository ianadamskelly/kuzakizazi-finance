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
        // Commands are usually auto-discovered, but you can register them here if needed.
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Schedule invoice generation to run at the start of every month.
        $schedule->command('invoices:generate')->monthlyOn(1, '02:00'); // Runs on the 1st of the month at 2 AM.

        // Schedule payroll generation to run at the end of every month.
        $schedule->command('payroll:generate')->monthlyOn(28, '03:00'); // Runs on the 28th of the month at 3 AM.
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}