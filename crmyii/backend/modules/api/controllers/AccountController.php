<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/10/20
 * Time: 16:12
 */

namespace backend\modules\api\controllers;


use backend\models\Account;
use backend\models\AccountCost;
use backend\models\AccountCostReal;
use backend\models\ContractRelevance;
use backend\models\File;
use backend\models\JdContract;
use backend\models\JdCustomer;
use backend\models\ProductLine;
use backend\models\QudaoDemand;
use backend\models\RenewHuikuan;
use backend\models\Tags;
use backend\models\Yuming;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;

use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use backend\components\Quanxian;
use Yii;
set_time_limit(0);

class AccountController extends ActiveController
{
    public $modelClass='backend\models\Account';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                //HttpBasicAuth::className(),
                HttpBearerAuth::className(),
                QueryParamAuth::className(),
            ],
        ];
        $behaviors['myBehavior']=[
            'class'=>Quanxian::className(),
        ];
        $behaviors['rateLimiter']['enableRateLimitHeaders'] = false;
        return $behaviors;
    }

    //账户列表
    public function  actionAccount_list($id){
        $data['list']=Account::account_list($id);
        $data['code']=200;
        return $data;
    }

    //返回媒介合同下的账户列表
    public function actionAccount_list_m($id){
        $data['list']=Account::account_list_m($id);
        $data['code']=200;
        return $data;
    }

    //账户详情页面
    public function actionAccount_info($id){
        $data['data']=Account::account_info($id);
        $data['qudaolist']=JdCustomer::qudaolist();
        $data['yuminglist']=Yuming::account_yuminglist($id);
        $data['account_history']=Account::account_history($id);

        $data['sem']=Account::yushansem($data['data']['a_users']);

        $data['code']=200;
        return $data;
    }

    //账户历史记录
    public function actionAccount_history($id)
    {
        $data['account_history']=Account::account_history($id);
        $data['code']=200;
        return $data;
    }
    //新增账户 接受合同id
    public function actionAccount_add($contract_id)
    {
        //产品线
        $data['code']='200';
        $data['contract_info']=JdContract::contractinfonomt($contract_id);
        $data['qudao_list']=JdCustomer::qudaolist();
        return $data;
    }

    //批量添加账户
    public function actionAccount_add_pi()
    {
        //账户
        $postdata=\Yii::$app->request->post();


        //产品线详情

        $contract_id=$postdata['contract_id'];
        $contractin=ContractRelevance::find()->where("contract_id=$contract_id")->one();
        $prlin=ProductLine::findOne($contractin->product_line);



        foreach ($postdata['a_users'] as $key=>$val)
        {
            //获取相同账户
            $acc_copy=Account::find()
                ->select('a.*,b.advertiser as advertiser')
                ->join('a left join','jd_contract b','a.contract_id=b.id')
                ->where("a.a_users ='".$val."' and a.endtime='4092599349' and a.prlin_id=".$prlin['id'])
                ->orderBy("id desc")
                ->asArray()
                ->one();

            $model=new Account();
            $model->load(\Yii::$app->getRequest()->getBodyParams(),'');
            $model->a_users=$val;

            if($model->ctime==''){

                $model->ctime=time();
            }else
            {
                $model->ctime = strtotime($model->ctime);
            }

            //格式化账户名称
            $model->a_users=trim($model->a_users);//删除左右两侧空格
            $model->a_users=str_replace(' ','',$model->a_users);





            $model->prlin_id=$contractin->product_line;

            if(\Yii::$app->params['yushan_synchronizing']=='true') {

                $url = \Yii::$app->params['yushancom']."sem/createhu";

                $post_data = array(
                    "name" => $postdata['appname'],
                    "account_type" => $prlin['yushan_type'],
                    "account_name" => $val,
                    "account_password" => $postdata['a_password'],
                    "account_appid" => 'no token',
                    "account_status" => '1',
                    "fd_rate" => $postdata['fandian'] / 100,
                    "view_type" => '120001',
                    "server_type" => $postdata['server_type'],
                    "sem"=>'3355'
                    // "qudao_id" => '',
                );


                //如果是正式账户
                if ($postdata['state'] == 0) {
                    //账户所属公司不等于原账户公司
                    if ($acc_copy['advertiser'] != $postdata['avid']) {

                        $yushan_data = \Yii::$app->hjd->hjd_post_curl($url, $post_data);
                        $yushan_id = $yushan_data->data->appid;
                        if ($yushan_id != '') {
                            $model->appid = $yushan_id;
                        } else {
                            $data['code'] = '500';
                            $data['msg'] = '新增账户羽扇平台数据同步失败';
                            $data['data'] = $yushan_data;
                            $data['crmdata'] = $post_data;
                            return $data;
                        }
                    } else {
                        //如果新增账户所属公司是原账户的公司
                        $model->appid = $acc_copy['appid'];
                    }
                }
            }
            //如果之前有这个账户就结束掉
            if($acc_copy['id'])
            {
                Account::updateAll(['endtime'=>time()],['id'=>$acc_copy['id']]);
                //并且给新账户赋值旧账户的 媒体付款类型 时间 周期
                $model->mt_payment_type=$acc_copy['mt_payment_type'];
                $model->dk_date=$acc_copy['dk_date'];
                $model->dk_zhouqi=$acc_copy['dk_zhouqi'];
            }




            $model->save();



        }


        $data['code']='200';
        $data['msg']='添加成功';
        return $data;


    }

    //新增账户返回
    public function actionAccount_addru(){
        //账户
        $postdata=\Yii::$app->request->post();

        //产品线详情
        $contract_id=$postdata['contract_id'];
        $contractin=ContractRelevance::find()->where("contract_id=$contract_id")->one();
        $prlin=ProductLine::findOne($contractin->product_line);


        //获取相同账户
        $acc_copy=Account::find()
            ->select('a.*,b.advertiser as advertiser')
            ->join('a left join','jd_contract b','a.contract_id=b.id')
            ->where("a.a_users ='".$postdata['a_users']."' and a.endtime='4092599349' and prlin_id=".$prlin['id'])
            ->orderBy("id desc")
            ->asArray()
            ->one();



        $model=new Account();
        $model->load(\Yii::$app->getRequest()->getBodyParams(),'');

        if($model->ctime==''){

            $model->ctime=time();
        }else
        {
            $model->ctime = strtotime($model->ctime);
        }

        //格式化账户名称
        $model->a_users=trim($model->a_users);//删除左右两侧空格
        $model->a_users=str_replace(' ','',$model->a_users);






        $model->prlin_id=$contractin->product_line;


       if(\Yii::$app->params['yushan_synchronizing']=='true') {

           $url = \Yii::$app->params['yushancom']."sem/createhu";
           //$url = "http://www.yushanapp.com/sem/createhu";
           // $url ="http://192.168.199.171:5281/sem/createhu";
           $post_data = array(
               "name" => $postdata['appname'],
               "account_type" => $prlin['yushan_type'],
               "account_name" => $postdata['a_users'],
               "account_password" => $postdata['a_password'],
               "account_appid" => 'no token',
               "account_status" => '1',
               "fd_rate" => $postdata['fandian'] / 100,
               "view_type" => '120001',
               "server_type" => $postdata['server_type'],
               "sem"=>'3355'
               // "qudao_id" => '',
           );


           //如果是正式账户
           if ($postdata['state'] == 0) {
               //账户所属公司不等于原账户公司
               if ($acc_copy['advertiser'] != $postdata['avid']) {

                   $yushan_data = \Yii::$app->hjd->hjd_post_curl($url, $post_data);
                   $yushan_id = $yushan_data->data->appid;
                   if ($yushan_id != '') {
                       $model->appid = $yushan_id;
                   } else {
                       $data['code'] = '500';
                       $data['msg'] = '新增账户羽扇平台数据同步失败';
                       $data['data'] = $yushan_data;
                       $data['crmdata'] = $post_data;
                       return $data;
                   }
               } else {
                   //如果新增账户所属公司是原账户的公司
                   $model->appid = $acc_copy['appid'];
               }
           }
       }
        //如果之前有这个账户就结束掉
        if($acc_copy['id'])
        {
            Account::updateAll(['endtime'=>time()],['id'=>$acc_copy['id']]);
            //并且给新账户赋值旧账户的 媒体付款类型 时间 周期
            $model->mt_payment_type=$acc_copy['mt_payment_type'];
            $model->dk_date=$acc_copy['dk_date'];
            $model->dk_zhouqi=$acc_copy['dk_zhouqi'];
        }




        if($model->save())
        {
            //如果之前有这个账户就结束掉
           // $Refund=where("a_users ='".$postdata['a_users']."' and a.endtime='4092599349'")->save(array("endtime"=>time()));


            $data['code']='200';
            $data['msg']='添加成功';
            $data['data']=$model;
        }else
        {
            $data['code']='500';
            $data['msg']='添加失败';
            $data['data']=$model->errors;
        }

        return $data;

    }

    //修改账户信息
    public function actionUpdateaccount($id){

         return Account::updataaccount($id);

    }

    //查询账户是否创建过  修改
    public function actionCheckaccount_up($a_users,$thisid){
        //添加账户的时候 判断这个账户之前是否被添加过
        $is_j=Account::find()->where("a_users ='$a_users' and endtime='4092599349' and id !=$thisid")->count();
        return $is_j;
    }
    //查询账户是否创建过 创建
    public function actionCheckaccount_add($a_users){
        //添加账户的时候 判断这个账户之前是否被添加过
        $is_j=Account::find()->where("a_users ='$a_users'")->count();
        return $is_j;
    }



    //导入消耗
    public function actionImportxiaohao($file='')
    {


        $tr = \Yii::$app->db->beginTransaction();
        try {
            //写入续费
            $aa=Account::importxiaohao($file);
            if(count($aa['error_account_list'])>0)
            {
                return array('code'=>'500','data'=>$aa['error_account_list']);
                exit;
                //$errstr=implode(',',$aa['error_account_list']);
                //throw new \Exception($errstr);
            }

            //修改账户总消耗
            $sql='update jd_account set  total_cost = (select sum(cost) from jd_account_cost where account_name=jd_account.a_users and avid=jd_account.avid )';
            \Yii::$app->db->createCommand($sql);
            //Account::updateAll(['total_cost'=>]);


            $tr->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }

        return $aa;
        //执行异步任务


        //return array('code'=>200,'msg'=>'已经异步执行该操作');

    }
    //导入消耗
    public function actionImportxiaohao_bak($file='')
    {

        

        //执行异步任务

        $client=new \Swoole\Client(SWOOLE_SOCK_TCP);
        if(!$client->connect('127.0.0.1',9501))
        {
            $msg='swoole client connect failed.';
            throw new \Exception('Error:'.$msg);
        }
        $dataarray=array('event'=>'import_xiaohao','file'=>$file);
        $dataarray= json_encode($dataarray) . "\r\n";
        $client->send($dataarray);
        return array('code'=>200,'msg'=>'已经异步执行该操作');

    }



    //添加账户消耗
    public function actionAddaccountcoust(){
        $model=new AccountCost();

        if($model->load(\Yii::$app->getRequest()->getBodyParams(),'') && $model->save()){
            return array('code'=>'200','msg'=>'录入成功','date'=>$model);
        }else
        {
            return array('code'=>'500','msg'=>'录入失败','date'=>$model->errors);
        }
    }

    //消耗列表
    public function actionCost_list(){
        //return AccountCost::cost_list2();
        return AccountCost::cost_list();
    }



    /*

    //消耗对应续费合同
    public function actionAccount_coust_toren(){
        $list=AccountCost::find()->select(['id'])->where("left_cost>0 ")->asArray()->all();

        foreach ($list as $key=>$value)
        {
            self::actionAccount_cost_real($value['id']);
        }
       return array('code'=>'200','msg'=>'消耗对应成功');
    }

    //账户对应的真实消耗 账户id 公司id
    public static function ctionAccount_cost_real($id){
        $costinfo=AccountCost::findOne($id);

        $account=$costinfo->account_name;
        $avid=$costinfo->av_id;
        $date=$costinfo->date;
        $date=strtotime($date);
        //查询公司下的账户
        //查询是否有关联账户
        $Guanlian=Account::find()
            ->select("id,a_users,alias")
            ->where(['avid'=>$avid,'a_users'=>$account])
            ->andWhere("alias!=''")
            ->asArray()
            ->one();
        //如果有关联账户
        if($Guanlian!='')
        {
            $account_list=Account::find()->select("id,a_users,alias")->where(['avid'=>$avid,'alias'=>$Guanlian['alias']])->asArray()->all();
        }else{
            $account_list=Account::find()->select("id,a_users,alias")->where(['avid'=>$avid,'a_users'=>$account])->asArray()->all();
        }

        $account_id_list=ArrayHelper::getColumn($account_list,'id');

        //查询账户相关续费
        $renew_list=RenewHuikuan::find()
            ->where(['account'=>$account_id_list,'payment_type'=>array(1,2),'is_ultimate_shenhe'=>1])
            ->andWhere(['>','no_cost_xf',0])
            ->orderBy(['payment_time'=>'asc'])
            ->asArray()
            ->all();

        if(count($renew_list)<1)
        {
            return ;
        }

        foreach ($renew_list as $key=>$value)
        {
            $costinfo=AccountCost::findOne($id);
            //如果续费未消耗百度币 大于 消耗 （余额大于续费金额）停止循环并返回续费欠额为0
            if($value['no_cost_xf']-$costinfo['left_cost']>0) {
                //设置续费剩余消耗=续费剩余消耗-消耗
                RenewHuikuan::updateAll(['no_cost_xf'=>$value['no_cost_xf']-$costinfo['left_cost']],['id'=>$value['id']]);

                //查询之前有没有同合同同账户同一天的数据如果有就执行修改 如果没有就执行新增
                $accoust_cost_real=AccountCostReal::find()->where(['date'=>$costinfo->date,'a_user'=>$costinfo->account_name,'contract_id'=>$value['xf_contractid']])->one();

                if($accoust_cost_real['id'])
                {
                    $accoust_cost_real->cost=$accoust_cost_real->cost+$costinfo->left_cost;
                    $accoust_cost_real->no_back_cost=$accoust_cost_real->no_back_cost+$costinfo->left_cost;

                }else
                {
                    //增加真实消耗记录
                    $accoust_cost_real= new AccountCostReal();
                    $accoust_cost_real->date=$costinfo->date;
                    $accoust_cost_real->a_user=$costinfo->account_name;
                    $accoust_cost_real->cost=$costinfo->left_cost;
                    $accoust_cost_real->no_back_cost=$costinfo->left_cost;
                    $accoust_cost_real->contract_id=$value['xf_contractid'];
                }



                if(!$accoust_cost_real->save())
                {
                    var_dump($accoust_cost_real->errors);
                    exit;
                }
                //修改消耗剩下的费用为0
                AccountCost::updateAll(['left_cost'=>0],['id'=>$id]);
                return ;
            }else{
                //如果续费未消耗百度币 小于 消耗  （此回款余额不足以抹平此续费）则继续执行循环 并把此回款的余额为0
                //查询之前有没有同合同同账户同一天的数据如果有就执行修改 如果没有就执行新增
                $accoust_cost_real=AccountCostReal::find()->where(['date'=>$costinfo->date,'a_user'=>$costinfo->account_name,'contract_id'=>$value['xf_contractid']])->one();

                if($accoust_cost_real['id'])
                {
                    $accoust_cost_real->cost=$accoust_cost_real->cost+$costinfo->left_cost;
                    $accoust_cost_real->no_back_cost=$accoust_cost_real->no_back_cost+$costinfo->left_cost;

                }else {
                    //增加真实消耗记录
                    $accoust_cost_real = new AccountCostReal();
                    $accoust_cost_real->date = $costinfo->date;
                    $accoust_cost_real->a_user = $costinfo->account_name;
                    $accoust_cost_real->cost = $value['no_cost_xf'];
                    $accoust_cost_real->no_back_cost = $value['no_cost_xf'];
                    $accoust_cost_real->contract_id = $value['xf_contractid'];
                }

                if(!$accoust_cost_real->save())
                {
                    var_dump($accoust_cost_real->errors);
                    exit;
                }

                //设置回款余额
                RenewHuikuan::updateAll(['no_cost_xf'=>0],['id'=>$value[id]]);
                //修改续费欠额
                AccountCost::updateAll(['left_cost'=>$costinfo['left_cost']-$value['no_cost_xf']],['id'=>$id]);
            }
        }

        return array('code'=>'200','msg'=>'success');


    }
    */


    //check account is create
    public function actionCheck_date_add($date){
        $account_list=AccountCost::find()->where(['date'=>$date])->asArray()->all();

        $account_lista=ArrayHelper::getColumn($account_list,'account_name');

        //$accounst_
        $acl=Account::find()
            ->select('a_users')
            ->where(['endtime'=>'4092599349'])
            ->andWhere(['not in','a_users',$account_lista])
            ->asArray()
            ->all();
        $acl=ArrayHelper::getColumn($acl,'a_users');
        if(count($acl)>0){
            $acl="'".implode('\',\'',$acl)."'";
        }

        $sql="
select * from (
    select *,b.xufei-c.tuikuan-a.acxiaohao as yue from (
        select a_user,sum(cost) as acxiaohao from jd_account_cost_real where istuikuan=0 and a_user in(
        ".$acl."
        ) GROUP BY a_user
    ) a
    left join (
        select sum(a.no_cost_xf) as xufei,b.a_users from jd_renew_huikuan a left join jd_account b on a.account=b.id where a.payment_type in(1,2) and a.is_ultimate_shenhe=1 GROUP BY b.a_users
    ) b on a.a_user=b.a_users
    left join (
        select sum(a.show_money) as tuikuan,b.a_users from jd_renew_huikuan a left join jd_account b on a.account=b.id where a.payment_type in(15) and a.is_ultimate_shenhe=1 GROUP BY b.a_users
    ) c on a.a_user=c.a_users
) c where yue >0
        
        ";


        $posts = \Yii::$app->db->createCommand($sql)
            ->queryAll();
        return $posts;
        exit;
    }


    //账户金额列表
    public function actionAcccount_money($start,$end){

        $data=Account::account_money_list($start,$end);
        return $data;

    }

    //账户金额详情
    public function actionAcccount_money_info($start,$end){

        $data=Account::account_money_info($start,$end);
        return $data;

    }
    //账户金额详情
    public function actionAcccount_money_info_day($start,$end){

        $data=Account::account_money_info_day($start,$end);
        return $data;

    }
    //查询账户消耗对应情况 有哪些没对应完
    public function actionCoust_left_not($start,$end){
        $data=AccountCost::find()
            ->where('left_cost>0')
            ->andWhere("date>='$start' and date<='$end'")
            ->asArray()
            ->all();

        return array('code'=>'200','data'=>$data);
    }

    public function actionAd_account_gl($id){
        $data=Account::find()->select('alias')->where(['avid'=>$id])->andWhere('alias notnull')->groupBy('alias')->asArray()->all();
        return array('code'=>'200','data'=>$data);
    }

    public function actionAccount_upalias(){
        $accountid=\Yii::$app->request->post('account_list');
        $alias=\Yii::$app->request->post('alias');
        if(empty($accountid) or empty($alias))
        {
            return false;
        }
        if(Account::updateAll(['alias'=>$alias],['id'=>$accountid]))
        {
            return array('code'=>'200','msg'=>'修改成功');
        }else
        {
            return array('code'=>'500','msg'=>'修改失败');
        }


    }

    //结束账户
    public function actionAccount_end($id){

        if(Account::updateAll(['endtime'=>time()],['id'=>$id]))
        {
            return array('cdoe'=>'200','msg'=>'操作成功');
        }else
        {
            return array('cdoe'=>'500','msg'=>'操作失败');
        }

    }


    //修改账户的媒体付款方式，打款时间，打款周期
    public function actionAccount_upkuan($accountid,$type,$dk_date='',$dk_zhouqi=''){
        $account=Account::findOne($accountid);
        $account->mt_payment_type=$type;

        if($dk_date){
            $account->dk_date=$dk_date;
        }
        if($dk_zhouqi)
        {
            $account->dk_zhouqi=$dk_zhouqi;
        }



        if($account->save()
        ){
            return array('code'=>'200','msg'=>'修改账户成功','data'=>Account::findOne($accountid));
        }else
        {
            var_dump($account->errors);exit;
            return array('code'=>'500','msg'=>'修改账户失败');
        }
    }

    //新增域名解析
    public function actionAdd_jiexi_url(){
        if($data=Yuming::add()){
            return array('code'=>'200','msg'=>'success','data'=>$data);
        }else
        {
            return array('code'=>'500','msg'=>'fall');
        }
    }

    public function actionYuminglist(){
        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部
        if(Yii::$app->hjd->list_type()==1 or Yii::$app->hjd->list_type()=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['a.submitusers'=>$uid]];
        }elseif (Yii::$app->hjd->list_type()==2)
        {
            $uid=Yii::$app->hjd->asbumen();
            $userswhere=['or',['a.submitusers'=>$uid]];

        }elseif (Yii::$app->hjd->list_type()==3)
        {
            $userswhere=['>','a.submitusers',0];
        }
        //筛选条件
        $Search_str=\Yii::$app->request->get('Search_str');
        $searchwhere = ['or',['like','c.advertiser', $Search_str],['like','b.appname' ,$Search_str],['like','u.name',$Search_str],['like','b.a_users' ,$Search_str],['like','pr.name' ,$Search_str],['like','a.true_url' ,$Search_str]];
        //日期筛选条件
        $start=strtotime(\Yii::$app->request->get('start_date'));
        $end=strtotime(\Yii::$app->request->get('end_date'));
        $end=strtotime("+1 days",$end);

        //公司条件筛选
        $av_id=\Yii::$app->request->get('av_id');

        if($start and $end)
        {
            $search_data=['and',['>','a.ctime',$start],['<','a.ctime',$end]];
        }else
        {
            $search_data=['and',['>','a.ctime',1]];
        }

        //操作条件
//        $status=\Yii::$app->request->get('status');
//
//        if($status=='1')
//        {
//            $statuswhere='a.true_url !=\'\'';
//        }elseif($status=='2')
//        {
//            $statuswhere='a.true_url is null';
//        }else
//        {
//            $statuswhere="a.id > 1";
//        }


        //审核条件
        $shenhe=Yii::$app->request->get('shenhe');

        if($shenhe=='1')
        {
            $shenhewhere="a.audit=1 and a.is_ultimate_shenhe=1";
        }elseif($shenhe=='0')
        {
            $shenhewhere="(a.audit=0 or a.audit=1) and a.is_ultimate_shenhe=0";
        }elseif($shenhe=='2')
        {
            $shenhewhere="a.audit=2";
        }else
        {
            $shenhewhere="a.id>0";
        }




        $list=Yuming::find()
            ->select('a.id,a.note,true_url,a.ctime,b.a_users,b.promote_url as url,b.appname,a.last_time,c.advertiser,u.name,pr.name as prname,a.audit,a.is_ultimate_shenhe,a.audit_count,a.ip')
            ->join('a left join','jd_account b','b.id=a.account_id')
            ->leftJoin('jd_customer c','c.id=b.avid')
            ->leftJoin('user_backend u','u.id=a.submitusers')
            ->leftJoin('jd_product_line pr','pr.id=b.prlin_id')
            ->andFilterWhere($searchwhere)
            ->andFilterWhere($search_data)
            ->andFilterWhere($userswhere)
            ->andFilterWhere(['c.id'=>$av_id])
            ->andWhere($shenhewhere)
            ->orderBy('a.id desc')
            ->asArray();



        if(\Yii::$app->request->get('excel')=='1')
        {


            set_time_limit(0);
            \moonland\phpexcel\Excel::export([
                'models'=>$list->all(),
                'fileName'=>'二级域名解析数据'.date("Y-m-d"),
                'columns' => ['id','note','true_url','a_users','appname','last_time','advertiser','name','prname'], //without header working, because the header will be get label from attribute label.
                'headers' => ['id' => '编号','note' => '备注', 'true_url' => '二级域名','a_users'=>'账户','appname'=>'appname','last_time'=>'操作时间','advertiser'=>'公司','name'=>'提交人','prname'=>'产品线'],

            ]);


        }


        $count=$list->count();


        $pagination=new Pagination(['totalCount'=>$count]);
        $par_page=Yii::$app->request->get('per-page');
        $list_page=$list->offset($pagination->offset)
            ->limit($par_page?$par_page:$pagination->limit)
            ->all();
        $return_data['count']=$pagination->totalCount;
        $return_data['current_page']=$pagination->page;
        $return_data['size']=$pagination->pageSize;


        return array('code'=>'200','msg'=>'success','data'=>$list_page,'page_data'=>$return_data);
        /*
        if($list_page)
        {
            return array('code'=>'200','msg'=>'success','data'=>$list_page,'page_data'=>$return_data);
        }else
        {
            return array('code'=>'500','msg'=>'没有数据');
        }
        */

    }

    public function actionExceltest(){
        set_time_limit(0);
        \moonland\phpexcel\Excel::export([
            'models'=>[['张三','18','1'],['李四','20','1'],['王五','22','1']],
            'fileName'=>time(),
            'columns' => ['column1','column2','column3'], //without header working, because the header will be get label from attribute label.
            'headers' => ['column1' => '姓名','column2' => '年龄', 'column3' => '婚否'],

        ]);

        //$str=ord('A');
        //echo $str;
        exit;

    }

    public function actionUpyumingstatus($id){
        $model= Yuming::findOne($id);
        $model->load(\Yii::$app->getRequest()->getBodyParams(),'');
        $model->last_time=date("Y-m-d H:i:s");

        if($model->save())
        {
            return array('code'=>'200','msg'=>'success','data'=>$model);
        }else
        {
            return array('code'=>'500','msg'=>'FAIL');
        }

    }


    public function actionUpyumingstatusover($id){
        $model= Yuming::findOne($id);
        //$model->load(\Yii::$app->getRequest()->getBodyParams(),'');
        if(Yii::$app->request->post('status')!='0')
        {
            throw new \Exception('参数错误 必须为0！');
        }
        $model->status=0;
        $model->last_time=date("Y-m-d H:i:s");

        if($model->save())
        {
            return array('code'=>'200','msg'=>'success','data'=>$model);
        }else
        {
            return array('code'=>'500','msg'=>'FAIL');
        }

    }

    public function actionDeleteyuming($id)
    {
        if(Yuming::deleteAll(['id'=>$id]))
        {
            return array('code'=>'200','msg'=>'success','data'=>$model);
        }else
        {
            return array('code'=>'500','msg'=>'FAIL');
        }
    }

    public function actionTags_list(){
        $list=Tags::find()->all();

        foreach ($list as $key=>$val)
        {
            switch ($val['parent_id']){
                case 1:
                    $tags[0]['parent_name']='产品类型';
                    $tags[0]['list'][]=$val;
                    break;
                case 2:
                    $tags[1]['parent_name']='产品类型';
                    $tags[1]['list'][]=$val;
                    break;

            }
        }

        return $tags;
    }


    public function actionUpaccount_tags($id){
        $model=Account::findOne($id);
        $model->load(Yii::$app->getRequest()->getBodyParams(),'');
        //$isjiugaixin= $postdata['isjiugaixin'];

        if($model->save())
        {
            return ['msg'=>'修改成功','code'=>'200','account'=>$model];
        }else{
            return ['msg'=>'修改失败','code'=>'200','err'=>$model->errors];
        }

    }

    //账户消耗
    public function actionAccountCost(){
        //获取日期
        $date=Yii::$app->request->get('date');
        $date2=date("Y-m-d",strtotime($date." -1 day"));
        $date7=date("Y-m-d",strtotime($date." -6 day"));
        //获取日期账户
        $yesterday=AccountCost::account_cost_date($date,$date);


        $qiantian=AccountCost::account_cost_date($date2,$date2);
        $avg=AccountCost::account_ave_cost_date($date7,$date);
        foreach ($yesterday as $key=>$val)
        {
            $data[$key]['cost']=round($val['cost'],2);
        }
        foreach ($avg as $key=>$val)
        {
            $data[$key]['account_name']=$key;
            $data[$key]['advertiser']=$val['advertiser'];
            $data[$key]['name']=$val['name'];
            $data[$key]['zuori']=0;
            $data[$key]['cost']=$data[$key]['cost']?$data[$key]['cost']:0;
            $data[$key]['zuoriValue']=0;
            $data[$key]['zuoriRate']=0;

            $data[$key]['avg']=round($val['cost'],2);
            $data[$key]['qiriValue']=round($data[$key]['cost']-$data[$key]['avg'],2);
            $data[$key]['qiriRate']=round(($data[$key]['cost']-$data[$key]['avg'])/($data[$key]['avg']>0?$data[$key]['avg']:1)*100,2);
        }

        foreach ($qiantian as $key=>$val)
        {
            $data[$key]['zuori']=round($val['cost'],2);
            $data[$key]['zuoriValue']=round($data[$key]['cost']-$data[$key]['zuori'],2);
            $data[$key]['zuoriRate']=round(($data[$key]['cost']-$data[$key]['zuori'])/($data[$key]['zuori']>0?$data[$key]['zuori']:1)*100,2);
        }

         $return_data=[];
        foreach ($data as $key=>$val){
            array_push($return_data,$val);
        }

        return array('code'=>'200','data'=>$return_data);
    }
    //账户总消耗
    public function actionAccountCostAll(){
        //获取日期
        $date=Yii::$app->request->get('date');
        $date2=date("Y-m-d",strtotime($date." -1 day"));
        $date7=date("Y-m-d",strtotime($date." -7 day"));
        //获取日期账户
        $yesterday=AccountCost::account_cost_date_all($date,$date);
        $qiantian=AccountCost::account_cost_date_all($date2,$date2);
        $avg=AccountCost::account_ave_cost_date_all($date7,$date);

        $data['cost']=round($yesterday,2);
        $data['zuori']=round($qiantian,2);
        $data['zuoriValue']=round($data['cost']-$data['zuori'],2);
        $data['zuoriRate']=round(($data['cost']-$data['zuori'])/$data['zuori']*100,2);

        $data['avg']=round($avg,2);
        $data['qiriValue']=round($data['cost']-$data['avg'],2);
        $data['qiriRate']=round(($data['cost']-$data['avg'])/$data['avg']*100,2);

        return array('code'=>'200','data'=>$data);
    }

    //账户消耗
    public function actionAccountCostAd(){
        //获取日期
        $date=Yii::$app->request->get('date');
        $date2=date("Y-m-d",strtotime($date." -1 day"));
        $date7=date("Y-m-d",strtotime($date." -6 day"));
        //获取日期账户
        $yesterday=AccountCost::account_cost_date_ad($date,$date);
        $qiantian=AccountCost::account_cost_date_ad($date2,$date2);

        $data=[];
        $avg=AccountCost::account_ave_cost_date_ad($date7,$date);
        foreach ($yesterday as $key=>$val)
        {
            $data[$key]['cost']=round($val['cost'],2);
        }
        foreach ($avg as $key=>$val)
        {
            $data[$key]['advertiser']=$key;
            $data[$key]['customer_type']=$val['customer_type'];
            $data[$key]['name']=$val['name'];

            $data[$key]['cost']=$data[$key]['cost']?$data[$key]['cost']:0;
            $data[$key]['zuori']=0;
            $data[$key]['zuoriValue']=0;
            $data[$key]['zuoriRate']=0;

            $data[$key]['avg']=round($val['cost'],2);
            $data[$key]['qiriValue']=round($data[$key]['cost']-$data[$key]['avg'],2);
            $data[$key]['qiriRate']=round(($data[$key]['cost']-$data[$key]['avg'])/($data[$key]['avg']>0?$data[$key]['avg']:1)*100,2);
        }

        foreach ($qiantian as $key=>$val)
        {
            $data[$key]['zuori']=round($val['cost'],2);
            $data[$key]['zuoriValue']=round($data[$key]['cost']-$data[$key]['zuori'],2);
            $data[$key]['zuoriRate']=round(($data[$key]['cost']-$data[$key]['zuori'])/($data[$key]['zuori']>0?$data[$key]['zuori']:1)*100,2);
        }

        $return_data=[];
        foreach ($data as $key=>$val){
            array_push($return_data,$val);
        }

        return array('code'=>'200','data'=>$return_data);
    }




    //账户消耗
    public function actionAccountCostChoosable(){
        //获取日期
        $date_start1=Yii::$app->request->get('start_date1');
        $date_end1=Yii::$app->request->get('end_date1');

        $date_start2=Yii::$app->request->get('start_date2');
        $date_end2=Yii::$app->request->get('end_date2');

        //获取日期账户
        $yesterday=AccountCost::account_cost_date($date_start1,$date_end1);

        $qiantian=AccountCost::account_cost_date($date_start2,$date_end2);
        foreach ($yesterday as $key=>$val)
        {
            $data[$key]['account_name']=$key;
            $data[$key]['advertiser']=$val['advertiser'];
            $data[$key]['name']=$val['name'];
            $data[$key]['cost']=round($val['cost'],2);
            $data[$key]['zuori']=0;
            $data[$key]['zuoriValue']=0;
            $data[$key]['zuoriRate']=0;
        }
        foreach ($qiantian as $key=>$val)
        {
            $data[$key]['account_name']=$key;
            $data[$key]['advertiser']=$val['advertiser'];
            $data[$key]['name']=$val['name'];
            $data[$key]['cost']=$data[$key]['cost']?$data[$key]['cost']:0;

            $data[$key]['zuori']=round($val['cost'],2);
            $data[$key]['zuoriValue']=round($data[$key]['cost']-$data[$key]['zuori'],2);
            $data[$key]['zuoriRate']=round(($data[$key]['cost']-$data[$key]['zuori'])/($data[$key]['zuori']>0?$data[$key]['zuori']:1)*100,2);
        }

        $return_data=[];
        foreach ($data as $key=>$val){
            array_push($return_data,$val);
        }

        return array('code'=>'200','data'=>$return_data);
    }
    //账户总消耗
    public function actionAccountCostAllChoosable(){
        //获取日期
        //获取日期
        $date_start1=Yii::$app->request->get('start_date1');
        $date_end1=Yii::$app->request->get('end_date1');

        $date_start2=Yii::$app->request->get('start_date2');
        $date_end2=Yii::$app->request->get('end_date2');

        //获取日期账户
        $yesterday=AccountCost::account_cost_date_all($date_start1,$date_end1);
        $qiantian=AccountCost::account_cost_date_all($date_start2,$date_end2);

        $data['cost']=round($yesterday,2);
        $data['zuori']=round($qiantian,2);
        $data['zuoriValue']=round($data['cost']-$data['zuori'],2);
        $data['zuoriRate']=round(($data['cost']-$data['zuori'])/($data['zuori']>0?$data['zuori']:1)*100,2);
        return array('code'=>'200','data'=>$data);
    }

    //账户消耗To公司
    public function actionAccountCostAdChoosable(){
        //获取日期
        $date_start1=Yii::$app->request->get('start_date1');
        $date_end1=Yii::$app->request->get('end_date1');

        $date_start2=Yii::$app->request->get('start_date2');
        $date_end2=Yii::$app->request->get('end_date2');

        //获取日期账户
        $yesterday=AccountCost::account_cost_date_ad($date_start1,$date_end1);

        $qiantian=AccountCost::account_cost_date_ad($date_start2,$date_end2);
        foreach ($yesterday as $key=>$val)
        {
            $data[$key]['advertiser']=$key;
            $data[$key]['customer_type']=$val['customer_type'];
            $data[$key]['name']=$val['name'];

            $data[$key]['cost']=round($val['cost'],2);
            $data[$key]['zuori']=0;
            $data[$key]['zuoriValue']=0;
            $data[$key]['zuoriRate']=0;
        }
        foreach ($qiantian as $key=>$val)
        {
            $data[$key]['advertiser']=$key;
            $data[$key]['customer_type']=$val['customer_type'];
            $data[$key]['name']=$val['name'];

            $data[$key]['cost']=$data[$key]['cost']?$data[$key]['cost']:0;
            $data[$key]['zuori']=round($val['cost'],2);
            $data[$key]['zuoriValue']=round($data[$key]['cost']-$data[$key]['zuori'],2);
            $data[$key]['zuoriRate']=round(($data[$key]['cost']-$data[$key]['zuori'])/($data[$key]['zuori']>0?$data[$key]['zuori']:1)*100,2);
        }
        $return_data=[];
        foreach ($data as $key=>$val){
            array_push($return_data,$val);
        }

        return array('code'=>'200','data'=>$return_data);
    }

    public function actionAdcost(){
        $ad_id=Yii::$app->request->get('id');
        //查找账户消耗值
        $cost_total=Account::ad_cost_list($ad_id);
        $cost_total_sum=0;
        $balance=0;
        foreach ($cost_total as $val)
        {

            $cost_total_sum+=$val['cost']?$val['cost']:0;
            $balance+=$val['money']?$val['money']:0;
        }

        return [
          'code'=>'200',
          'data'=>[
              'cost_total'=>$cost_total_sum,
              'balance'=>$balance
          ]
        ];

    }

    public function actionAdProductLineCost(){
        $ad_id=Yii::$app->request->get('id');
        $one= AccountCost::find()->orderBy('date desc')->limit(1)->one();
        $date = $one['date'];
        //查找账户消耗值
        $cost_total=Account::ad_cost_list($ad_id,$date);
        $sum=0;
        $adname=JdCustomer::findOne($ad_id);

        foreach ($cost_total as $key=>$val)
        {
            $sum+=$val['account_num']?$val['account_num']:0;
        }

        return [
            'code'=>'200',
            'data'=>$cost_total,
            'adinfo'=>$adname,
            'sum'=>$sum
        ];

    }


    public function actionLast_date(){
        $one= AccountCost::find()->orderBy('date desc')->limit(1)->one();
        return ['date'=>$one['date'],'code'=>'200'];
    }


    public function actionAccountAmendment(){
        $id=Yii::$app->request->get('id');
        $pr_lin=Yii::$app->request->get('pr_lin');


        $balance=Yii::$app->request->get('balance');
        $upbalance=Yii::$app->request->get('upbalance');
        $cost=Yii::$app->request->get('cost');
        $balance_cost=Yii::$app->request->get('cost');
        $value=$balance-$upbalance;
        //$value=$balance-$upbalance+($cost+$balance_cost);


        $data=Account::prlin_account_max_up($id,$pr_lin,$value);

        return ['date'=>$data,'code'=>'200'];

    }


}