<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/9/19
 * Time: 18:20
 */

namespace common\components;

use backend\models\Account;
use backend\models\ArticleClass;
use backend\models\AuthAssignment;
use backend\models\AuthItem;
use backend\models\ContractRelevance;
use backend\models\Department;
use backend\models\JdContract;
use backend\models\RenewHuikuan;
use Yii;
use yii\db\Query;

class Helper
{
    public static $str;
    public static $usersarray;

    public static function checkedMobile($mobile)
    {
        return $mobile;
    }

    public function beforeAction()
    {
        exit;
    }

    //帮助类
    //递归查询用户组里的所有id
    public static function assignments($userid = '')
    {
        if ($userid == '') {
            $userid = Yii::$app->user->getId();
        }
        //先把数组设置为空 以免出现数据重复的情况
        self::$str = [];
        $auth = Yii::$app->getAuthManager();
        //获取所属组 如是销售则只显示自己的客户
        $user_assignment = $auth->getAssignments($userid);
        $assignments = [];
        //获取自己的角色 注意此方法只用于用户只有一个角色

        foreach ($user_assignment as $key => $value) {
            if ($key) {
                self::$str[] = $key;
                $assignments = self::assignments_names($key);//递归获取角色下面包含的角色
            }
        }
        //查询属于这些角色的用户
        $rows = (new Query())
            ->select(['user_id'])
            ->from('auth_assignment')
            ->where(['item_name' => $assignments])
            ->column();

        //如果是最底层的----- 也就是说所属角色下面没有孩子
        if (count($assignments) == 1) {
            //直接返回id
            return array($userid);
        } else {
            return $rows;
        }


    }

    //根据角色名获取角色下 所有用户 包含 子角色的用户
    public static function role_name_users($name)
    {
        $assignments = self::assignments_names($name);//递归获取角色下面包含的角色
        if (!$assignments) {
            $assignments = [];
        }
        $assignments = array_merge(array($name), $assignments);

        //查询属于这些角色的用户
        $rows = (new Query())
            ->select(['user_id'])
            ->from('auth_assignment')
            ->where(['item_name' => $assignments])
            ->column();
        return $rows;
    }

    //递归获取此角色下的所有角色 并以数组形式返回
    public static function assignments_names($name)
    {
        //查询parent （父亲名称）是$name 的角色
        $rows = (new Query())
            ->from('auth_item_child')
            ->join('a left join', 'auth_item b', 'a.child=b.name')
            ->where(['a.parent' => $name, 'b.type' => 1])
            ->all();

        //循环角色
        foreach ($rows as $key => $val) {
            //$a.=$val['child']; //为什么用$a不管用呢,大概是因为重复执行方法的时候会覆盖$a;

            self::$str[] = $val['child'];
            //如果这个角色有孩子 则 递归执行 取到孩子的角色
            if ($val['child'] != '') {
                //以数组形式记录角色
                self::assignments_names($val['child']);
            }
        }

        return self::$str;

    }


    //得到文件的后缀名
    public static function getFileSuffix($file)
    {
        $pics = explode('.', $file);
        $num = count($pics);
        return strtolower($pics[$num - 1]);
    }

    public static function hjd_post_curl($url, $postdate)
    {

        $data_string = json_encode($postdate);

        //$data_string='{"content":"<p>\u65e0\u60c5\u4e8c\u4e03\u989d<br><\/p>","template":"default","title":"123\u9a71\u868a\u5668\u6587","to":"2885430949@qq.com"}';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($data_string))
        );
        ob_start();
        $response = curl_exec($ch);

        $return_content = ob_get_contents();
        ob_end_clean();
        if ($response === FALSE) {
            echo "cURL 具体出错信息: " . curl_error($ch);
            exit;
        }
        $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $obj = json_decode($return_content);
        return $obj;
    }

    public static function hjd_get_curl($url)
    {

        //初始化
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        // 执行后不直接打印出来
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        // 跳过证书检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // 不从证书中检查SSL加密算法是否存在
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        //执行并获取HTML文档内容
        $output = curl_exec($ch);

        //释放curl句柄
        curl_close($ch);

        return $output;
    }


    /*
    //计算实付金额

    */

    public static function shifu_fun($show_money, $mei_fandian, $dl_fandian)
    {
        $fandian = ($mei_fandian + 100) / 100; //媒体返点
        $dlfandian = (100 - $dl_fandian) / 100; //媒体返点
        $cost = $show_money / $fandian * $dlfandian; //续费成本

        return round($cost,2);
    }


    /*
     * 列表显示type
     *
     * */
    public static function list_type($role = '')
    {
        $type = 3;
        //
        //  echo Yii::$app->getRequest();
        //exit;
        $actionID = Yii::$app->controller->action->getUniqueId();
        $actionID = "/" . $actionID;
        if ($role != '') {
            $actionID = $role;
        }
        //如果有权限访问 则查询列表权限 如果未查到则返回看全部
        //查询用户角色
        $user = \Yii::$app->getUser();
        //取最大的权限
        $rlist = AuthItem::find()->select('a.name,b.type,c.item_name,c.user_id')->join('a left join', 'auth_list_crm b', 'a.name=b.route')->leftJoin('auth_assignment c', 'c.item_name=b.role')->where(['c.user_id' => $user->id, 'a.name' => $actionID])->orderBy('b.type desc')->asArray()->one();


        if ($rlist) {
            $type = $rlist['type'];
        }

        return $type;
    }

    //获取12个月的开始时间和结束时间参数1 得到几月数据  参数2 从上周几开始计算，周期  参数3 指定开始时间 没有则默认今天
    public static function teodate_month_12($to, $strdate = '')
    {
        //如果没有指定日期则默认当前日期
        if ($strdate == '') {
            $strdate = date('Y-01-01');
        }

        $a = strtotime($strdate);

        //获取几周日期
        for ($i = 0; $i < $to; $i++) {
            $start = strtotime(" +$i month", $a);//起始时间;
            $array[$i]['start'] = date('Y-m-d', $start);
            $enddate = date("Y-m-d", strtotime("+1 month  ", $start));
            if ($enddate > date("Y-m-d")) {
                //$enddate=date("Y-m-d",strtotime("-1 day"));
                //  $enddate=date("Y-m-d");
            }
            $array[$i]['end'] = $enddate;//结束日期
        }
        //echo $a;
        return $array;
    }


    public static function teodate_month_121($to, $strdate = '')
    {
        //如果没有指定日期则默认当前日期
        if ($strdate == '') {
            $strdate = date('Y-01-01');
        }

        $a = strtotime($strdate);

        //获取几周日期
        for ($i = 0; $i < $to; $i++) {
            $start = strtotime(" +$i month", $a);//起始时间;
            $array[$i]['start'] = date('Y-m-d', $start);
            $enddate = date("Y-m-d", strtotime("+1 month -1 day  ", $start));
            if ($enddate > date("Y-m-d")) {
                //$enddate=date("Y-m-d",strtotime("-1 day"));
                //  $enddate=date("Y-m-d");
            }
            $array[$i]['end'] = $enddate;//结束日期
        }
        //echo $a;
        return $array;
    }

    //获取月的开始时间和结束时间
    function teodate_month()
    {
        $array['start'] = date("Y-m-d", mktime(0, 0, 0, date('m'), 1, date('Y')));
        $enddate = date("Y-m-d");
        $array['end'] = $enddate;//结束日期
        //echo $a;
        return $array;
    }

    function date_daye_j7()
    {
        $array['start'] = $time_start = strtotime(date("Y-m-d") . "-7 day");
        $array['end'] = $time_end = strtotime(date("Y-m-d"));
        return $array;
    }

//获取周的开始时间和结束时间参数1 得到几周数据  参数2 从上周几开始计算，周期  参数3 指定开始时间 没有则默认今天
    function teodate_week($to, $zhouji, $strdate = '')
    {
        //如果没有指定日期则默认当前日期
        if ($strdate == '') {
            $strdate = date('Y-m-d');
        }
        $a = strtotime($strdate . "+1 day"); //因为是获取的上周几 所以给她加一天，比如 今天周一，要获取上周一，则 改认为今天是周二这样去读
        //获取几周日期
        /*
        for($i=0;$i<$to;$i++)
        {

        }
        */
        $bb = 0;
        for ($i = $to; $i > 0; $i--) {
            $start = strtotime("this $zhouji -$i week", $a);//起始时间;
            $array[$bb]['start'] = date('Y-m-d', $start);
            $enddate = date("Y-m-d", strtotime("+1 week ", $start));
            if ($enddate > date("Y-m-d")) {
                //$enddate=date("Yq-m-d",strtotime("-1 day"));
                //$enddate=date("Y-m-d");
            }
            $array[$bb]['end'] = $enddate;//结束日期
            $bb++;
        }

        //echo $a;

        return $array;
    }

    //判断是几维数组
    function arrayLevel($arr)
    {
        $al = array(0);
        function aL($arr, &$al, $level = 0)
        {
            if (is_array($arr)) {
                $level++;
                $al[] = $level;
                foreach ($arr as $v) {
                    aL($v, $al, $level);
                }
            }
        }

        aL($arr, $al);
        return max($al);
    }

    function getmaxdim($vDim)
    {
        if (!is_array($vDim)) return 0;
        else {
            $max1 = 0;
            foreach ($vDim as $item1) {
                $t1 = $this->getmaxdim($item1);
                if ($t1 > $max1) $max1 = $t1;
            }
            return $max1 + 1;
        }
    }

    //递归查询部门里的所有用户id
    public static function asbumen($userid = '')
    {
        if ($userid == '') {
            $userid = Yii::$app->user->getId();
        }
        //先把数组设置为空 以免出现数据重复的情况
        self::$usersarray = [];


        //查询用户所属部门
        $user_assignment = AuthAssignment::find()->where(['user_id' => $userid])->asArray()->all();

        $assignments = [];
        //获取自己的角色 注意此方法只用于用户只有一个角色

        foreach ($user_assignment as $key => $value) {


            self::$usersarray[] = $value['department'];
            self::bumen_xia($value['department']);//递归获取部门下面包含的部门

        }


        //查询属于这些角色的用户
        $rows = (new Query())
            ->select(['user_id'])
            ->from('auth_assignment')
            ->where(['department' => self::$usersarray])
            ->column();


        if (count($rows) > 1) {

            return $rows;
        } else {
            return $userid;
        }


    }


    //递归获取此角色下的所有角色 并以数组形式返回
    public static function bumen_xia($parent_id)
    {

        //查询parent （父亲名称）是$name 的角色
        $rows = Department::find()->where(['parent_id' => $parent_id])->asArray()->all();


        if (count($rows) > 0) {
            //循环部门
            foreach ($rows as $key => $val) {
                //$a.=$val['child']; //为什么用$a不管用呢,大概是因为重复执行方法的时候会覆盖$a;
                self::$usersarray[] = $val['id'];
                //以数组形式记录角色
                self::bumen_xia($val['id']);
            }

        } else {
            return;
        }


    }


    //递归获取此角色下的所有角色 并以数组形式返回
    public static function bumen_users($parent_id)
    {
        $a = [];
        global $a;
        //查询parent （父亲名称）是$name 的角色
        $rows = Department::find()->where(['parent_id' => $parent_id])->asArray()->all();
        $a[] = $parent_id;

        if (count($rows) > 0) {
            //循环部门
            foreach ($rows as $key => $val) {
                // echo $val['id'];
                $a[] = $val['id'];
                //echo $val['id'];
                //$a.=$val['child']; //为什么用$a不管用呢,大概是因为重复执行方法的时候会覆盖$a;

                //以数组形式记录角色
                self::bumen_users($val['id']);
            }

        } else {
            return $a;
        }

        return $a;

    }


    //递归获取此角色下的所有角色 并以数组形式返回
    public static function article_classtree($parent_id)
    {
        $a = [];
        global $a;
        //查询parent （父亲名称）是$name 的角色
        $rows = ArticleClass::find()->where(['parent_id' => $parent_id])->asArray()->all();
        $a[] = $parent_id;

        if (count($rows) > 0) {
            //循环部门
            foreach ($rows as $key => $val) {
                // echo $val['id'];
                $a[] = $val['id'];
                //echo $val['id'];
                //$a.=$val['child']; //为什么用$a不管用呢,大概是因为重复执行方法的时候会覆盖$a;

                //以数组形式记录角色
                self::bumen_users($val['id']);
            }

        } else {
            return $a;
        }

        return $a;

    }


    //获取用户所拥有的接口function 访问权限
    public static function get_roles()
    {
        $userId = Yii::$app->user->id;
        $routes = [];
        $manager = Yii::$app->getAuthManager();
        foreach ($manager->getPermissionsByUser($userId) as $name => $value) {
            if ($name[0] === '/') {
                $routes[] = $name;
            }
        }
        return $routes;
    }

    //获取传入时间倒15天日期 开始时间和结束时间
    function day_7($start_time = '')
    {
        //如果没有指定日期则默认当前日期
        if ($start_time == '') {
            $start_time = date('Y-m-d');
        }
        //加一天
        $strdate = date("Y-m-d", strtotime($start_time . " -7 day"));

        for ($i = 0; $i <= 6; $i++) {
            $array[$i]['start'] = date("Y-m-d", strtotime($strdate . " +$i day"));
            $array[$i]['end'] = date("Y-m-d", strtotime($array[$i]['start'] . " +1 day"));
        }


        return $array;

    }

    //获取传入时间倒15天日期 开始时间和结束时间
    function day_7d($start_time = '')
    {
        //如果没有指定日期则默认当前日期
        if ($start_time == '') {
            $start_time = date('Y-m-d');
        }
        //加一天
        $strdate = date("Y-m-d", strtotime($start_time . " -7 day"));

        for ($i = 6; $i >= 0; $i--) {
            $array[$i]['start'] = date("Y-m-d", strtotime($strdate . " +$i day"));
            $array[$i]['end'] = date("Y-m-d", strtotime($array[$i]['start'] . " +1 day"));
        }


        return $array;

    }

    //修改账户总续费信息
    function updateaccountpay($accountid)
    {
        $accountinfo = Account::findOne($accountid);
        $xfaccountlistid = Account::find()
            ->select('id')
            ->where(['a_users' => $accountinfo->a_users, 'avid' => $accountinfo->avid])
            ->asArray()
            ->column();


        $xf = RenewHuikuan::find()->where(['account' => $xfaccountlistid, 'advertiser' => $accountinfo->avid, 'is_ultimate_shenhe' => '1', 'payment_type' => [1, 2]])->sum('show_money');
        $tk = RenewHuikuan::find()->where(['account' => $xfaccountlistid, 'advertiser' => $accountinfo->avid, 'is_ultimate_shenhe' => '1', 'payment_type' => [15]])->sum('show_money');
        $xf = $xf ? $xf : 0;
        $tk = $tk ? $tk : 0;

        Account::updateAll(['total_pay' => ($xf - $tk)], ['id' => $xfaccountlistid]);

    }

    //计算账户显示金额 现金，合同id

    function show_money($money, $contract_id, $web_show_money = '')
    {

        $contract_info = JdContract::findOne($contract_id);
        if (!$contract_info['fd_type']) {
            throw new \Exception('错误!没有返点类型无法计算账户显示金额!');
        }

        $contract_ren = ContractRelevance::find()
            ->select('a.*,b.fk_type')
            ->join('a left join', 'jd_product_line b', 'b.id=a.product_line')
            ->where(['a.contract_id' => $contract_id])
            ->asArray()
            ->one();

        //如果是
        if ($contract_ren['fk_type'] == '1' && $contract_ren['fk_type'] != '') {
            return true;
        }
        //如果合同的返点类型是账户返点则

        $show_money_db = round($money * (1 + ($contract_ren['fandian'] / 100)), 2) / (1 - ($contract_ren['xj_fandian'] / 100));

        /*
        if($contract_info->fd_type==1)
        {

        }elseif ($contract_info->fd_type==2)
        {
            $show_money_db=round($money/(1-($contract_ren['fandian']/100)),2);
        }*/

        if ($web_show_money == '') {
            return $show_money_db;
            exit;
        }
        // echo $show_money_db.'---'.$web_show_money;exit;
        if (($show_money_db != $web_show_money)) {
            if ($show_money_db > $web_show_money) {
                if ($show_money_db - $web_show_money > 0.05) {
                    throw new \Exception('错误!前端账户显示金额跟系统计算不一致！（应为：）' . $show_money_db);
                }
            }
            if ($show_money_db < $web_show_money) {
                if ($web_show_money - $show_money_db > 0.05) {
                    throw new \Exception('错误!前端账户显示金额跟系统计算不一致！（应为：）' . $show_money_db);
                }
            }


        }
    }




    function scanFile($dir)
    {
        if (is_dir($dir)) {
            $files = array();
            $child_dirs = scandir($dir);
            foreach ($child_dirs as $child_dir){

                if ($child_dir != '.' && $child_dir != '..') {
                    if (is_dir($dir . '/' . $child_dir)) {
                        $files[$child_dir] = Yii::$app->hjd->scanFile($dir . '/' . $child_dir);
                    } else {
                        $files[] = $child_dir;
                    }
                }
            }
            return $files;
        } else {
            return $dir;
        }
    }
    public $pathh=array();

    function read_all ($dir,$one=''){
        if($one!='')
        {
            $this->pathh=[];
        }
        $pathh=array();
     if(!is_dir($dir)) return false;
     $handle = opendir($dir);
     if($handle){
           while(($fl = readdir($handle)) !== false){
             $temp = $dir.DIRECTORY_SEPARATOR.$fl;
            //如果不加  $fl!='.' && $fl != '..'  则会造成把$dir的父级目录也读取出来
            if(is_dir($temp) && $fl!='.' && $fl != '..'){
                        //echo '目录：'.$temp.'<br>';
                Yii::$app->hjd->read_all($temp);
            }else{
               if($fl!='.' && $fl != '..'){
                   array_push($this->pathh,$temp);
                    //echo $temp."<br>";
                }
            }
         }
    }
        return $this->pathh;
    }


    function date_is_true($date){
        $a= strtotime($date);
        if(date('Y-m-j',$a)!=$date && date('Y-m-d',$a)!=$date)
        {
            //如果是无效日期就转为此月最后一天

            $dataarr=explode('-',$date);

            $y=$dataarr[0];
            $m=$dataarr[1]+1;
            $date2=$y.'-'.$m.'-01';

            $formdate=strtotime($date2.' -1 day');

            return date('Y-m-d',$formdate);
        }else
        {
            return  $date;
        }
    }


    /**
     * 获取指定日期段内每一天的日期
     * @param  Date  $startdate 开始日期
     * @param  Date  $enddate   结束日期
     * @return Array
     */
    function getDateFromRange($startdate, $enddate){

        $stimestamp = strtotime($startdate);
        $etimestamp = strtotime($enddate);

        // 计算日期段内有多少天
        $days = ($etimestamp-$stimestamp)/86400+1;

        // 保存每天日期
        $date = array();

        for($i=0; $i<$days; $i++){
            $date[] = date('Y-m-d', $stimestamp+(86400*$i));
        }

        return $date;
    }


}