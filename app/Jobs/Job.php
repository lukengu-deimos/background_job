<?php

namespace App\Jobs;

use Exception;
use Illuminate\Support\Facades\Log;
use ReflectionClass;

class Job implements IJobInterface
{
    // Maximum number of retry attempts for failed jobs
    public function __construct(protected $maxRetries = 3)
    {
        // Get the maximum retries from the config file or use the default value
        $this->maxRetries = config('job.max_attempts') ?? $maxRetries;
    }

    /**
     * Run the background job.
     *
     * This method is responsible for validating the job, serializing the parameters,
     * building the command to execute in the background, and running it.
     * In case of an exception, it will retry the job for a limited number of attempts.
     *
     * @param string $class The class name of the job to execute.
     * @param string $method The method name to call within the class.
     * @param array $parameters The parameters to pass to the method.
     * @param int $delay The number of seconds to delay the job.
     * @param int $priority The priority of the job (higher means higher priority).
     * @return void
     */
    public function execute(string $class, string $method, array $parameters = [], int $delay = 0, int $priority = 0): void
    {
        try {
            // Validate class and method names to ensure safety
            $this->validateClassAndMethod($class, $method);

            // Serialize and encode the parameters to pass as a string in the background command
            $params = base64_encode(serialize($parameters));

            // Build the command to run the job in the background
            $command = $this->buildCommand($class, $method, $params, $delay, $priority);

            // Execute the command in the background
            $this->executeCommandInBackground($command);

            // Log the status of the job execution
            LogHelper::logStatus("Job '{$class}@{$method}' started with parameters: " . json_encode($parameters));

        } catch (Exception $e) {
            // In case of failure, log the error and retry the job
            LogHelper::logError("Failed to run job '{$class}@{$method}': " . $e->getMessage());
            $this->retryJob($class, $method, $parameters, $delay, $priority);
        }
    }

    /**
     * Retry a failed job up to a maximum number of attempts.
     *
     * This method handles retrying the job if it fails. It will attempt to run the job again,
     * and log each retry attempt. If the maximum retries are reached, it will log the failure.
     *
     * @param string $class The class name of the job.
     * @param string $method The method name to execute.
     * @param array $parameters The parameters to pass to the method.
     * @param int $delay The number of seconds to delay the job.
     * @param int $priority The priority of the job.
     * @return void
     */
    private function retryJob(string $class, string $method, array $parameters, int $delay, int $priority): void
    {
        static $retryAttempts = 0;
        $retryAttempts++;

        // Check if the retry count has exceeded the maximum number of retries
        if ($retryAttempts <= $this->maxRetries) {
            LogHelper::logStatus("Retrying job... Attempt $retryAttempts");
            // Retry the job by calling execute method again
            $this->execute($class, $method, $parameters, $delay, $priority);
            return;
        }

        // Log failure after exceeding retry attempts
        LogHelper::logStatus("Job failed after $retryAttempts attempts");
    }

    /**
     * Validate the class and method to ensure they are safe to execute.
     *
     * This method checks if the class exists and belongs to an allowed namespace.
     * It also checks if the specified method exists in the class.
     *
     * @param string $class The class name.
     * @param string $method The method name.
     * @return void
     * @throws Exception If the class or method is invalid.
     */
    private function validateClassAndMethod(string $class, string $method): void
    {
        // Only allow classes within the 'App\Jobs\MyJobs' namespace
        if (!$this->isValidNamespace($class)) {
            LogHelper::logError("Unauthorized class: {$class}");
            throw new Exception("Unauthorized class: {$class}");
        }

        // Check if the method exists on the class
        if (!method_exists($class, $method)) {
            LogHelper::logError("Method {$method} does not exist on class {$class}");
            throw new Exception("Method {$method} does not exist on class {$class}");
        }
    }

    /**
     * Check if the class belongs to a valid namespace.
     *
     * This method checks whether the given class belongs to the allowed namespace ('App\Jobs\MyJobs').
     * It also ensures that the class exists before performing the check.
     *
     * @param string $class The class name.
     * @return bool True if the class belongs to the valid namespace, false otherwise.
     */
    private function isValidNamespace(string $class): bool
    {
        // Check if the class exists
        if (class_exists($class)) {
            // Use ReflectionClass to get the class namespace
            $reflection = new ReflectionClass($class);
            $namespace = $reflection->getNamespaceName();

            // Only allow classes from the 'App\Jobs\MyJobs' namespace
            return $namespace === 'App\\Jobs\\MyJobs';
        }
        return false; // Class doesn't exist
    }

    /**
     * Build the shell command to execute the job in the background.
     *
     * This method constructs the command that will be used to run the job in the background,
     * passing along the class, method, serialized parameters, delay, and priority.
     *
     * @param string $class The class name.
     * @param string $method The method name.
     * @param string $params The base64-encoded parameters to pass.
     * @param int $delay The delay in seconds before executing the job.
     * @param int $priority The priority of the job.
     * @return string The constructed shell command.
     */
    private function buildCommand(string $class, string $method, string $params, int $delay = 0, int $priority = 0): string
    {
        // Get the PHP binary path and artisan script path
        $phpBinary = PHP_BINARY;
        $scriptPath = base_path('artisan');

        // Construct the command to run the job in the background
        return "{$phpBinary} {$scriptPath} run:background-job {$class} {$method} {$params} {$delay} {$priority} > /dev/null 2>&1 &";
    }

    /**
     * Execute the command in the background.
     *
     * This method runs the background command, ensuring compatibility with both Windows and Unix systems.
     *
     * @param string $command The shell command to execute.
     * @return void
     */
    private function executeCommandInBackground(string $command): void
    {
        // Check if the system is Windows
        if (strncasecmp(PHP_OS, 'WIN', 3) === 0) {
            // Windows-specific background execution
            pclose(popen("start /B " . $command, "r"));
        } else {
            // For Unix-based systems, execute the command in the background
            exec($command);
        }
    }
}
