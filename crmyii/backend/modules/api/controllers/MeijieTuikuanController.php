<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/10/27
 * Time: 9:40
 */

namespace backend\modules\api\controllers;


use backend\models\MrenewHuikuan;

use Prophecy\Exception\Prediction\NoCallsException;
use Yii;

use yii\rest\ActiveController;

use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

use backend\components\Quanxian;



class MeijieTuikuanController extends ActiveController
{
    public $modelClass='backend\models\MrenewHuikuan';
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
        $data['data']=MrenewHuikuan::meijietuikuan_list($uid);
        return $data;
    }




    //续费详情
    public function actionInfo($id){
        //要续费的合同信息
        $data['code']='200';
        $data['data']=array(
            "contract"=>MrenewHuikuan::meitituikuaninfo($id)
        );
        return $data;
    }



    //退款到总账户  直开 一审
    public function actionShenhe1($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'meijie_tuikuan',1,'jd_mrenew_huikuan');
            //审核续费后续
            MrenewHuikuan::meijie_tuikuanshenhe($id,1);
            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'审核成功');
    }


    //退款到总账户  直开 二审
    public function actionShenhe2($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'meijie_tuikuan',2,'jd_mrenew_huikuan');
            //审核续费后续
            MrenewHuikuan::meijie_tuikuanshenhe($id,2);
            $tr1->commit(); //提交数据
        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'审核成功');
    }

    //退款到总账户  直开 二审
    public function actionShenhe3($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'meijie_tuikuan',3,'jd_mrenew_huikuan');
            //审核续费后续
            MrenewHuikuan::meijie_tuikuanshenhe($id,3);
            $tr1->commit(); //提交数据
        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'审核成功');
    }
    //退款到总账户  直开 二审
    public function actionShenhe4($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'meijie_tuikuan',4,'jd_mrenew_huikuan');
            //审核续费后续
            MrenewHuikuan::meijie_tuikuanshenhe($id,4);
            $tr1->commit(); //提交数据
        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'审核成功');
    }
    //退款到总账户  直开 二审
    public function actionShenhe5($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'meijie_tuikuan',5,'jd_mrenew_huikuan');
            //审核续费后续
            MrenewHuikuan::meijie_tuikuanshenhe($id,5);
            $tr1->commit(); //提交数据
        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'审核成功');
    }



}