<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackgroundJob extends Command
{
    protected $signature = 'run:background-job {class} {method} {params?} {delay=0} {priority=0}';
    protected $description = 'Run a background job';


    public function handle(): void
    {
        $class = $this->argument('class');
        $method = $this->argument('method');
        $params = unserialize(base64_decode($this->argument('params')));
        $delay = $this->argument('delay');
        $priority = $this->argument('priority');

        $job = call_user_func([$class, $method], ...$params);
        // Dispatch the job
        $job->onQueue($priority)->delay(now()->addSeconds($delay))->dispatch();

        $this->info('Job dispatched successfully!');
    }
}
