<?php
require '../vendor/autoload.php';

// Removes job class 'Job' of queue 'default'
// $result = Resque::dequeue('queue-2', [$argv[1]]);

// Removes job class 'Job' with Job ID '087df5819a790ac666c9608e2234b21e' of queue 'default'
$result = Resque::dequeue('queue-2', ['Job' => '02f9740613acff4704fca5dfdfbefd52']);

// Removes job class 'Job' with arguments of queue 'default'
// Resque::dequeue('default', ['Job' => array('foo' => 1, 'bar' => 2)]);

// Removes multiple jobs
// Resque::dequeue('default', ['Job', 'Job2']);

var_dump($result);