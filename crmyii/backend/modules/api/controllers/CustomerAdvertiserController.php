<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/10/12
 * Time: 17:24
 */
namespace backend\modules\api\controllers;
use backend\models\Account;
use backend\models\ContractNew;
use backend\models\CustomerAdvertiser as Customer;
use backend\models\CustomerAdvertiser;
use backend\models\JdCustomer;
use yii\rest\ActiveController;

use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

use Yii;
use backend\components\Quanxian;

class CustomerAdvertiserController extends ActiveController
{
    public $modelClass = 'backend\models\CustomerAdvertiser';
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

        $tr = \Yii::$app->db->beginTransaction();
        try {
            // 添加广告主
            $insstate= CustomerAdvertiser::addru();

            if($insstate->id){
                $data= array('code'=>200,'mgs'=>'提交成功','data'=>$insstate);
            }elseif($insstate['code']=='500')
            {
                $data= $insstate;
            }

            $tr->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }

        return $data;



    }




    //返回合同下的所有广告主
    public function actionCustomer_list($customer_id){
        $data['customer_info']=JdCustomer::kehu_info_sm($customer_id);
        $data['list']=Customer::ad_list($customer_id);
        $data['code']=200;
        return $data;
    }

    //分配销售 或者分配商务  接收数组
    public function actionUpcustomermarket($custmoerarray,$users,$type){
       $data['data']=Customer::upmarket($custmoerarray,$users,$type);
       $data['code']=200;
       return $data;
    }

    //根据账户id 获取广告主和产品
    public function actionAccounttoadpr($id){
        $info=Account::find()
            ->select('b.advertiser,c.product')
            ->join('a left join','customer_advertiser b','a.cu_ad_id=b.id')
            ->leftJoin('customer_advertiser_releveance c','a.cu_ad_product=c.id')
            ->where(['a.id'=>$id])
            ->asArray()
            ->one();

        if(!$info){
            $a=array('code'=>'500','msg'=>'该账户没有信息');
        }else
        {
            $a=array('code'=>'200','msg'=>$info);
        }
        return $a;

    }

    //根据广告主获取账户
    public function actionAdtoaccount($id){
        $contractid=Yii::$app->request->get('contractid');

        return Account::find()->where(['cu_ad_id'=>$id,'endtime'=>'4092599349','contract_id'=>$contractid])
            ->asArray()->all();
    }





    public function actionInfo($id){
        $data=CustomerAdvertiser::findOne($id);
        return array('code'=>'200','data'=>$data);
    }

    public function actionUpadvertiser($id){

        $data=CustomerAdvertiser::upadvertiser($id);
        return array('code'=>'200','data'=>$data);
    }




}