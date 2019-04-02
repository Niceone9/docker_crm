<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/10/20
 * Time: 16:12
 */

namespace backend\modules\api\controllers;

use backend\components\Quanxian;

use backend\models\JdContract;
use backend\models\RenewHuikuan;
use backend\models\Yihuikuanxufei;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

use yii\rest\ActiveController;
use Yii;

class YihuikuanxufeiController extends ActiveController
{
    public $modelClass='backend\models\Yihuikuanxufei';

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
        $data['data']=Yihuikuanxufei::yihuikuanxufeilist();
        $data['code']="200";
        return $data;
    }

    public function actionUpgerenfandian($id,$fandian){
        $model=Yihuikuanxufei::findOne($id);
        $model->gr_fandian=$fandian;
        if($model->save())
        {
            $data['code']="200";
            $data['data']=$model;
            $data['msg']="修改成功";
        }else
        {
            $data['code']="500";
            $data['data']=$model->errors;
            $data['msg']="修改失败";
        }
        return $data;
    }
}

