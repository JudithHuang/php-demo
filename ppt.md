title: php-resque
speaker: Judith Huang
url: https://github.com/judithhuang/php-demo
transition: slide
files: /js/demo.js,/css/demo.css,/js/zoom.js
theme: moon
usemathjax: yes

[slide]
# php-resque 
# php-resque-scheduler

[slide]
# php-resque

- Resque is a Redis-backed library for creating background jobs, placing those jobs on one or more queues, and processing them later.

[slide]
# Background

- Resque was pioneered and is developed by the fine folks at GitHub, and written in Ruby. What you're seeing here is an almost direct port of the Resque worker and enqueue system to PHP.  
- For more information on Resque, visit the official GitHub project: https://github.com/resque/resque
- For further information, see the launch post on the GitHub blog: http://github.com/blog/542-introducing-resque
- The PHP port does NOT include its own web interface for viewing queue stats, as the data is stored in the exact same expected format as the Ruby version of Resque.

[slide]
# Features

- Workers can be distributed between multiple machines
- Includes support for priorities (queues)
- Resilient to memory leaks (forking)
- Expects failure

- Has the ability to track the status of jobs
- Will mark a job as failed, if a forked child running a job does not exit with a status code as 0
- Has built in support for setUp and tearDown methods, called pre and post jobs

[slide]
# Queue sytem

![queue-system](/images/queue-system.jpg "queue-system1")

[slide]
# Getting Started
- Add php-resque to your application's composer.json.
<pre><code class="json">
"require": {
    "chrisboulton/php-resque": "dev-master",
    "chrisboulton/php-resque-scheduler": "dev-master"
}
</code></pre>
- Run composer install.
- If you haven't already, add the Composer autoload to your project's initialization file.

[slide]
# Jobs

[slide]
## Queueing Jobs
- Jobs are queued as follows:
<pre><code class="php">
// Required if redis is located elsewhere
Resque::setBackend('localhost:6379');
$args = array('name' => 'Chris');
Resque::enqueue('default', 'My_Job', $args);
</code></pre>

[slide]
## Defining Jobs
- Each job should be in its own class, and include a `perform` method.
<pre><code class="php">
class My_Job
{
    public function setUp()
    {
        // ... Set up environment for this job
    }

    public function perform()
    {
        // .. Run job
    }

    public function tearDown()
    {
        // ... Remove environment for this job
    }
}
</code></pre>

[slide]
## Dequeueing Jobs
- This method can be used to conveniently remove a job from a queue.
<pre><code class="php">
// Removes job class 'My_Job' of queue 'default'
Resque::dequeue('default', ['My_Job']);
// Removes job class 'My_Job' with Job ID '087df5819a790ac666c9608e2234b21e' of queue 'default'
Resque::dequeue('default', ['My_Job' => '087df5819a790ac666c9608e2234b21e']);
// Removes job class 'My_Job' with arguments of queue 'default'
Resque::dequeue('default', ['My_Job' => array('foo' => 1, 'bar' => 2)]);
// Removes multiple jobs
Resque::dequeue('default', ['My_Job', 'My_Job2']);
</code></pre>

[slide]
## Tracking Job Statuses
- php-resque has the ability to perform basic status tracking of a queued job. The status information will allow you to check if a job is in the queue, is currently being run, has finished, or has failed.
- To track the status of a job, pass true as the fourth argument to `Resque::enqueue`. A token used for tracking the job status will be returned
<pre><code class="php">
$token = Resque::enqueue('default', 'My_Job', $args, true);
echo $token;
// To fetch the status of a job:
$status = new Resque_Job_Status($token);
echo $status->get(); // Outputs the status
</code></pre>

[slide]
## Job statuses
- STATUS_WAITING: Job is still queued
- STATUS_RUNNING: Job is currently running
- STATUS_FAILED: Job has failed
- STATUS_COMPLETE: Job is complete
- false: Failed to fetch the status, is the token valid ?

[slide]
# Workers
- Resque workers are rake tasks that run forever.

<pre><code>
// start a work
QUEUE=file_serve php bin/resque
// Running All Queues
QUEUE='*' bin/resque
// Running Multiple Workers
COUNT=5 bin/resque
// Custom prefix
PREFIX=my-app-name bin/resque
</code></pre>

[slide]
# Event/Hook System
- php-resque has a basic event system that can be used by your application to customize how some of the php-resque internals behave.
- You listen in on events (as listed below) by registering with `Resque_Event` and supplying a callback that you would like triggered when the event is raised:
<pre><code>
Resque_Event::listen('eventName', [callback]);
</code></pre>

[slide]
## Events
- beforeFirstFork: Called once, as a worker initializes.
- beforeFork: Called before php-resque forks to run a job.
- afterFork: Called after php-resque forks to run a job (but before the job is run).    
- beforePerform: Called before the setUp and perform methods on a job are run.
- afterPerform: Called after the perform and tearDown methods on a job are run.
- onFailure: Called whenever a job fails.
- beforeEnqueue: Called immediately before a job is enqueued using the Resque::enqueue method.
- afterEnqueue: Called after a job has been queued using the Resque::enqueue method. 

[slide]
# php-resque-scheduler
- php-resque-scheduler is a PHP port of resque-scheduler, which adds support for scheduling items in the future to Resque.
- At the moment, php-resque-scheduler only supports delayed jobs, which is the ability to push a job to the queue and have it run at a certain timestamp, or in a number of seconds. Support for recurring jobs (similar to CRON) is planned for a future release.

[slide]
# Delayed Jobs
- Delayed jobs are one-off jobs that you want to be put into a queue at some point in the future. The classic example is sending an email
<pre><code class="php">
require 'Resque/Resque.php';
require 'ResqueScheduler/ResqueScheduler.php';
$in = 3600;
$args = array('id' => $user->id);
ResqueScheduler::enqueueIn($in, 'email', 'SendFollowUpEmail', $args);
</code></pre>

[slide]
# Delayed Jobs
- Instead of passing a relative time in seconds, you can also supply a timestamp as either a DateTime object or integer containing a UNIX timestamp to the enqueueAt method:
<pre><code>
require 'Resque/Resque.php';
require 'ResqueScheduler/ResqueScheduler.php';
$time = 1332067214;
ResqueScheduler::enqueueAt($time, 'email', 'SendFollowUpEmail', $args);
$datetime = new DateTime('2012-03-18 13:21:49');
ResqueScheduler::enqueueAt($datetime, 'email', 'SendFollowUpEmail', $args);
</code></pre>

[slide]
# Worker
- Like resque, resque-scheduler includes a worker that runs in the background. This worker is responsible for pulling items off the schedule/delayed queue and adding them to the queue for resque. This means that for delayed or scheduled jobs to be executed, the worker needs to be running.
- A basic "up-and-running" resque-scheduler.php file is included that sets up a running worker environment is included in the root directory. It accepts many of the same environment variables as php-resque:

[slide]
# Worker
- REDIS_BACKEND: Redis server to connect to
- LOGGING: Enable logging to STDOUT
- VERBOSE: Enable verbose logging
- VVERBOSE: Enable very verbose logging
- INTERVAL: Sleep for this long before checking scheduled/delayed queues
- APP_INCLUDE: Include this file when starting (to launch your app)
- PIDFILE: Write the PID of the worker out to this file

[slide]
# Event/Hook System
- php-resque-scheduler uses the same event system used by php-resque and exposes the following events.
- afterSchedule: Called after a job has been added to the schedule.
- beforeDelayedEnqueue: Called immediately after a job has been pulled off the delayed queue and right before the job is added to the queue in resque.

[slide]
# References
- [php-resque](https://github.com/chrisboulton/php-resque)
- [php-resque-scheduler](https://github.com/chrisboulton/php-resque-scheduler)
- [PHP-Resque的使用](https://icewing.cc/post/background-jobs-and-phpresque-2.html)