<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/10/20
 * Time: 16:12
 */

namespace backend\modules\api\controllers;

use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use backend\components\Quanxian;

use backend\models\ProductLine;
use yii\rest\ActiveController;

class ProductlineminController extends ActiveController
{
    public $modelClass='backend\models\ProductLine';

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
    public function actionPrlinlist(){
        return ProductLine::pr_list();
    }
    //新增产品线
    public function  actionAdd($id){

        $data['code']=200;
        return $data;

    }


    //新增公司回款返回
    public function actionAddru(){

        if($datadat=ProductLine::add_prlin()){
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
    public function actionPr_info($id){
        $data['code']='200';
        $data['data']=ProductLine::pr_info($id);
        return $data;
    }

    public function actionUpprlin($id){
        $model=ProductLine::findOne($id);
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
        if(ProductLine::deleteAll(['id'=>$id])){
            return array('code'=>"200","msg"=>'删除成功');
        }else
        {
            return array('code'=>"500","msg"=>'删除失败');
        }
    }


}