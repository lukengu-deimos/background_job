<?php
if (!function_exists('runBackgroundJob')) {
    /**
     * Run a background job.
     *
     * @param string $class
     * @param string $method
     * @param array $parameters
     * @param int $delay
     * @param int $priority
     * @return void
     */
    function runBackgroundJob(string $class, string $method, array $parameters = [], int $delay = 0, int $priority = 0): void
    {
        $jobRunner = app(App\Jobs\IJobInterface::class);
        $jobRunner->execute($class, $method, $parameters, $delay, $priority);
    }
}
