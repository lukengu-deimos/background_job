
## Custom Background Job Runner for Laravel


This project implements a custom background job runner system in Laravel, allowing the execution of PHP classes as background jobs independent of Laravel's built-in queue system. The solution demonstrates scalability, error handling, job retries, logging, and security features.

## Features
- Execute Classes and Methods in Background: Allows background execution of PHP classes and methods.
- Error Handling: Catches and logs errors in a separate log file.
- Retry Mechanism: Configurable retry attempts if a job fails.
- Logging: Logs job status (success, failure) with timestamps.
- Security: Validates and sanitizes class and method names to prevent execution of unauthorized code.
- Cross-Platform Support: Works on both Windows and Unix-based systems for background execution.
- Job Delays: Support for delaying job execution by a specified number of seconds.
- Job Priority: Implements basic job priority for managing execution order.

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Setup
1. Install the background job runner service and helper function by adding the service and helper files to your Laravel app.
2. Register the helper file in `composer.json` under `autoload > files`:

```json
"autoload": "{
    "files": [
        "app/Helpers/BackgroundJobHelper.php"
    ]
}"
```
## Usage
To run a job in the background, use the runBackgroundJob helper function.
```aiignore
runBackgroundJob('App\Jobs\MyJobs\ExampleJob', 'run', ['param1', 'param2']);
```

## Error Handling and Retry
The system automatically retries failed jobs up to a configurable number times with a provided delay.


