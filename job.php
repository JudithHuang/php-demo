<?php
class job
{
    public function setUp()
    {
        fwrite(STDOUT, "===> 这里放一些任务执行之前需要处理的逻辑代码");
    }

    public function perform()
    {
        fwrite(STDOUT, "这里可以接受参数的哦，比方说我们取一下order_id:");
        fwrite(STDOUT, "这里是具体的处理订单的逻辑代码");
    }

    /**
     * 执行任务的后置条件
     */
     public function tearDown()
     {
         fwrite(STDOUT, "===> 这里放一些任务处理之后需要处理的逻辑代码");
     }
}