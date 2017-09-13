<?php
require '../vendor/autoload.php';
date_default_timezone_set('GMT');
Resque::setBackend('localhost:6379');

$worker = new ResqueScheduler_Worker();
$worker->logLevel = ResqueScheduler_Worker::LOG_NORMAL;
fwrite(STDOUT, "*** Starting scheduler worker\n");
$worker->work();