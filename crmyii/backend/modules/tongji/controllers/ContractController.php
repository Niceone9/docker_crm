<?php

namespace backend\modules\tongji\controllers;

use backend\models\ContractNew;
use backend\models\JdContract;
use yii\rest\ActiveController;
use yii\web\Controller;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
/**
 * Default controller for the `crm` module
 */
class ContractController extends ActiveController
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public $modelClass='backend\models\JdContract';
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

    //根据时间返回期间新建的合同数量
    public function actionContract_date_sum($state_time,$end_time){

        $count= ContractNew::contract_data_count($state_time,$end_time);
        return array("code"=>'200',"count"=>$count);


    }


    public function actionContract_date_list($state_time,$end_time){
        $count= ContractNew::contract_data_list($state_time,$end_time);
        return array("code"=>'200',"count"=>$count);

    }
}
