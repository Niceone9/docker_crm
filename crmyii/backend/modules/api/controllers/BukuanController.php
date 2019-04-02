<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/10/20
 * Time: 16:12
 */

namespace backend\modules\api\controllers;




use backend\models\JdContract;
use backend\models\RenewHuikuan;
use yii\rest\ActiveController;


use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

use Yii;
use backend\components\Quanxian;

class BukuanController extends ActiveController
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

    //公司补款列表
    public function actionList(){
        return RenewHuikuan::bukuan_list();
    }
    //新增合同补款
    public function  actionAdd($contract_id){
        $data['code']='200';
        $data['contract_info']=JdContract::contractinfonomt($contract_id);
        $data['code']=200;
        return $data;
    }


    //新增公司回款返回
    public function actionAddru(){
        $ad=RenewHuikuan::add_bukuan();
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
    public function actionBukuan_info($id){
        $data['code']='200';
        $data['data']=RenewHuikuan::bukuan_info($id);
        return $data;
    }

    //一级审核
    public function actionShenhe1($id){

        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'bukuan',1,'jd_renew_huikuan');
            //审核续费后续
            RenewHuikuan::bukuan_shenhe($id,1);

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
            $this->global_audit($id,'bukuan',2,'jd_renew_huikuan');
            //审核续费后续
            RenewHuikuan::bukuan_shenhe($id,2);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'补款审核成功');
    }

    //二级审核
    public function actionShenhe3($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'bukuan',3,'jd_renew_huikuan');
            //审核续费后续
            RenewHuikuan::bukuan_shenhe($id,3);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'补款审核成功');
    }
    //二级审核
    public function actionShenhe4($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'bukuan',4,'jd_renew_huikuan');
            //审核续费后续
            RenewHuikuan::bukuan_shenhe($id,4);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'补款审核成功');
    }
    //二级审核
    public function actionShenhe5($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'bukuan',5,'jd_renew_huikuan');
            //审核续费后续
            RenewHuikuan::bukuan_shenhe($id,5);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'补款审核成功');
    }




    public function actionAddbukuanbi(){
        $ad=RenewHuikuan::addbukuanbi();
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




    //一级审核
    public function actionBishenhe1($id){

        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'bukuanbi',1,'jd_renew_huikuan');
            //审核续费后续
            RenewHuikuan::bukuanbi_shenhe($id,1);

            $tr1->commit(); //提交数据bu

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'补款审核成功');
    }

    //二级审核
    public function actionBishenhe2($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'bukuanbi',2,'jd_renew_huikuan');
            //审核续费后续
            RenewHuikuan::bukuanbi_shenhe($id,2);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'补款审核成功');
    }

    //二级审核
    public function actionBishenhe3($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'bukuanbi',3,'jd_renew_huikuan');
            //审核续费后续
            RenewHuikuan::bukuanbi_shenhe($id,3);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'补款审核成功');
    }
    //二级审核
    public function actionBishenhe4($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'bukuanbi',4,'jd_renew_huikuan');
            //审核续费后续
            RenewHuikuan::bukuanbi_shenhe($id,4);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'补款审核成功');
    }
    //二级审核
    public function actionBishenhe5($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'bukuanbi',5,'jd_renew_huikuan');
            //审核续费后续
            RenewHuikuan::bukuanbi_shenhe($id,5);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'补款审核成功');
    }

}