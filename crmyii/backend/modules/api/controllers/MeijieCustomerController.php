<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/10/12
 * Time: 17:24
 */
namespace backend\modules\api\controllers;
use backend\models\JdCustomer as Customer;
use yii\rest\ActiveController;

use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

use Yii;
use backend\components\Quanxian;

class MeijieCustomerController extends ActiveController
{
    public $modelClass = 'backend\models\JdCustomer';
    //页面显示分页信息
    public $serializer=[
        'class'=>'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

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


    public function actionAdd(){
        $model=new Customer();
        $postdata = \Yii::$app->request->post();
        $model->load(Yii::$app->getRequest()->getBodyParams(),'');
        $model->ctime=time();
        $model->r_time=time();
        $model->audit_count=1;
        if($model->save())
        {
            $data['code']='200';
            $data['msg']='添加成功';
            $data['data']=$model;
        }
        else{
            $data['code']="500";
            $data['msg']="添加失败";
            $data['data']=$model->errors;
        }
        return $data;
    }




    //返回 销售自己的客户  参数 销售id
    public function actionCustomer_list(){
        $data['list']=Customer::meijie_market_list();
        $data['code']=200;
        return $data;
    }
    /*
    //分配销售 或者分配商务  接收数组
    public function actionUpcustomermarket($custmoerarray,$users,$type){
       $data['data']=Customer::upmarket($custmoerarray,$users,$type);
       $data['code']=200;
       return $data;
    }
    */

}