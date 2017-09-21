<?php
require '../vendor/autoload.php';
require 'jobs/Job.php';
require 'jobs/FailJob.php';

// Required if redis is located elsewhere
Resque::setBackend('localhost:6379');

$in = 15;
$args = array('name' => 'Judith Huang');
ResqueScheduler::enqueueIn($in, 'default', $argv[1], $args);

var_dump('start a Job');