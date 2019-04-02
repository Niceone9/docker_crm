<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/10/20
 * Time: 16:12
 */

namespace backend\modules\api\controllers;

use backend\models\AgentCompany;
use backend\models\Piaotype;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use backend\components\Quanxian;

use yii\rest\ActiveController;

class PiaotypeController extends ActiveController
{
    public $modelClass='backend\models\Piaotype';
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
    public function actionPiaotypelist(){

        return Piaotype::dl_list();
    }
    //新增产品线
    public function  actionAdd(){
        $list=AgentCompany::find()->orderBy('id asc')->all();

        $data['daili']=$list;

        $data['code']=200;
        return $data;

    }


    //新增公司回款返回
    public function actionAddru(){

        if($datadat=Piaotype::add_dl()){
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
    public function actionPiaotype_info($id){
        $data['code']='200';
        $data['data']=Piaotype::dl_info($id);
        return $data;
    }

    public function actionUppiaotype($id){
        $model=Piaotype::findOne($id);
        $model->load(\Yii::$app->getRequest()->getBodyParams(),'');

        if($model->save())
        {
            return $model;
        }else
        {
            return array('code'=>500,'msg'=>'修改成功','data'=>$model->errors);
        }
    }

    public function actionDeletea($id){
        if(Piaotype::deleteAll(['id'=>$id])){
            return array('code'=>"200","msg"=>'删除成功');
        }else
        {
            return array('code'=>"500","msg"=>'删除失败');
        }
    }
}

