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
# 注: 如果 $trackStatus 设置为 true , 则不会删掉 job.status 的记录
php resque/Dequeue.php
```

## 启动 Work

```shell
cd src
QUEUE='default' APP_INCLUDE=jobs/init.php php ../vendor/chrisboulton/php-resque/bin/resque
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
cd src
php resqueScheduler/Work.php
```