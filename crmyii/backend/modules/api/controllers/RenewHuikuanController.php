<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/10/27
 * Time: 9:40
 */

namespace backend\modules\api\controllers;

use backend\models\Account;
use backend\models\AccountCost;
use backend\models\AccountCostReal;
use backend\models\ActionSql;
use backend\models\BackMoney;
use backend\models\Customerb;
use backend\models\File;
use backend\models\JdCustomer;
use backend\models\ProductLine;
use backend\models\RenewOrder;
use backend\models\Yihuikuanxufei;
use Prophecy\Exception\Prediction\NoCallsException;
use Yii;
use backend\models\JdContract;
use backend\models\RenewHuikuan;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;

use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

use backend\components\Quanxian;

ini_set('max_execution_time', '0');
set_time_limit(0);

class RenewHuikuanController extends ActiveController
{
    public $modelClass='backend\models\RenewHuikuan';
    //
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

    //续费列表
    public function actionIndexlist($uid=''){
        $data['code']=200;
        $data['data']=RenewHuikuan::xufei_list($uid);
        return $data;
    }

    //财务续费列表
    public function actionIndexlist_caiwu($uid=''){
        $data['code']=200;
        $data['data']=RenewHuikuan::xufei_list($uid);
        return $data;
    }

    //媒介续费列表
    public function actionIndexlist_meijie($uid=''){
        $data['code']=200;
        $data['data']=RenewHuikuan::xufei_list($uid);
        return $data;
    }

    //添加续费
    public function actionAdd($contract_id){

        //要续费的合同信息
        $data['code']='200';
        $data['data']=array(
            "contract"=>JdContract::contractinfonomt($contract_id),
            "accountlist"=>Account::account_contract_list($contract_id)
        );

        return $data;
    }
    //添加续费返回
    public function actionAddru()
    {

        $tr = Yii::$app->db->beginTransaction();
        try {
            //写入续费
           $insert=RenewHuikuan::add_renew();
           if(isset($insert['code']))
            {
                return $insert;exit;
            }
            //操作已回款续费

            $tr->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'续费成功','data'=>$insert);
    }


    //续费详情
    public function actionRenewInfo($id){
        //要续费的合同信息
        $data['code']='200';
        $data['data']=array(
            "contract"=>RenewHuikuan::renewinfo($id)
        );
        return $data;
    }
    //续费详情
    public function actionRenewInfo_meijie($id){
        //要续费的合同信息
        $data['code']='200';
        $data['data']=array(
            "contract"=>RenewHuikuan::renewinfo($id)
        );
        return $data;
    }

    //续费详情
    public function actionRenewInfo_caiwu($id){
        //要续费的合同信息
        $data['code']='200';
        $data['data']=array(
            "contract"=>RenewHuikuan::renewinfo($id)
        );
        return $data;
    }



    //批次添加续费返回
    public function actionAddrupici()
    {

        $tr = Yii::$app->db->beginTransaction();
        try {
            //写入续费
            $insert=RenewHuikuan::add_pici();
            if(isset($insert['code']))
            {
                return $insert;exit;
            }
            $insert=$insert[0];
            //操作已回款续费
            //--RenewHuikuan::renew_huikuan($insert['xf_contractid']);
            $tr->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'续费成功','data'=>$insert);
    }

    //批次图片设置
    public function actionPicifile($xf_id){
        //根据续费id 更改其他同批次续费
        $xf_info=RenewHuikuan::findOne($xf_id);
        //查询此续费图片
        $filelist=File::find()->where(['yid'=>$xf_info['id'],'type'=>4])->all();
        //查询其他同批次续费
        $xf_list=RenewHuikuan::find()->select('id')->where(['pici'=>$xf_info['pici']])->andWhere('id != '.$xf_info['id'])->all();
        foreach ($xf_list as $key=>$val)
        {
            //新增图片信息
            foreach ($filelist as $k=>$v)
            {
                $newfile=new File();
                $newfile->type=$v['type'];
                $newfile->yid=$val['id'];
                $newfile->file=$v['file'];
                $newfile->save();
            }

        }

        return array('code'=>200,'mgs'=>'批次新增图片成功');
    }

    //一级审核
    public function actionShenhe1($id){

        //查询此续费是否有同批次LIST
        $renew_inof_one=RenewHuikuan::findOne($id);

            $tr1 = Yii::$app->db->beginTransaction();
            try {
                //如果有批次的话
                if($renew_inof_one['pici']!='')
                {
                    $picilist=RenewHuikuan::find()->where(['pici'=>$renew_inof_one['pici']])->all();
                    foreach ($picilist as $picival){
                        //写入历史记录
                        $this->global_audit($picival['id'],'renew',1,'jd_renew_huikuan');
                        //审核续费后续
                        RenewHuikuan::renew_shenhe($picival['id'],1);
                    }


                }else
                {
                    //写入历史记录
                    $this->global_audit($id,'renew',1,'jd_renew_huikuan');
                    //审核续费后续
                    RenewHuikuan::renew_shenhe($id,1);
                }
                $tr1->commit(); //提交数据

            } catch (Exception $e) {
                //回滚
                $tr1->rollBack();
                return $e->getMessage(); //返回自定义异常信息
            }
            return array('code'=>200,'mgs'=>'续费审核成功');






    }
    //二级审核
    public function actionShenhe2($id){
        //查询此续费是否有同批次LIST
        $renew_inof_one=RenewHuikuan::findOne($id);

        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //如果有批次的话
            if($renew_inof_one['pici']!='')
            {
                $picilist=RenewHuikuan::find()->where(['pici'=>$renew_inof_one['pici']])->all();
                foreach ($picilist as $picival){
                    //写入历史记录
                    $this->global_audit($picival['id'],'renew',2,'jd_renew_huikuan');
                    //审核续费后续
                    RenewHuikuan::renew_shenhe($picival['id'],2);
                }


            }else
            {
                //写入历史记录
                $this->global_audit($id,'renew',2,'jd_renew_huikuan');
                //审核续费后续
                RenewHuikuan::renew_shenhe($id,2);
            }
            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'续费二级审核成功');

    }

    //二级审核
    public function actionShenhe3($id){
        //查询此续费是否有同批次LIST
        $renew_inof_one=RenewHuikuan::findOne($id);

        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //如果有批次的话
            if($renew_inof_one['pici']!='')
            {
                $picilist=RenewHuikuan::find()->where(['pici'=>$renew_inof_one['pici']])->asArray()->all();
                foreach ($picilist as $picival){
                    //写入历史记录
                    $this->global_audit($picival['id'],'renew',3,'jd_renew_huikuan');
                    //审核续费后续
                    RenewHuikuan::renew_shenhe($picival['id'],3);
                }


            }else
            {
                //写入历史记录
                $this->global_audit($id,'renew',3,'jd_renew_huikuan');
                //审核续费后续
                RenewHuikuan::renew_shenhe($id,3);
            }

            //如果是第三级审核的话 判断媒介合同状态
            /*
             *
            $post = Yii::$app->db->createCommand('SELECT * FROM post WHERE id=:id AND status=:status')
           ->bindValue(':id', $_GET['id'])
           ->bindValue(':status', 1)
           ->queryOne();
             * */

           $one=ActionSql::find()->where(['action'=>'renew','is_true'=>'1'])->one();
           if($one['sql']){
            $post = Yii::$app->db->createCommand($one['sql'])
                ->bindValue(':id', $renew_inof_one['account'])
                ->queryScalar();
           }
            if($post=='1')
            {
                //如果有批次的话
                if($renew_inof_one['pici']!='')
                {
                    $picilist=RenewHuikuan::find()->where(['pici'=>$renew_inof_one['pici']])->asArray()->all();
                    foreach ($picilist as $picival){
                        //写入历史记录
                        $this->global_audit($picival['id'],'renew',4,'jd_renew_huikuan');
                        //审核续费后续
                        RenewHuikuan::renew_shenhe($picival['id'],4);
                    }


                }else
                {
                    //写入历史记录
                    $this->global_audit($id,'renew',4,'jd_renew_huikuan');
                    //审核续费后续
                    RenewHuikuan::renew_shenhe($id,4);
                }
            }





            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'续费二级审核成功');

    }

    //二级审核
    public function actionShenhe4($id){
        //查询此续费是否有同批次LIST
        $renew_inof_one=RenewHuikuan::findOne($id);

        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //如果有批次的话
            if($renew_inof_one['pici']!='')
            {
                $picilist=RenewHuikuan::find()->where(['pici'=>$renew_inof_one['pici']])->all();
                foreach ($picilist as $picival){
                    //写入历史记录
                    $this->global_audit($picival['id'],'renew',4,'jd_renew_huikuan');
                    //审核续费后续
                    RenewHuikuan::renew_shenhe($picival['id'],4);
                }


            }else
            {
                //写入历史记录
                $this->global_audit($id,'renew',4,'jd_renew_huikuan');
                //审核续费后续
                RenewHuikuan::renew_shenhe($id,4);
            }
            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'续费二级审核成功');

    }

    //二级审核
    public function actionShenhe5($id){
        //查询此续费是否有同批次LIST
        $renew_inof_one=RenewHuikuan::findOne($id);

        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //如果有批次的话
            if($renew_inof_one['pici']!='')
            {
                $picilist=RenewHuikuan::find()->where(['pici'=>$renew_inof_one['pici']])->all();
                foreach ($picilist as $picival){
                    //写入历史记录
                  $a=$this->global_audit($picival['id'],'renew',5,'jd_renew_huikuan');
                  if($a['code']=='200')
                  {
                      //审核续费后续
                      RenewHuikuan::renew_shenhe($picival['id'],5);
                  }
                }
            }else
            {
                //写入历史记录
                $this->global_audit($id,'renew',5,'jd_renew_huikuan');
                //审核续费后续
                RenewHuikuan::renew_shenhe($id,5);
            }
            $tr1->commit(); //提交数据
        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'续费二级审核成功');

    }



    //一级审核
    public function actionXiadanShenhe1($id){

        //查询此续费是否有同批次LIST
        $renew_inof_one=RenewHuikuan::findOne($id);

        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //如果有批次的话
            if($renew_inof_one['pici']!='')
            {
                $picilist=RenewHuikuan::find()->where(['pici'=>$renew_inof_one['pici']])->all();
                foreach ($picilist as $picival){
                    //写入历史记录
                    $this->global_audit($picival['id'],'xiadan',1,'jd_renew_huikuan');
                    //审核续费后续
                    RenewHuikuan::renew_shenhe($picival['id'],1);
                }


            }else
            {
                //写入历史记录
                $this->global_audit($id,'xiadan',1,'jd_renew_huikuan');
                //审核续费后续
                RenewHuikuan::renew_shenhe($id,1);
            }
            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'续费审核成功');






    }
    //二级审核
    public function actionXiadanShenhe2($id){
        //查询此续费是否有同批次LIST
        $renew_inof_one=RenewHuikuan::findOne($id);

        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //如果有批次的话
            if($renew_inof_one['pici']!='')
            {
                $picilist=RenewHuikuan::find()->where(['pici'=>$renew_inof_one['pici']])->all();
                foreach ($picilist as $picival){
                    //写入历史记录
                    $this->global_audit($picival['id'],'xiadan',2,'jd_renew_huikuan');
                    //审核续费后续
                    RenewHuikuan::renew_shenhe($picival['id'],2);
                }


            }else
            {
                //写入历史记录
                $this->global_audit($id,'xiadan',2,'jd_renew_huikuan');
                //审核续费后续
                RenewHuikuan::renew_shenhe($id,2);
            }
            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'续费二级审核成功');

    }

    //二级审核
    public function actionXiadanShenhe3($id){
        //查询此续费是否有同批次LIST
        $renew_inof_one=RenewHuikuan::findOne($id);

        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //如果有批次的话
            if($renew_inof_one['pici']!='')
            {
                $picilist=RenewHuikuan::find()->where(['pici'=>$renew_inof_one['pici']])->all();
                foreach ($picilist as $picival){
                    //写入历史记录
                    $this->global_audit($picival['id'],'xiadan',3,'jd_renew_huikuan');
                    //审核续费后续
                    RenewHuikuan::renew_shenhe($picival['id'],3);
                }


            }else
            {
                //写入历史记录
                $this->global_audit($id,'xiadan',3,'jd_renew_huikuan');
                //审核续费后续
                RenewHuikuan::renew_shenhe($id,3);
            }
            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'续费二级审核成功');

    }

    //二级审核
    public function actionXiadanShenhe4($id){
        //查询此续费是否有同批次LIST
        $renew_inof_one=RenewHuikuan::findOne($id);

        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //如果有批次的话
            if($renew_inof_one['pici']!='')
            {
                $picilist=RenewHuikuan::find()->where(['pici'=>$renew_inof_one['pici']])->all();
                foreach ($picilist as $picival){
                    //写入历史记录
                    $this->global_audit($picival['id'],'xiadan',4,'jd_renew_huikuan');
                    //审核续费后续
                    RenewHuikuan::renew_shenhe($picival['id'],4);
                }


            }else
            {
                //写入历史记录
                $this->global_audit($id,'xiadan',4,'jd_renew_huikuan');
                //审核续费后续
                RenewHuikuan::renew_shenhe($id,4);
            }
            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'续费二级审核成功');

    }

    //二级审核
    public function actionXiadanShenhe5($id){
        //查询此续费是否有同批次LIST
        $renew_inof_one=RenewHuikuan::findOne($id);

        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //如果有批次的话
            if($renew_inof_one['pici']!='')
            {
                $picilist=RenewHuikuan::find()->where(['pici'=>$renew_inof_one['pici']])->all();
                foreach ($picilist as $picival){
                    //写入历史记录
                    $this->global_audit($picival['id'],'xiadan',5,'jd_renew_huikuan');
                    //审核续费后续
                    RenewHuikuan::renew_shenhe($picival['id'],5);
                }


            }else
            {
                //写入历史记录
                $this->global_audit($id,'xiadan',5,'jd_renew_huikuan');
                //审核续费后续
                RenewHuikuan::renew_shenhe($id,5);
            }
            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'续费二级审核成功');

    }




    //续费下单修改账户
    public function actionOrder_upaccount(){
        $id=Yii::$app->request->post('id');
        //检测是否为下单的续费
        $order=RenewOrder::find()->where(['xf_id'=>$id])->one();
        $renewinfo=RenewHuikuan::findOne($id);

        if($renewinfo['audit']=='1')
        {
            //账户详细信息
            $account=Account::findOne($renewinfo['account']);


            $account->a_users=Yii::$app->request->post('a_users');
            $account->a_password=Yii::$app->request->post('a_password');
            $account->state=0;
            $account->promote_url=Yii::$app->request->post('promote_url');

            //格式化账户名称
            $account->a_users=trim($account->a_users);//删除左右两侧空格
            $account->a_users=str_replace(' ','',$account->a_users);

            $account->save();
            Account::updateAll(['endtime'=>time()],"a_users='$account[a_users]' and endtime=4092599349 and id !=".$account->id);

            if(\Yii::$app->params['yushan_synchronizing']=='true') {

                $url = Yii::$app->params['yushancom']."sem/createhu";
                //$url = "http://192.168.1.68:5281/sem/createhu";

                //同步羽扇
                //产品线
                $prlin = ProductLine::findOne($account['prlin_id']);

                //渠道
                $qudao = Customerb::findOne($account['qudao']);

                $post_data = array(
                    "name" => $account['appname'],
                    "account_type" => $prlin['yushan_type'],
                    "account_name" => $account['a_users'],
                    "account_password" => $account['a_password'],
                    "account_appid" => 'no token',
                    "account_status" => '1',
                    "fd_rate" => $account['fandian'] / 100,
                    "view_type" => '120001',
                    "server_type" => $account['server_type'],
                    "qudao_id" => $qudao['yushan_id'],
                    "sem"=>'3355'
                );

                $yushan_data = Yii::$app->hjd->hjd_post_curl($url, $post_data);

                $yushan_id = $yushan_data->data->appid;
                if ($yushan_id != '') {
                    Account::updateAll(["appid" => $yushan_id], ['id' => $renewinfo['account']]);
                    $data['code'] = '200';
                    $data['msg'] = "修改账户成功";
                } else {
                    $data['code'] = '500';
                    $logfile=\Yii::getAlias('@app').'/runtime/logs/hjd.log';
                    error_log(PHP_EOL.date("Y-m-d H:i:s").'  同步羽扇失败:'.$yushan_data.PHP_EOL,3,$logfile);
                    $data['msg'] = '同步羽扇失败 ，请截图报告管理员！';
                }
            }else
            {
                $data['code'] = '200';
                $data['msg'] = "修改账户成功";
            }

            return $data;
        }else
        {
            return array("code"=>"500","msg"=>"没有通过审核不可改账户信息");

        }

    }


    //未回款逾期
    public function actionWeihuikuanyuqi(){
        return RenewHuikuan::whkyq();
    }
    //未回款逾期
    public function actionWeihuikuanyuqicu(){
        return RenewHuikuan::whkyqcu();
    }
    //已回款逾期
    public function actionYihuikuanyuqi(){
        return RenewHuikuan::yhkyq();
    }

    //看媒介真实返点
    public function actionMei_true_fandian(){
        return 1;
    }
    //看媒介销售返点
    public function actionMei_market_fandian(){
        return 1;
    }

    //修改媒介销售返点
    public function actionUp_meijie_markey_fandian($id,$fandian){
        if($contract=JdContract::updataContract($id,'m_xsfd',$fandian))
        {
            $data['code']='200';
            $data['mes']='修改媒介合同销售返点成功';
        }else
        {
            $data['code']='500';
            $data['mes']='修改媒介合同销售返点失败';
        }
        return $data;
    }

    //返回续费对应的回款
    public function actionRenew_to_huikuan($renew_id){
        $yihuikuan=Yihuikuanxufei::find()
            ->select('b.money,b.payment_time,a.money as pmoey')
            ->join('a left join','jd_renew_huikuan b','a.hk_id=b.id')
            ->where(['a.xf_id'=>$renew_id])
            ->asArray()
            ->all();
        $code='200';

        return array('code'=>$code,'data'=>$yihuikuan);
    }



    //销售提成
    public function actionMarket_ticheng($start='',$end='',$uid='',$type=''){
        $list=RenewHuikuan::markeyticheng($start,$end,$uid,$type);

        return($list);

    }
    //销售提成按销售统计
    public function actionMarket_ticheng_m($start='',$end=''){
        $list=RenewHuikuan::markeyticheng_zj($start,$end);

        return($list);

    }
    //按新老订单统计
    //销售提成按销售统计
    public function actionMarket_ticheng_adstate($start='',$end='',$uid=''){
        $list=RenewHuikuan::markeyticheng_adstate($start,$end,$uid);

        return($list);

    }

    //销售客户利润列表
    public function actionMarket_ad_ticheng($start='',$end='',$uid='')
    {

        $list=RenewHuikuan::users_ad_ticheng($start,$end,$uid);

        return($list);
    }
    //销售新增客户折线图
    public function actionMarket_new_customer_cn($uid,$year)
    {

        $list=RenewHuikuan::market_new_customer_cn($uid,$year);

        return($list);
    }

    //销售利润折线图
    public function actionMarket_lirun_money_zhexian($year,$uid=''){

        $list=RenewHuikuan::market_lirun_money_zhexian($year,$uid);

        return($list);

    }
    //续费全年折线图
    public function actionMarket_renew_money_zhexian($year){

        $list=RenewHuikuan::market_renew_money_zhexian($year);

        return($list);

    }
    //利润全年折线图
    public function actionLirun_money_zhexian($year){

        $list=RenewHuikuan::market_lirun_money_zhexian($year);

        return($list);

    }


    //
    //销售提成计算公搜
    public function actionTicheng_zongji($start,$end,$uid){
        $renew=RenewHuikuan::markeyticheng($start,$end,$uid,'renew');
        $tuikuan=RenewHuikuan::markeyticheng($start,$end,$uid,'tuikuan');
        $bukuan=RenewHuikuan::markeyticheng($start,$end,$uid,'bukuan');

        $data=[
            'xf'=>$renew['tichengsum'],
            'tuikuan'=>$tuikuan['tichengsum'],
            'bukuan'=>$bukuan['tichengsum']
        ];
        return $data;

    }

    //续费与回款折线图


    public function actionRenewhuikuantu($yyyy,$adid){
        $sql='select sum(money) as money,to_char(to_timestamp(payment_time),\'YYYY-MM\') as date from jd_renew_huikuan where payment_type in(1,2) and advertiser='.$adid.' and is_ultimate_shenhe=1 GROUP BY date';
        $sql2="select * from ($sql) t where date like '%{$yyyy}%' order by date asc ";
        $dk_sm = Yii::$app->db->createCommand($sql2)
            ->queryAll();





        $sql='select round(sum(b_money),2) as money,to_char(to_timestamp(b_time),\'YYYY-MM\') as date from jd_back_money where is_ultimate_shenhe=1 and advertiser='.$adid.' GROUP BY date';
        $sql2="select * from ($sql) t where date like '%{$yyyy}%'  order by date asc ";
        $hk_sm = Yii::$app->db->createCommand($sql2)
            ->queryAll();

        $hk_sm=ArrayHelper::map($hk_sm,'date','money');

        $data=[];
        $hkzong=0;
        for ($i=1;$i<=12;$i++)
        {
            if($i<10)
            {
                $a='0'.$i;
            }else
            {
                $a=$i;
            }
            $hkzong+=$hk_sm[$yyyy.'-'.$a]?$hk_sm[$yyyy.'-'.$a]:0;
            $data[$i-1]['date']=$yyyy.'-'.$a;

            $data[$i-1]['money']=$hk_sm[$yyyy.'-'.$a]?$hk_sm[$yyyy.'-'.$a]:0;
            $data[$i-1]['zong']=$hkzong;
        }


        $dk_sm=ArrayHelper::map($dk_sm,'date','money');
        $data1=[];
        $dkzong=0;
        for ($i=1;$i<=12;$i++)
        {
            if($i<10)
            {
                $a='0'.$i;
            }else
            {
                $a=$i;
            }
            $dkzong+=$dk_sm[$yyyy.'-'.$a]?$dk_sm[$yyyy.'-'.$a]:0;
            $data1[$i-1]['date']=$yyyy.'-'.$a;

            $data1[$i-1]['money']=$dk_sm[$yyyy.'-'.$a]?$dk_sm[$yyyy.'-'.$a]:0;
            $data1[$i-1]['zong']=$dkzong;
        }






        return array('code'=>'200','renew'=>$data,'huikuan'=>$data1);
        exit;
        $a='';
        for ($i=0;$i<=11;$i++)
        {
            if($i+1<10)
            {
                $a='0';
            }
            if(empty($hk_sm[$i]))
            {
                $hk_sm[$i]=array(
                    'money'=>0,
                    'date'=>$yyyy."-".$a.($i+1),
                    'zong'=>$hk_sm[$i-1]['zong']
                );
            }

            if(empty($dk_sm[$i]))
            {
                $dk_sm[$i]=array(
                    'money'=>0,
                    'date'=>$yyyy."-".$a.($i+1),
                    'zong'=>$dk_sm[$i-1]['zong']
                );
            }
        }

        return array('code'=>'200','renew'=>$dk_sm,'huikuan'=>$hk_sm);


    }


    //续费周期和回款周期
    public function actionZhouqi_kehu($id){
        $renew=RenewHuikuan::find()
            ->select(['to_char(to_timestamp(payment_time),\'YYYY-MM-DD\') as date'])
            ->where(['payment_type'=>[1,2],'advertiser'=>$id,'is_ultimate_shenhe'=>1])
            ->groupBy('date')
            ->orderBy('date asc')
            ->asArray()
            ->all();


        $cday=0;
       // var_dump($renew);
        foreach($renew as $key=>$val)
        {
            if($key>0){
                //echo $renew[$key]['date'];
                $ca3 = date_diff(date_create($val['date']), date_create($renew[$key-1]['date']));//入职时间和现在时间差值
                $cday+=$ca3->d;
            }
        }
        if(count($renew)>1){
            $data['renewzhouqi']=$cday/(count($renew)-1);
        }else
        {
            $data['renewzhouqi']='0';
        }

        $huikuan=BackMoney::find()
            ->select(['to_char(to_timestamp(b_time),\'YYYY-MM-DD\') as date'])
            ->where(['advertiser'=>$id,'is_ultimate_shenhe'=>1])
            ->groupBy('date')
            ->orderBy('date asc')
            ->asArray()
            ->all();
        $cday=0;
        // var_dump($renew);
        foreach($huikuan as $key=>$val)
        {
            if($key>0){
                //echo $renew[$key]['date'];
                $ca3 = date_diff(date_create($val['date']), date_create($huikuan[$key-1]['date']));//入职时间和现在时间差值
                $cday+=$ca3->d;
            }
        }
        if(count($huikuan)>1){
            $data['huikuanzhouqi']=$cday/(count($huikuan)-1);
        }else
        {
            $data['huikuanzhouqi']='0';
        }


        return $data;



    }
    //产品线续费利润 首次续费 最后一次续费
    public function actionProductlinrenew($id){
        $list=RenewHuikuan::ad_renew_product_lirun($id);

        return($list);
    }

    //公司欠款总计
    public function actionQiankuan_ad($id){
        $renew=RenewHuikuan::find()
            ->where(['payment_type'=>[1,2],'advertiser'=>$id,'is_ultimate_shenhe'=>1])
            ->sum('xf_qiane');

        return $renew?$renew:'0';

    }

    //客户消耗统计
    public function actionCost_ad_tongji($id,$start,$end){

        $data=AccountCost::find()
            ->select(['a.date','sum(round(a.cost,2)) as cost','pl.name'])
            ->join('a left join','(select avid,a_users,prlin_id from jd_account group by avid,a_users,prlin_id) ja','a.account_name= ja.a_users and a.av_id =ja.avid')
            ->leftjoin('jd_product_line pl','ja.prlin_id = pl.id')
            ->where(['and',['>=','a.date',$start],['<=','a.date',$end]])
            ->andWhere(['a.av_id'=>$id])
            ->groupBy(['a.date','pl.name'])
            ->orderBy('pl.name asc')
            ->orderBy('a.date asc')->asArray()
            ->all();
        $return_data=[];
        $prlin=[];

        $da=[];
        foreach ($data as $key=>$val)
        {
            if(!in_array($val['name'],$prlin))
            {
                $prlin[]=$val['name'];
            }
            $return_data[$val['name'].$val['date']]=$val;
        }




        $getdatearr=Yii::$app->hjd->getDateFromRange($start,$end);


        foreach ($getdatearr as $k => $v)
        {

            foreach ($prlin as $prname)
            {
                if($return_data[$prname.$v])
                {
                    $da[$prname][]=$return_data[$prname.$v];
                   // $return_data[$prname.$v]=['date'=>$v,'cost'=>'0','name'=>$prname];
                }else
                {
                    $da[$prname][]=['date'=>$v,'cost'=>'0','name'=>$prname];
                }
            }

        }

        return array('code'=>'200','data'=>$da);
    }

    public function actionLirun_ad_product_tu($id,$year){
        $data=RenewHuikuan::lirun_ad_oroduct_tu($id,$year);
        return array('code'=>'200','data'=>$data);
    }


    public function actionCustomer_profits_rank($start,$end){

        $list= RenewHuikuan::users_ad_ticheng($start,$end);
        foreach ($list as $key=>$val)
        {
            $list[$key]['rank']=$key+1;
        }
        return $list;
    }

    public function actionHjdtest($id){
        RenewHuikuan::renew_huikuan($id);

    }



}