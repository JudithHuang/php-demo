<?php
require '../vendor/autoload.php';

// Removes job class 'Job' of queue 'default'
// $result = Resque::dequeue('queue-2', [$argv[1]]);

// Removes job class 'Job' with Job ID '087df5819a790ac666c9608e2234b21e' of queue 'default'
$result = Resque::dequeue('default', ['Job' => '6c807cad55d0f5b61c2d2d421ae724fc']);

// Removes job class 'Job' with arguments of queue 'default'
// Resque::dequeue('default', ['Job' => array('foo' => 1, 'bar' => 2)]);

// Removes multiple jobs
// Resque::dequeue('default', ['Job', 'Job2']);

var_dump($result);
