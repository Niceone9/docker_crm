<?php
phpinfo();
exit;

echo ceil('57799.99');

$money=100;

$fandian=20;

echo $money/(1-($fandian/100));

exit;
echo $on=md5('lzad.cc00:16:3e:0c:ec:bcmoushi.com');exit;
for ($i=1;$i<=10;$i++)
{
    sleep(1);
    error_log('/r /n'.date('Y-m-d H:i:s').'错误'.$i,3,hjdlog.log);
}
exit;
class TaskServer{

    private $_serv;
    private $_run;

    public function __construct()
    {
        //创建一个服务
        $this->_serv=new Swoole\Server("127.0.0.1",9501);
        $this->_serv->set([
           'worker_num'=>2,//开启worker进程数量
           'daemonize'=>false,//守护进程化
           'log_file'=>__DIR__.'/server.log',//配置运行时文件

           'task_worker_num'=>2,//配置Task进程的数量
           'max_request'=>5000,//worker进程的最大任务数
           'task_max_request'=>5000,//设置task进程的最大任务数。一个task进程在处理完超过此数值的任务后将自动退出。这个参数是为了防止PHP进程内存溢出
           'open_eof_check'=>true,//打开EOF 检测
           'package_efo'=>"\r\n",//设置EOF
           'open_eof_split'=>true,//自动分包
        ]);
        $this->_serv->on('Connect',[$this,'onConnect']);
        $this->_serv->on('Receive',[$this,'onReceive']);
        $this->_serv->on('WorkerStart',[$this,'onWorkerStart']);
        $this->_serv->on('Task',[$this,'onTask']);
        $this->_serv->on('Finish',[$this,'onFinish']);
        $this->_serv->on('Close',[$this,'onClose']);

    }

    public function onConnect($serv,$fd,$fromId){
        echo $fd.'--'.$fromId;
    }

    public function onWorkerStart($serv,$workerId){

    }

    public function onReceive($serv,$fd,$fromId,$data)
    {
        echo $data;
    }

    public function onTask($serv,$taskId,$fromId){

    }

    public function onFinish($serv,$taskId,$data)
    {

    }

    public function onClose($serv,$fd,$fromId)
    {

    }
    /*
     *
     *
     *
     * */

    public function start(){
        $this->_serv->start();
    }


}

$reload=new TaskServer;
$reload->start();

?>