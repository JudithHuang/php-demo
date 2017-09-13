<?php
require '../vendor/autoload.php';
require 'jobs/Job.php';
require 'jobs/FailJob.php';
use Psr\Log\LogLevel;

date_default_timezone_set('GMT');
Resque::setBackend('localhost:6379');

$logger = new Resque_Log(false);
 // Start a single worker
 $worker = new Resque_Worker(explode(',', 'default'));
 $worker->setLogger($logger);
 $logger->log(LogLevel::NOTICE, 'Starting worker {worker}', array('worker' => $worker));
 $worker->work();