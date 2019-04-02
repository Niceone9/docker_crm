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



class MeijieRenewHuikuanController extends ActiveController
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
        $data['data']=MrenewHuikuan::xufei_list($uid);
        return $data;
    }




    //打款列表
    public function actionDklist($uid=''){

        $data['code']=200;
        $data['data']=MrenewHuikuan::xufei_list($uid);
        return $data;
    }

    //续费详情
    public function actionRenewInfo($id){
        //要续费的合同信息
        $data['code']='200';
        $data['data']=array(
            "contract"=>MrenewHuikuan::renewinfo($id)
        );
        return $data;
    }

}