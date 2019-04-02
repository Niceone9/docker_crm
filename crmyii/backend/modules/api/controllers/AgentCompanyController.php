<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/10/20
 * Time: 16:12
 */

namespace backend\modules\api\controllers;

use backend\models\AgentCompany;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use backend\components\Quanxian;

use yii\rest\ActiveController;

class AgentCompanyController extends ActiveController
{
    public $modelClass='backend\models\AgentCompany';
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


    //产品线列表
    public function actionDaililist(){

        return AgentCompany::dl_list();
    }
    //新增产品线
    public function  actionAdd($id){

        $data['code']=200;
        return $data;

    }

    public function actionDeletea($id){
        if(AgentCompany::deleteAll(['id'=>$id])){
            return array('code'=>"200","msg"=>'删除成功');
        }else
        {
            return array('code'=>"500","msg"=>'删除失败');
        }
    }


    //新增公司回款返回
    public function actionAddru(){

        if($datadat=AgentCompany::add_dl()){
            $data['code']='200';
            $data['data']=$datadat;
            return  $data;
        }else
        {
            $data['code']='200';
            $data['msg']='提交失败';
        }
    }


    //
    public function actionDaili_info($id){
        $data['code']='200';
        $data['data']=AgentCompany::dl_info($id);
        return $data;
    }

    public function actionUpprdaili($id){
        $model=AgentCompany::findOne($id);
        $model->load(\Yii::$app->getRequest()->getBodyParams(),'');

        if($model->save())
        {
            return $model;
        }else
        {
            return array('code'=>500,'msg'=>'修改成功','data'=>$model->errors);
        }
    }

}

