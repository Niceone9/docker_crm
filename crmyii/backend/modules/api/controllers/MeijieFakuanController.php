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
use backend\models\MrenewHuikuan;
use backend\models\RenewHuikuan;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

use yii\rest\ActiveController;
use Yii;

class MeijieFakuanController extends ActiveController
{
    public $modelClass='backend\models\MrenewHuikuan';

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


    public function actionList(){
        $data['code']=200;
        $data['data']=MrenewHuikuan::fakuan_list();
        return $data;
    }



    public function actionAdd($contract_id){
        $data['code']='200';
        $data['contract_info']=JdContract::meijie_contractinfo($contract_id);
        $data['contract_account']=Account::meijie_account_contract_list($contract_id);
        return $data;
    }

    public function actionAddru(){
        $model=new MrenewHuikuan();
        $postdata=\Yii::$app->request->post();

        $model->load(\Yii::$app->getRequest()->getBodyParams(),'');
        $model->payment_time=strtotime($model->payment_time);
        $model->ctime=time();
        //合同信息

        $contract_info=JdContract::meijie_contractinfo($model->xf_contractid);

        //获取实付金额
        $model->xf_cost=$postdata['money'];
        $model->xf_qiane=$postdata['money'];
        $model->mht_id=$postdata['xf_contractid'];
        $model->payment_type=19;


        if($model->save())
        {
            return array("code"=>"200","msg"=>"提交成功","data"=>$model);
        }else
        {
            return array("code"=>"500","msg"=>'提交失败',"data"=>$model->errors);
        }


    }




    //退款到总账户  直开 一审
    public function actionShenhe1($id){

        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'meijie_fakuan',1,'jd_mrenew_huikuan');
            //审核续费后续
            MrenewHuikuan::fakuan_shenhe($id,1);
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
            $this->global_audit($id,'meijie_fakuan',2,'jd_mrenew_huikuan');
            //审核续费后续
            MrenewHuikuan::fakuan_shenhe($id,2);
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
            $this->global_audit($id,'meijie_fakuan',3,'jd_mrenew_huikuan');
            //审核续费后续
            MrenewHuikuan::fakuan_shenhe($id,3);
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
            $this->global_audit($id,'meijie_fakuan',4,'jd_mrenew_huikuan');
            //审核续费后续
            MrenewHuikuan::fakuan_shenhe($id,4);
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
            $this->global_audit($id,'meijie_fakuan',5,'jd_mrenew_huikuan');
            //审核续费后续
            MrenewHuikuan::fakuan_shenhe($id,5);
            $tr1->commit(); //提交数据
        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'审核成功');
    }


    //退款详情
    public function actionFakuan_info($id){

        $data['data']=MrenewHuikuan::fakuaninfo($id);
        $data['code']=200;
        return $data;
    }
}

