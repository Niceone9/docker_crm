<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/10/20
 * Time: 16:12
 */

namespace backend\modules\api\controllers;


use backend\models\AgentCompany;

use backend\models\JdContract;
use backend\models\JdCustomer;
use backend\models\MbackMoney;
use backend\models\QdDakuan;

use yii\rest\ActiveController;


use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

use Yii;
use backend\components\Quanxian;

class QdDakuanController extends ActiveController
{
    public $modelClass='backend\models\QdDakuan';

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
        return QdDakuan::qd_dakuan_list();
    }
    //新增公司回款
    public function  actionAdd($contract_id){

        //客户信息
       $data['contractinfo']=JdContract::contractinfonomt($contract_id);

        //查询最近一笔打款读出开票信息。
        $zjdakuan=QdDakuan::find()->select('kaihuhang,shoukuanren,zhanghu')->where("contract_id=$contract_id and zhanghu!=''")->groupBy('kaihuhang,shoukuanren,zhanghu')->all();

        $data['zuijin']=$zjdakuan;


        //代理公司
        $data['agentCompany']=AgentCompany::find()->all();
        $data['code']=200;
        return $data;

    }


    //新增公司回款返回
    public function actionAddru(){
        $data['code']='200';
        $data['data']=QdDakuan::add_qddakuan();
       return  $data;
    }

    //回款详情
    public function actionQd_dakuan_info($id){
        $data['code']='200';
        $data['data']=QdDakuan::qdakuan_info($id);
        return $data;
    }

    //一级审核
    public function actionShenhe1($id){

        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'qd_dakuan',1,'jd_qd_dakuan');
            //审核续费后续
            QdDakuan::qddakuan_shenhe($id,1);

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
            $this->global_audit($id,'qd_dakuan',2,'jd_qd_dakuan');
            //审核续费后续
            QdDakuan::qddakuan_shenhe($id,2);

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
            $this->global_audit($id,'qd_dakuan',3,'jd_qd_dakuan');
            //审核续费后续
            QdDakuan::qddakuan_shenhe($id,3);

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
            $this->global_audit($id,'qd_dakuan',4,'jd_qd_dakuan');
            //审核续费后续
            QdDakuan::qddakuan_shenhe($id,4);

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
            $this->global_audit($id,'qd_dakuan',5,'jd_qd_dakuan');
            //审核续费后续
            QdDakuan::qddakuan_shenhe($id,5);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'回款审核成功');

    }

}