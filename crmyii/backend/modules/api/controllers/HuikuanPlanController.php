<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/10/20
 * Time: 16:12
 */

namespace backend\modules\api\controllers;


use backend\models\AgentCompany;


use backend\models\Holiday;
use backend\models\HuikuanPlan;
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

class HuikuanPlanController extends ActiveController
{
    public $modelClass='backend\models\Holiday';

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
        $data['code']='200';
        $data['data']=HuikuanPlan::getPlan();
        return  $data;
    }

    public function actionAdPlanList(){
        $data['code']='200';
        $data['data']=HuikuanPlan::getPlanAdList();
        return $data;
    }

    public function actionHuikuan_plan_list_ad(){
        $data['code']='200';
        $data['data']=HuikuanPlan::getPlanAd();
        return  $data;
    }
    //新增公司回款返回
    public function actionAddru(){

         if($datadat=HuikuanPlan::createPlan()){
            $data['code']='200';
            $data['data']=$datadat;
            return  $data;
         }else
         {
             $data['code']='200';
             $data['msg']='提交失败';
         }
    }

    //获取计划回款总数  按日期
    public function actionGetPlanGroup(){
        $data= HuikuanPlan::getPlanGroup();

        return ['code'=>'200','data'=>$data];
    }

    //获取计划回款和实际回款总数
    public function actionGetPlanCost(){
        $data=HuikuanPlan::GetPlanCost();
        return ['code'=>'200','data'=>$data];
    }

    //获取未按时回款的客户列表
    public function actionGetOverdueAd(){
        $data['code']='200';
        $data['data']=HuikuanPlan::GetOverdueAd();
        return  $data;
    }

    public function actionUpPlan($id){
        if($datadat=HuikuanPlan::upPlan($id)){
            $data['code']='200';
            $data['data']=$datadat;
            return  $data;
        }else
        {
            $data['code']='200';
            $data['msg']='提交失败';
        }
    }





}