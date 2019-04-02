<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/10/12
 * Time: 17:24
 */
namespace backend\modules\api\controllers;
use backend\models\CustomerAdvertiser;
use backend\models\CustomerAdvertiserReleveance;
use backend\models\Customerb;
use backend\models\JdCustomer as Customer;
use yii\rest\ActiveController;

use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

use Yii;
use backend\components\Quanxian;

class CustomerController extends ActiveController
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
            if(\Yii::$app->params['yushan_synchronizing']=='true') {
                if ($model->customer_type == '2') {
                    //$url = "http://www.yushanapp.com/sem/createsem";
                    $url = Yii::$app->params['yushancom']."sem/createsem";
                    $post_data = array(
                        "name" => $model->advertiser,
                        "password" => md5('123456'),
                        "email" => '123@qq.com',
                        "phone" => '12345678910',
                        "realname" => $model->advertiser,
                        "type" => '100004',
                    );

                    $yushan_data = Yii::$app->hjd->hjd_post_curl($url, $post_data);
                    $yushan_id = $yushan_data->data->id;
                    if ($yushan_id != '') {
                        Customer::updateAll(['yushan_id' => $yushan_id], ['id' => $model->id]);

                    }
                }
            }
            //修改客保id
            Customerb::updateAll(['ytime'=>time()],['id'=>$postdata['kbid']]);
            //新建广告主
            $ad= new CustomerAdvertiser();
            $ad->advertiser=$model->advertiser;
            $ad->advertiser_for_short='crm_';
            $ad->customer_id=$model->id;
            $ad->ctime=time();

            $ad->save();

            //新建产品
            $pr=new CustomerAdvertiserReleveance();
            $pr->advertiser=$ad->id;
            $pr->product=$model->appname;
            $pr->product_short_name=$model->appname;
            $pr->save();



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

            $data['list']=Customer::market_list();

        $data['code']=200;
        return $data;
    }


    //修改客户信息
    public function actionUpcustomerinfo($id){

    }

    //分配销售 或者分配商务  接收数组
    public function actionUpcustomermarket($custmoerarray,$users,$type){
       $data['data']=Customer::upmarket($custmoerarray,$users,$type);
       $data['code']=200;
       return $data;
    }

    public function actionShenhe(){

    }

    public function actionMinfo($id)
    {
        $info=Customer::find()->where(['id'=>$id])->asArray()->one();

        $info['balance']=round($info['dakuan']-$info['myu_e']-$info['fakuan']-$info['beikuan_yue'],2);
        return $info;
    }



}