<?php
require 'vendor/autoload.php';
require 'job.php';

use Psr\Log\LogLevel;
date_default_timezone_set('GMT');
Resque::setBackend('127.0.0.1:6379');

$logger = new Resque_Log(false);
 // Start a single worker
 $worker = new Resque_Worker(explode(',', 'judith'));
 $worker->setLogger($logger);
 $logger->log(LogLevel::NOTICE, 'Starting worker {worker}', array('worker' => $worker));
 $worker->work(5);