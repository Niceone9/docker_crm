<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/10/20
 * Time: 16:12
 */

namespace backend\modules\api\controllers;




use backend\models\Beikuan;
use backend\models\BeikuanAccount;
use backend\models\JdContract;
use backend\models\MrenewHuikuan;
use backend\models\RenewHuikuan;
use yii\rest\ActiveController;


use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

use Yii;
use backend\components\Quanxian;

class BeikuanController extends ActiveController
{
    public $modelClass='backend\models\Beikuan';

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

    //备款列表
    public function actionList(){
        return Beikuan::beikuan_list();
    }
    //新增合同补款
    public function  actionAdd($contract_id){
        $data['code']='200';
        $data['contract_info']=JdContract::contractinfonomt($contract_id);
        $data["accountlist"]=BeikuanAccount::account_contract_list($contract_id);
        $data['code']=200;
        return $data;
    }


    //新增公司回款返回
    public function actionAddru(){
        $ad=Beikuan::add_beikuan();
        if($ad[id]){
            $data['code']='200';
            $data['data']=$ad;
        }else
        {
            $data['code']='500';
            $data['data']=$ad;
        }
       return  $data;
    }

    //补款详情
    public function actionBeikuan_info($id){
        $data['code']='200';
        $data['data']=Beikuan::beikuan_info($id);
        return $data;
    }

    //一级审核
    public function actionShenhe1($id){

        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'beikuan',1,'beikuan');
            //审核续费后续
            Beikuan::beikuan_shenhe($id,1);

            $tr1->commit(); //提交数据bu

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'补款审核成功');
    }

    //二级审核
    public function actionShenhe2($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'beikuan',2,'beikuan');
            //审核续费后续
            Beikuan::beikuan_shenhe($id,2);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'补款审核成功');
    }

    public function actionShenhe3($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'beikuan',3,'beikuan');
            //审核续费后续
            Beikuan::beikuan_shenhe($id,3);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'补款审核成功');
    }
    public function actionShenhe4($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'beikuan',4,'beikuan');
            //审核续费后续
            Beikuan::beikuan_shenhe($id,4);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'补款审核成功');
    }
    public function actionShenhe5($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'beikuan',5,'beikuan');
            //审核续费后续
            Beikuan::beikuan_shenhe($id,5);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'补款审核成功');
    }


    public function actionRefundMoneyAdd(){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            Beikuan::add_refund_money();

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'添加成功');
    }

    //备款账户退款列表
    public function actionListTuikuan(){
        return Beikuan::beikuan_tui_list();
    }

    //退款详情
    public function actionBeikuan_tuikuan_info($id){
        $data['code']='200';
        $data['data']=Beikuan::beikuan_info($id);
        return $data;
    }


    //一级审核
    public function actionTuikuanShenhe1($id){

        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'beikuan_account_tuikuan',1,'beikuan');
            //审核续费后续
            Beikuan::beikuan_tui_shenhe($id,1);

            $tr1->commit(); //提交数据bu

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'备款退款审核成功');
    }

    //二级审核
    public function actionTuikuanShenhe2($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'beikuan_account_tuikuan',2,'beikuan');
            //审核续费后续
            Beikuan::beikuan_tui_shenhe($id,2);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'备款退款审核成功');
    }

    public function actionTuikuanShenhe3($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'beikuan_account_tuikuan',3,'beikuan');
            //审核续费后续
            Beikuan::beikuan_tui_shenhe($id,3);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'备款退款审核成功');
    }
    public function actionTuikuanShenhe4($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'beikuan_account_tuikuan',4,'beikuan');
            //审核续费后续
            Beikuan::beikuan_tui_shenhe($id,4);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'备款退款审核成功');
    }
    public function actionTuikuanShenhe5($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'beikuan_account_tuikuan',5,'beikuan');
            //审核续费后续
            Beikuan::beikuan_tui_shenhe($id,5);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'备款退款审核成功');
    }





}