<?php
require '../vendor/autoload.php';

$status = new Resque_Job_Status($argv[1]);
var_dump($status->get());