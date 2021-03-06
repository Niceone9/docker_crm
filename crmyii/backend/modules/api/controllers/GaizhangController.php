<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/10/20
 * Time: 16:12
 */

namespace backend\modules\api\controllers;


use app\models\Gaizhang;
use backend\models\AgentCompany;



use backend\models\JdCustomer;
use backend\models\Piaotype;
use backend\models\Waichu;
use yii\rest\ActiveController;


use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;


use Yii;
use backend\components\Quanxian;

class GaizhangController extends ActiveController
{
    public $modelClass='backend\models\Gaizhang';

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

    //发票列表
    public function actionList(){
        $data['data']=Gaizhang::g_list();
        $data['code']=200;
        return $data;
    }
    //新增发票
    public function  actionAdd($id){

        $data['code']=200;
        return $data;

    }


    //新增公司回款返回
    public function actionAddru(){

         if($datadat=Gaizhang::add_g()){
            $data['code']='200';
            $data['data']=$datadat;
            return  $data;
         }else
         {
             $data['code']='200';
             $data['msg']='提交失败';
         }
    }




    //回款详情
    public function actionGaizhang_info($id){
        $data=Gaizhang::gaizhang_info($id);
        return $data;
    }

    //一级审核
    public function actionShenhe1($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'gaizhang',1,'gaizhang');
            //审核续费后续
            Gaizhang::gaizhang_shenhe($id,1);
            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'审核成功');
    }

    //二级审核
    public function actionShenhe2($id){


        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'gaizhang',2,'gaizhang');

            //审核续费后续
            Gaizhang::gaizhang_shenhe($id,2);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {

            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'审核成功');

    }

    public function actionShenhe3($id){


        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'gaizhang',3,'gaizhang');
            //审核续费后续
            Gaizhang::gaizhang_shenhe($id,3);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'审核成功');

    }

    public function actionShenhe4($id){


        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'gaizhang',4,'gaizhang');
            //审核续费后续
            Gaizhang::gaizhang_shenhe($id,4);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'审核成功');

    }

    public function actionShenhe5($id){


        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'gaizhang',5,'gaizhang');
            //审核续费后续
            Gaizhang::gaizhang_shenhe($id,5);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'审核成功');

    }

    //

}