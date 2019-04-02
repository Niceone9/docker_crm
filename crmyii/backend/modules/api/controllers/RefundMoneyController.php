<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/10/20
 * Time: 16:12
 */

namespace backend\modules\api\controllers;

use backend\components\Quanxian;
use backend\models\Account;
use backend\models\AuditAction;
use backend\models\JdContract;
use backend\models\ProductLine;
use backend\models\RenewHuikuan;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

use yii\rest\ActiveController;
use Yii;

class RefundMoneyController extends ActiveController
{
    public $modelClass='backend\models\RenewHuikuan';

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


    public function actionList_zongzhnaghu($uid='',$type=15){
        $data['code']=200;
        $data['data']=RenewHuikuan::tuikuan_list($uid,$type);
        return $data;
    }

    public function actionList_kehu($uid='',$type=14){
        $data['code']=200;
        $data['data']=RenewHuikuan::tuikuan_list($uid,$type);
        return $data;
    }

    public function actionAdd($contract_id){
        $data['code']='200';
        $data['contract_info']=JdContract::contractinfonomt($contract_id);
        $data['contract_account']=Account::account_contract_list($contract_id);
        return $data;
    }

    public static function actionAddru($shengcheng=''){
        $model=new RenewHuikuan();
        $postdata=\Yii::$app->request->post();

        $model->load(\Yii::$app->getRequest()->getBodyParams(),'');
        //如果是生成退款
        if($shengcheng!='')
        {

            $postdata=$shengcheng;
            $model->load($shengcheng,'');
        }else
        {

            $model->payment_time=strtotime($model->payment_time);
        }


        if($postdata['payment_type']==15){

            //账户信息
            $accountinfo=Account::findOne($model->account);
            //媒介合同信息
            $mcontractinfo=JdContract::findOne($accountinfo->mht_id);

            //获取实付金额
            $model->xf_cost=\Yii::$app->hjd->shifu_fun($model->show_money,$mcontractinfo['rebates_proportion'],$mcontractinfo['dl_fandian']);

        }

        //合同信息
        $contract_info=JdContract::contractinfonomt($model->xf_contractid);



        $model->ht_state=$contract_info['contract_state'];//合同状态
        $model->no_cost_xf=$model->show_money;
        $model->ctime=time();


        


        //如果是退款到客户
        if($postdata['payment_type']=='14')
        {
            $model->xf_qiane=$postdata['money'];
            //退款到客户如果合同余额不够退款金额 不让退款
            $conyue=$contract_info['huikuan']+$contract_info['bukuan']-$contract_info['yu_e'];

            //高精度对比
            $c = bccomp($postdata['money'],$conyue, 2);
            if($c==1)
            {
                return array("code"=>"500","msg"=>"退款金额不得大于本合同余额（{$conyue}）");
                exit;
            }
        }
        //如果是退款到总账户
        if($postdata['payment_type']==15)
        {
            //如果是询价类产品就不进行账户币对比

            $prlin=ProductLine::findOne($mcontractinfo->product_line);

            if($prlin['fk_type']=='0')
            {
                //跟系统计算账户比的公式进行对比。
                Yii::$app->hjd->show_money($model->money,$model->xf_contractid,$model->show_money);
            }



            $model->backmoney_yue=$postdata['money'];
            //计算回扣
            $model->yhuikou=$model->money-$model->money*(100+$model->rebates_proportion)/(100+$model->rebates_proportion+$contract_info->kh_grfd);
            $model->yhuikou=number_format($model->yhuikou, 2);

            $model->whuikou=$model->yhuikou;
        }


        if($model->save())
        {
            if($postdata['payment_type']==14)
            {
                RenewHuikuan::money_change_jian($postdata['advertiser'],$postdata['xf_contractid'],$postdata['payment_type'],$postdata['money']);
                //--RenewHuikuan::renew_huikuan($postdata['xf_contractid']);
                //修改公司发票回款状态
                Invoice::up_invoice_state($postdata['advertiser']);
            }
        }else
        {
           return $model->errors;
        }

        return array("code"=>"200","msg"=>"提交成功","data"=>$model);
    }

    //退款到客户1审
    public function actionShenhe1_tuikuan_kehu($id){
        //写入历史记录
        $this->global_audit($id,'refund_kehu',1,'jd_renew_huikuan');
        $tr1 = Yii::$app->db->beginTransaction();
        try {

            //审核续费后续
            RenewHuikuan::tkehu_shenhe($id,1);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'回款审核成功');
    }


    //退款到客户2审
    public function actionShenhe2_tuikuan_kehu($id){
        //写入历史记录
        $this->global_audit($id,'refund_kehu',2,'jd_renew_huikuan');
        $tr1 = Yii::$app->db->beginTransaction();
        try {

            //审核续费后续
            RenewHuikuan::tkehu_shenhe($id,2);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'回款审核成功');
    }

    //退款到客户2审
    public function actionShenhe3_tuikuan_kehu($id){
        //写入历史记录
        $this->global_audit($id,'refund_kehu',3,'jd_renew_huikuan');
        $tr1 = Yii::$app->db->beginTransaction();
        try {

            //审核续费后续
            RenewHuikuan::tkehu_shenhe($id,3);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'回款审核成功');
    }

    //退款到客户2审
    public function actionShenhe4_tuikuan_kehu($id){
        //写入历史记录
        $this->global_audit($id,'refund_kehu',4,'jd_renew_huikuan');
        $tr1 = Yii::$app->db->beginTransaction();
        try {

            //审核续费后续
            RenewHuikuan::tkehu_shenhe($id,4);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'回款审核成功');
    }

    //退款到客户2审
    public function actionShenhe5_tuikuan_kehu($id){
        //写入历史记录
        $this->global_audit($id,'refund_kehu',5,'jd_renew_huikuan');
        $tr1 = Yii::$app->db->beginTransaction();
        try {

            //审核续费后续
            RenewHuikuan::tkehu_shenhe($id,5);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'回款审核成功');
    }










    //退款到总账户审核操作 拼框套壳
    public function actionShenhe1_tuikuan_account_p($id){
        //写入历史记录
        $this->global_audit($id,'refund_account_p',1,'jd_renew_huikuan');
        $tr1 = Yii::$app->db->beginTransaction();
        try {


            //审核续费后续
            RenewHuikuan::account_shenhe($id,1,'refund_account_p');
            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'审核成功');

    }


    //退款到总账户审核操作 拼框套壳
    public function actionShenhe2_tuikuan_account_p($id){
        //写入历史记录
        $this->global_audit($id,'refund_account_p',2,'jd_renew_huikuan');
        $tr1 = Yii::$app->db->beginTransaction();
        try {

            //审核续费后续
            RenewHuikuan::account_shenhe($id,2,'refund_account_p');
            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'审核成功');

    }


    //退款到总账户审核操作 拼框套壳
    public function actionShenhe3_tuikuan_account_p($id){
        //写入历史记录
        $this->global_audit($id,'refund_account_p',3,'jd_renew_huikuan');
        $tr1 = Yii::$app->db->beginTransaction();
        try {

            //审核续费后续
            RenewHuikuan::account_shenhe($id,3,'refund_account_p');
            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'审核成功');

    }


    //退款到总账户审核操作 拼框套壳
    public function actionShenhe4_tuikuan_account_p($id){
        //写入历史记录
        $this->global_audit($id,'refund_account_p',4,'jd_renew_huikuan');
        $tr1 = Yii::$app->db->beginTransaction();
        try {

            //审核续费后续
            RenewHuikuan::account_shenhe($id,4,'refund_account_p');
            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'审核成功');

    }


    //退款到总账户审核操作 拼框套壳
    public function actionShenhe5_tuikuan_account_p($id){
        //写入历史记录
        $this->global_audit($id,'refund_account_p',5,'jd_renew_huikuan');
        $tr1 = Yii::$app->db->beginTransaction();
        try {

            //审核续费后续
            RenewHuikuan::account_shenhe($id,5,'refund_account_p');
            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'审核成功');

    }



    //退款到总账户  直开 一审
    public function actionShenhe1_tuikuan_account_z($id){

        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'refund_account_z',1,'jd_renew_huikuan');
            //审核续费后续
            RenewHuikuan::account_shenhe($id,1,'refund_account_z');
            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'审核成功');
    }


    //退款到总账户  直开 二审
    public function actionShenhe2_tuikuan_account_z($id){

        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'refund_account_z',2,'jd_renew_huikuan');
            //审核续费后续
            RenewHuikuan::account_shenhe($id,2,'refund_account_z');
            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'审核成功');
    }

    //退款到总账户  直开 二审
    public function actionShenhe3_tuikuan_account_z($id){

        $tr1 = Yii::$app->db->beginTransaction();
        try {
//写入历史记录
            $this->global_audit($id,'refund_account_z',3,'jd_renew_huikuan');
            //审核续费后续
            RenewHuikuan::account_shenhe($id,3,'refund_account_z');
            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'审核成功');
    }
    //退款到总账户  直开 二审
    public function actionShenhe4_tuikuan_account_z($id){

        $tr1 = Yii::$app->db->beginTransaction();
        try {
//写入历史记录
            $this->global_audit($id,'refund_account_z',4,'jd_renew_huikuan');
            //审核续费后续
            RenewHuikuan::account_shenhe($id,4,'refund_account_z');
            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'审核成功');
    }
    //退款到总账户  直开 二审
    public function actionShenhe5_tuikuan_account_z($id){

        $tr1 = Yii::$app->db->beginTransaction();
        try {
//写入历史记录
            $this->global_audit($id,'refund_account_z',5,'jd_renew_huikuan');
            //审核续费后续
            RenewHuikuan::account_shenhe($id,5,'refund_account_z');
            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'审核成功');
    }

    //退款详情
    public function actionRefundmoney_info($id){

        $data['data']=RenewHuikuan::tuikuaninfo($id);
        $data['code']=200;
        return $data;
    }

    //
}

