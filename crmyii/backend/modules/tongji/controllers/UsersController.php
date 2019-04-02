<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017/5/21
 * Time: 22:50
 */

namespace api\modules\crm\controllers;
use api\models\AccountConsumption;
use api\models\RenewHuikuan;
use api\models\Users;
use common\models\User;
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;


class UsersController extends  ActiveController
{

    public $modelClass='api\models\Users';
    public function behaviors()
    {

        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                HttpBasicAuth::className(),
                HttpBearerAuth::className(),
                QueryParamAuth::className(),
            ],
        ];
        return $behaviors;
    }

    public function actionHjd($id){
        $uid=$id?$id:16;
        return Users::findOne($uid);
    }
    //根据appid 读取销售名称
    public function actionAppid_to_markey($appid){
        return Users::appid_to_markey($appid);
    }
    //销售提成
    public function actionMarket_ticheng($start,$end,$users){
        if($start!='default' && $end!='default') {
            $start = strtotime($start);
            $end = strtotime($end);
        }
        return RenewHuikuan::market_ticheng($start,$end,$users);
    }
    //销售已回款提成
    public function actionMarket_ticheng_yihuikuan($start,$end,$users){
        if($start!='default' && $end!='default') {
            $start = strtotime($start);
            $end = strtotime($end);
        }
        return RenewHuikuan::market_ticheng_yihuikuan($start,$end,$users);
    }

}