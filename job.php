<?php
class job
{
    /**
     * 执行任务的前置条件
     */
    public function setUp()
    {
        fwrite(STDOUT, "===> 这里放一些任务执行之前需要处理的逻辑代码 \n");
    }

    public function perform()
    {
        fwrite(STDOUT, "这里可以接受参数，比方说我们取一下name: ".$this->args["name"]."\n");
        fwrite(STDOUT, "这里是具体的处理订单的逻辑代码 \n");
    }

    /**
     * 执行任务的后置条件
     */
     public function tearDown()
     {
         fwrite(STDOUT, "===> 这里放一些任务处理之后需要处理的逻辑代码 \n");
     }
}