<?php
require '../vendor/autoload.php';
require 'jobs/Job.php';
require 'jobs/FailJob.php';

// Required if redis is located elsewhere
Resque::setBackend('localhost:6379');

$args = array('name' => 'Judith Huang');
$token = Resque::enqueue('default', $argv[1], $args, true);
echo 'Job\'s token is: '.$token;