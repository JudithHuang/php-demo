<?php
require '../../vendor/autoload.php';

// Removes job class 'Job' of queue 'default'
$result = Resque::dequeue('default', [$argv[1]]);

var_dump($result);

// Removes job class 'Job' with Job ID '087df5819a790ac666c9608e2234b21e' of queue 'default'
// Resque::dequeue('default', ['Job' => '087df5819a790ac666c9608e2234b21e']);

// Removes job class 'Job' with arguments of queue 'default'
// Resque::dequeue('default', ['Job' => array('foo' => 1, 'bar' => 2)]);

// Removes multiple jobs
// Resque::dequeue('default', ['Job', 'Job2']);