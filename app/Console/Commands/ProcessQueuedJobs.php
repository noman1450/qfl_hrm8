<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProcessQueuedJobs extends Command
{
    protected $signature = 'queue:process-if-any';

    protected $description = 'Process queued jobs only if the jobs table has pending jobs';

    public function handle()
    {
        $pending = DB::table('jobs')->count();

        if ($pending == 0) {
            $this->info('No queued jobs.');
            return 0;
        }

        $this->info("Found {$pending} queued job(s), processing...");

        $this->call('queue:work', [
            '--stop-when-empty' => true,
            '--tries'           => 3,
            '--sleep'           => 2,
        ]);

        return 0;
    }
}
