<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;

class LogHelper
{
    /**
     * Log a status message to the 'background_jobs' log channel.
     * This method is used to log general informational messages related to background jobs.
     *
     * @param string $message The status message to log.
     * @return void
     */
    public static function logStatus(string $message): void
    {
        // Log the message at the 'info' level using the 'background_jobs' log channel
        Log::channel('background_jobs')->info($message);
    }

    /**
     * Log an error message to the 'background_jobs_errors' log channel.
     * This method is used to log error messages specifically for background job failures.
     *
     * @param string $message The error message to log.
     * @return void
     */
    public static function logError(string $message): void
    {
        // Log the error message at the 'error' level using the 'background_jobs_errors' log channel
        Log::channel('background_jobs_errors')->error($message);
    }
}
