<?php

namespace App\Jobs\MyJobs;

class ExampleJob
{
    public function printHello (string $name): string
    {
        return print "Hello, $name!";
    }
}
