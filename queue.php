<?php
require 'vendor/autoload.php';

if(empty($argv[1])) {
    die('Specify the name of a job to add. e.g, php queue.php PHP_Job');
}

date_default_timezone_set('GMT');
Resque::setBackend('127.0.0.1:6379');

// You can also use a DSN-style format:
//Resque::setBackend('redis://user:pass@127.0.0.1:6379');
//Resque::setBackend('redis://user:pass@a.host.name:3432/2');

$args = array(
    'time' => time(),
    'array' => array(
        'test' => 'test',
    ),
);

// $jobId = Resque::enqueue('default', $argv[1], $args, true);
// echo 'job Id'.$jobId;

// php-resque-scheduler
fwrite(STDOUT, "start a Job");
$in = 5;
ResqueScheduler::enqueueIn($in, 'judith', $argv[1], $args);