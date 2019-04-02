<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/10/20
 * Time: 16:12
 */

namespace backend\modules\api\controllers;


use backend\models\MarketScore;


use backend\models\Holiday;
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

class MarketScoreController extends ActiveController
{
    public $modelClass='backend\models\MarketScore';

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
       //  $behaviors['myBehavior']=[
       //    'class'=>Quanxian::className(),
       //];
        $behaviors['rateLimiter']['enableRateLimitHeaders'] = false;
        return $behaviors;
    }

    //发票列表
    public function actionList(){
        return MarketScore::pf_list();
    }
    //新增发票
    public function  actionAdd($id){

        $data['code']=200;
        return $data;

    }


    //新增公司回款返回
    public function actionAddru(){

         if($datadat=Holiday::add_qingjia()){
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
    public function actionQingjia_info($id){
        $data['code']='200';
        $data['data']=Holiday::qingjia_info($id);
        return $data;
    }


}