# Get Start

```shell
# 项目根目录执行, 获取依赖的第三方库
composer install
```

# php-resque

## 将 Job push 到队列

```shell
cd src
php resque/Queue.php Job
```

## 查看 Job 的状态

```shell
cd src
php resque/checkStatus.php $jobId
```

## 将 Job 从队列中删除

```shell
cd src
# TODO: queue 被删除, Job 仍然在
php resque/Dequeue.php
```

## 启动 Work

```shell
cd src
QUEUE='*' APP_INCLUDE=jobs/init.php php ../vendor/chrisboulton/php-resque/bin/resque
# or
php resque/Work.php
```

## 查看 redis 中 Job

```shell
keys *
get $key
# 删除全部
FLUSHALL
```

# php-resque-scheduler

## 将 Job push 到队列

```shell
cd src
php resqueScheduler/Queue.php Job
```

## 启动 Work

```shell
# 注: 在 scheduler 下将 Job 放入队列前，要启动两个 Work
#  - scheduler_work: 把可执行的 Jobs 放入到执行队列中
#  - resque_work: 执行执行队列中的 Job
cd src
# 该 Work 用于把可执行的 Jobs 放入到执行队列中
php resqueScheduler/Work.php
```