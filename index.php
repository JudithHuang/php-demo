<?php
  require 'vendor/autoload.php';


  // Required if redis is located elsewhere
  // Resque::setBackend('localhost:6379');

  // $args = array(
  //         'name' => 'Chris'
  //         );
  // Resque::enqueue('default', 'My_Job', $args);

  // class My_Job
  // {
  //     public function perform()
  //     {
  //         // Work work work
  //         echo $this->args['name'];
  //     }
  // }

  // $token = Resque::enqueue('default', 'My_Job', $args, true);
  // echo 'status'.$token;
