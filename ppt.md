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

Jobs are queued as follows:

<pre><code class="php">
// Required if redis is located elsewhere
Resque::setBackend('localhost:6379');
$args = array('name' => 'Judith');
Resque::enqueue('default', 'Job', $args);
</code></pre>

Storage in Redis

<pre><code class="shell">
127.0.0.1:6379> keys *
1) "resque:job:6e72d0a153aa55df600e5856c85dbf06:status"
2) "resque:queue:default"
3) "resque:job:838bdb9a4bb550cb7d9baacd54b42476:status"
4) "resque:queues"
5) "resque:job:b10f67f4a41578255f58afdf31632f7e:status"
6) "resque:queue:queue-2"
127.0.0.1:6379> type resque:queues
set
127.0.0.1:6379> type resque:queue:default
list
127.0.0.1:6379> SMEMBERS resque:queues
1) "default"
2) "queue-2"
127.0.0.1:6379> LRANGE resque:queue:queue-2 0 -1
1) "{\"class\":\"Job\",\"args\":[{\"name\":\"Judith Huang\"}],\"id\":\"6e72d0a153aa55df600e5856c85dbf06\",\"queue_time\":1505993824.68912}"
127.0.0.1:6379> LRANGE resque:queue:default 0 -1
1) "{\"class\":\"Job\",\"args\":[{\"name\":\"Judith Huang\"}],\"id\":\"b10f67f4a41578255f58afdf31632f7e\",\"queue_time\":1505993558.822553}"
2) "{\"class\":\"Job\",\"args\":[{\"name\":\"Judith Huang\"}],\"id\":\"838bdb9a4bb550cb7d9baacd54b42476\",\"queue_time\":1505993776.0565}"
127.0.0.1:6379> 
</code></pre>

[slide]
## Defining Jobs
- Each job should be in its own class, and include a `perform` method.
<pre><code class="php">
class Job
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
This method can be used to conveniently remove a job from a queue.

<pre><code class="php">
// Removes job class 'Job' of queue 'default'
Resque::dequeue('default', ['Job']);
// Removes job class 'Job' with Job ID '087df5819a790ac666c9608e2234b21e' of queue 'default'
Resque::dequeue('default', ['Job' => '087df5819a790ac666c9608e2234b21e']);
// Removes job class 'Job' with arguments of queue 'default'
Resque::dequeue('default', ['Job' => array('foo' => 1, 'bar' => 2)]);
// Removes multiple jobs
Resque::dequeue('default', ['Job', 'Job2']);
</code></pre>

<pre><code class="shell">
# 执行 php resque/Dequeue.php 向 queue 中 push 两个 Job 后
127.0.0.1:6379> keys *
1) "resque:job:b52ec60907dff2b44fd3917d5b2a2244:status"
2) "resque:queues"
3) "resque:queue:queue-2"
4) "resque:job:02f9740613acff4704fca5dfdfbefd52:status"
127.0.0.1:6379> LRANGE resque:queue:queue-2 0 -1
1) "{\"class\":\"Job\",\"args\":[{\"name\":\"Judith Huang\"}],\"id\":\"02f9740613acff4704fca5dfdfbefd52\",\"queue_time\":1506004349.612935}"
2) "{\"class\":\"Job\",\"args\":[{\"name\":\"Judith Huang\"}],\"id\":\"b52ec60907dff2b44fd3917d5b2a2244\",\"queue_time\":1506004351.706851}"
# 执行 php resque/Dequeue.php 将 id 为 02f9740613acff4704fca5dfdfbefd52 的 Job pop 后
127.0.0.1:6379> LRANGE resque:queue:queue-2 0 -1
1) "{\"class\":\"Job\",\"args\":[{\"name\":\"Judith Huang\"}],\"id\":\"b52ec60907dff2b44fd3917d5b2a2244\",\"queue_time\":1506004351.706851}"
127.0.0.1:6379>
</code></pre>

[slide]
## Tracking Job Statuses
- php-resque has the ability to perform basic status tracking of a queued job. The status information will allow you to check if a job is in the queue, is currently being run, has finished, or has failed.
- To track the status of a job, pass true as the fourth argument to `Resque::enqueue`. A token used for tracking the job status will be returned
<pre><code class="php">
// $monitor Set to true to be able to monitor the status of a job.
$trackStatus = true;
$token = Resque::enqueue('default', 'Job', $args, $trackStatus);
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
Resque workers are rake tasks that run forever.

<pre><code>
// Running All Queues
QUEUE='*' APP_INCLUDE=jobs/init.php php ../vendor/chrisboulton/php-resque/bin/resque
</code></pre>

<pre><code class="shell">
# 启动 Work
127.0.0.1:6379> keys *
1) "resque:workers"
2) "resque:worker:huangjuandeMac-mini.local:8020:queue-2:started"
127.0.0.1:6379> type resque:workers
set
127.0.0.1:6379> SMEMBERS resque:workers
1) "huangjuandeMac-mini.local:8020:queue-2"
# 执行 php resque/Dequeue.php 向 queue 中 push 一个 Job 后
127.0.0.1:6379> keys *
1) "resque:job:2f4abe6e7e15f47d1007ea7bcefa763b:status"
2) "resque:stat:processed:huangjuandeMac-mini.local:6375:queue-2"
3) "resque:worker:huangjuandeMac-mini.local:8020:queue-2:started"
4) "resque:workers"
5) "resque:stat:processed"
6) "resque:queues"
127.0.0.1:6379> get resque:stat:processed:huangjuandeMac-mini.local:6375:queue-2
"1"
127.0.0.1:6379> get resque:stat:processed
"1"
# 执行 php resque/Dequeue.php 再向 queue 中 push 一个 Job 后
127.0.0.1:6379> get resque:stat:processed:huangjuandeMac-mini.local:6375:queue-2
"2"
127.0.0.1:6379> get resque:stat:processed
"2"
127.0.0.1:6379>
</code></pre>

[slide]
# php-resque-scheduler
- php-resque-scheduler is a PHP port of resque-scheduler, which adds support for scheduling items in the future to Resque.
- At the moment, php-resque-scheduler only supports delayed jobs, which is the ability to push a job to the queue and have it run at a certain timestamp, or in a number of seconds. Support for recurring jobs (similar to CRON) is planned for a future release.

[slide]
# Delayed Jobs
Delayed jobs are one-off jobs that you want to be put into a queue at some point in the future. The classic example is sending an email
<pre><code class="php">
require 'Resque/Resque.php';
require 'ResqueScheduler/ResqueScheduler.php';
$in = 3600;
$args = array('id' => $user->id);
ResqueScheduler::enqueueIn($in, 'email', 'SendFollowUpEmail', $args);
</code></pre>

<pre><code class="shell">
127.0.0.1:6379> keys *
1) "resque:delayed_queue_schedule"
2) "resque:delayed:1506008557"
127.0.0.1:6379> type resque:delayed:1506008557
list
127.0.0.1:6379> type resque:delayed_queue_schedule
zset
127.0.0.1:6379> LRANGE resque:delayed:1506008557 0 -1
1) "{\"class\":\"Job\",\"args\":[{\"name\":\"Judith Huang\"}],\"queue\":\"default\"}"
127.0.0.1:6379> ZREVRANGE resque:delayed_queue_schedule 0 -1
1) "1506008557"
127.0.0.1:6379>
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

<pre><code class="shell">
$worker = new ResqueScheduler_Worker();
$worker->work();
127.0.0.1:6379> keys *
1) "resque:delayed_queue_schedule"
2) "resque:delayed:1506009749"
127.0.0.1:6379> keys *
1) "resque:job:7d399bf6f16ad99380e62a4fb863932b:status"
2) "resque:queues"
3) "resque:stat:processed"
4) "resque:stat:processed:huangjuandeMac-mini.local:6323:default"
127.0.0.1:6379> get resque:job:7d399bf6f16ad99380e62a4fb863932b:status
"{\"status\":4,\"updated\":1506009752}"
127.0.0.1:6379>
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