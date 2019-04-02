<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/10/20
 * Time: 16:12
 */

namespace backend\modules\api\controllers;


use backend\models\AgentCompany;

use backend\models\Invoice;

use backend\models\JdCustomer;
use backend\models\Piaotype;
use yii\rest\ActiveController;


use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;


use Yii;
use backend\components\Quanxian;

class InvoiceController extends ActiveController
{
    public $modelClass='backend\models\BackMoney';

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
        return Invoice::fp_list();
    }
    //新增发票
    public function  actionAdd($id){

        //客户信息
        $data['customer']=JdCustomer::kehu_info_sm($id);

        //代理公司
        $data['agentCompany']=AgentCompany::find()->all();
        $data['code']=200;
        return $data;

    }

    //开票类型
    public function actionFptype($agentcompany_id){
        return Piaotype::find()->where(['advertiser'=>$agentcompany_id])->all();
    }

    //新增公司回款返回
    public function actionAddru(){

        $tr1 = Yii::$app->db->beginTransaction();
        try {
           $datadat= Invoice::add_fapiao();
            //更新发票状态



            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        $data['code']='200';
        $data['data']=$datadat;
        return  $data;
    }


    //修改发票
    public function actionUpinvoice($id){
        $data['invoice_info']=Invoice::invoice_info($id);
        $data['code']="200";
        return $data;
    }

    //修改发票返回
    public function actionUpinvoice_ru($id){
        $model=Invoice::findOne($id);
        $model->load(Yii::$app->getRequest()->getBodyParams(),'');
        $model->kp_time = strtotime(Yii::$app->getRequest()->getBodyParams()['kp_time']);
        if($model->save())
        {
            return array("code"=>"200","data"=>$model);
        }else
        {
            return array("code"=>"500","data"=>$model->errors);
        }
    }

    //回款详情
    public function actionInvoice_info($id){
        $data['code']='200';
        $data['data']=Invoice::invoice_info($id);
        return $data;
    }

    //一级审核
    public function actionShenhe1($id){

        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'invoice',1,'jd_invoice');
            //审核续费后续
            Invoice::fapiao_shenhe($id,1);

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
            $this->global_audit($id,'invoice',2,'jd_invoice');
            //审核续费后续
            Invoice::fapiao_shenhe($id,2);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'审核成功');

    }
    //二级审核
    public function actionShenhe3($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'invoice',3,'jd_invoice');
            //审核续费后续
            Invoice::fapiao_shenhe($id,3);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'审核成功');

    }
    //二级审核
    public function actionShenhe4($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'invoice',4,'jd_invoice');
            //审核续费后续
            Invoice::fapiao_shenhe($id,4);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'审核成功');

    }
    //二级审核
    public function actionShenhe5($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'invoice',5,'jd_invoice');
            //审核续费后续
            Invoice::fapiao_shenhe($id,5);

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