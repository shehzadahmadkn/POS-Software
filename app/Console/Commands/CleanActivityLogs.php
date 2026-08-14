<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanActivityLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activitylog:clean-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up activity logs older than 6 months to maintain performance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = \Carbon\Carbon::now()->subMonths(6);
        $deleted = \Spatie\Activitylog\Models\Activity::where('created_at', '<', $date)->delete();
        $this->info("Deleted {$deleted} old activity log records.");
    }
}
