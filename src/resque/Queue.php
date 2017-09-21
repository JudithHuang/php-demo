<?php
require '../vendor/autoload.php';
require 'jobs/Job.php';
require 'jobs/FailJob.php';

// Required if redis is located elsewhere
Resque::setBackend('localhost:6379');

$args = array('name' => 'Judith Huang');
// $monitor Set to true to be able to monitor the status of a job.
$trackStatus = true;
$token = Resque::enqueue('queue-2', $argv[1], $args, $trackStatus);
echo 'Job\'s token is: '.$token;