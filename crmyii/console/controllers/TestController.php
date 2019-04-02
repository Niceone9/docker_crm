<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/12/28
 * Time: 10:25
 */
namespace console\controllers;
use backend\models\Account;

use backend\modules\api\controllers\LinuxTimeController;
use yii;
class TestController extends \yii\console\Controller
{
    private $_serv;

    private function _prepare()
    {
        $this->_serv = new \Swoole\Server('127.0.0.1', 9501);
        $this->_serv->set([
            'worker_num' => 2,//设置启动的worker进程数。这里设置为CPU的1-4倍最合理
            'daemonize'=>1,//守护进程化。设置daemonize => 1时，程序将转入后台作为守护进程运行。长时间运行的服务器端程序必须启用此项
            'log_file' => __DIR__ . '/swoole_server.log',//指定swoole错误日志文件。在swoole运行期发生的异常信息会记录到这个文件中。默认会打印到屏幕。
            'task_worker_num' => 2,//配置Task进程的数量，配置此参数后将会启用task功能。所以Server务必要注册onTask、onFinish2个事件回调函数。如果没有注册，服务器程序将无法启动。
            'max_request' => 5000,//设置worker进程的最大任务数，默认为0，一个worker进程在处理完超过此数值的任务后将自动退出，进程退出后会释放所有内存和资源。
            'task_max_request' => 5000,//设置task进程的最大任务数。一个task进程在处理完超过此数值的任务后将自动退出。这个参数是为了防止PHP进程内存溢出。如果不希望进程自动退出可以设置为0。
            'open_eof_check' => true, //打开EOF检测，此选项将检测客户端连接发来的数据，当数据包结尾是指定的字符串时才会投递给Worker进程。否则会一直拼接数据包，直到超过缓存区或者超时才会中止。
            'package_eof' => "\r\n", //与 open_eof_check 或者 open_eof_split 配合使用，设置EOF字符串。
            'open_eof_split' => true, //启用EOF自动分包。当设置open_eof_check后，底层检测数据是否以特定的字符串结尾来进行数据缓冲。但默认只截取收到数据的末尾部分做对比。这时候可能会产生多条数据合并在一个包内。
        ]);
        $this->_serv->on('Connect', [$this, 'onConnect']);
        $this->_serv->on('Start', [$this, 'onStart']);
        $this->_serv->on('Receive', [$this, 'onReceive']);
        $this->_serv->on('WorkerStart', [$this, 'onWorkerStart']);
        $this->_serv->on('Task', [$this, 'onTask']);
        $this->_serv->on('Finish', [$this, 'onFinish']);
        $this->_serv->on('Close', [$this, 'onClose']);
    }

    public function actionStart()
    {
        $this->_prepare();
        $this->_serv->start();
    }

    public function onConnect($serv, $fd, $fromId)
    {
        echo '已经开始swoole 服务';
    }

    public function onStart()
    {
        /*
        $logObject = Yii::getLogger();
        $logObject->log("This is a warning message.", \yii\log\Logger::LEVEL_WARNING);
        $logObject->flush(true);
*/
        // yii::warning('This is a warning message hjd');
    }

    public function onWorkerStart($serv, $workerId)
    {


        //print_r(get_included_files());
    }

    //接收到数据时回调此函数，发生在worker进程中。
    /*
     *
    $server，swoole_server对象
    $fd，TCP客户端连接的唯一标识符
    $reactor_id，TCP连接所在的Reactor线程ID
    $data，收到的数据内容，可能是文本或者二进制内容
     * */
    public function onReceive($serv, $fd, $fromId, $data)
    {
        $data = $this->unpack($data);
        echo '任务开始' . PHP_EOL;
        if (!empty($data['event'])) {
            $serv->task(array_merge($data, ['fd' => $fd]));
        }
        echo '任务已经在执行，继续往下' . PHP_EOL;
    }

    public function onTask($serv, $taskId, $fromId, $data)
    {
        try {
            switch ($data['event']) {
                case 'import_xiaohao':
                    //导入消耗

                    if(!Account::importxiaohao($data['file'])){
                        echo '导入失败';
                    }
                    //对应真实消耗
                    LinuxTimeController::actionAccount_coust_toren();
                    break;
                default:
                    break;

            }
        } catch (\Exception $e) {
            throw new \Exception('task exeception:' . $e->getMessage());
        }

        return 'task end';

    }

    public function onFinish($serv, $taskId, $data)
    {
        echo '任务执行成功' . date('Y-m-d H:i:s');
        return true;
    }

    public function onClose()
    {

    }

    public function actionIndex()
    {
        var_dump(Account::importxiaohao('Uploads/linshi/20180228/1519801413807036643664.xlsx'));
        echo $_SERVER['DOCUMENT_ROOT'];
        //yii::warning('this is a warning message');
    }

    public function unpack($data)
    {
        $data = str_replace("\r\n", '', $data);
        if (!$data) {
            return false;
        }

        $data = json_decode($data, true);//将json 转换成数组
        if (!$data || !is_array($data)) {
            return false;
        }
        return $data;

    }
    /*
     *
     *
     *
     * public function actionIndex($name,$age){
        //echo 'This is my first console application';
        echo "my name is {$name}";
        echo "my age is {$age}";
        return self::EXIT_CODE_NORMAL;
    }
     *
     * */
}