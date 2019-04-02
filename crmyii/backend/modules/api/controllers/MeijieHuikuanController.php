<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/10/20
 * Time: 16:12
 */

namespace backend\modules\api\controllers;


use backend\models\AgentCompany;
use backend\models\BackMoney;
use backend\models\JdContract;
use backend\models\JdCustomer;
use backend\models\MbackMoney;
use backend\models\RenewHuikuan;
use yii\rest\ActiveController;


use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

use Yii;
use backend\components\Quanxian;

class MeijieHuikuanController extends ActiveController
{
    public $modelClass='backend\models\MbackMoney';

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

    //公司回款列表
    public function actionList(){
        return MbackMoney::back_money_list();
    }
    //新增公司回款
    public function  actionAdd($id){

        //客户信息
       $data['customer']=JdCustomer::kehu_info_sm($id);

        //代理公司
        $data['agentCompany']=AgentCompany::find()->all();
        $data['code']=200;
        return $data;

    }


    //新增公司回款返回
    public function actionAddru(){
        $data['code']='200';
        $data['data']=MbackMoney::add_backmoney();
       return  $data;
    }

    //回款详情
    public function actionBack_money_info($id){
        $data['code']='200';
        $data['data']=MbackMoney::backmonet_info($id);
        return $data;
    }

    //一级审核b
    public function actionShenhe1($id){

        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'meijie-huikuan',1,'jd_mback_money');
            //审核续费后续
            MbackMoney::backmoney_shenhe($id,1);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'回款审核成功');
    }

    //二级审核
    public function actionShenhe2($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'meijie-huikuan',2,'jd_mback_money');
            //审核续费后续
            MbackMoney::backmoney_shenhe($id,2);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'回款审核成功');

    }

    public function actionShenhe3($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'meijie-huikuan',3,'jd_mback_money');
            //审核续费后续
            MbackMoney::backmoney_shenhe($id,3);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'回款审核成功');

    }

    public function actionShenhe4($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'meijie-huikuan',4,'jd_mback_money');
            //审核续费后续
            MbackMoney::backmoney_shenhe($id,4);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'回款审核成功');

    }

    public function actionShenhe5($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'meijie-huikuan',5,'jd_mback_money');
            //审核续费后续
            MbackMoney::backmoney_shenhe($id,5);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'回款审核成功');

    }

    //待审核回款
    public function actionDshenhebackmoney($id){
        $a= MbackMoney::find()->where("audit!=2 and is_ultimate_shenhe!=1 and advertiser=$id and type=2")->sum('b_money');
        if(!$a){
            $a='';
        }
        return $a;
    }
    //待审核打款
    public function actionDshenhedamoney($id){
        $a= MbackMoney::find()->where("audit!=2 and is_ultimate_shenhe!=1 and advertiser=$id and type=1")->sum('b_money');
        if(!$a){
            $a='';
        }
        return $a;
    }

}