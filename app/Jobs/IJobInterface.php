<?php

namespace App\Jobs;

/**
 * Interface IJobInterface
 *
 * This interface defines the contract for any job classes that will execute background tasks.
 * Classes implementing this interface must provide the logic for executing a job, which includes:
 *
 * 1. **Executing a method**: Running a specific method of a given class.
 * 2. **Passing parameters**: Accepting parameters to be passed to the method.
 * 3. **Delay**: Allowing the execution of the job to be delayed by a specified number of seconds.
 * 4. **Priority**: Enabling the definition of the job's priority (higher priority jobs should be executed first).
 *
 * Any class that implements this interface must provide a concrete implementation of the `execute` method,
 * which will handle the job execution, manage the delay, and respect the priority logic.
 *
 * @package App\Jobs
 */
interface IJobInterface
{
    /**
     * Execute the job.
     *
     * @param string $class The class to be instantiated and executed.
     * @param string $method The method of the class to be invoked.
     * @param array $parameters The parameters to pass to the method.
     * @param int $delay The number of seconds to delay the job execution.
     * @param int $priority The priority of the job (higher value means higher priority).
     * @return void
     */
    public function execute(string $class, string $method, array $parameters = [],  int $delay = 0, int $priority = 0): void;
}
