<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017/7/5
 * Time: 15:21
 */

namespace backend\modules\tongji\controllers;


use backend\models\JdCustomer;
use yii\rest\ActiveController;

use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\web\Response;
use yii\web\NotFoundHttpException;


class CustomerController extends ActiveController
{
    public $modelClass='api\models\Customer';
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


    //新增客户排名
    public static function actionCustomer_add_rank($start,$end){
        $data=Customer::customer_add_rank($start,$end);
        $return_data['code']=200;
        $return_data['data']=$data;
        return $return_data;

    }

    //新增合同排名
    public static function actionContract_add_rank($marketid,$start,$end){
        $data=JdCustomer::contract_add_rank($marketid,$start,$end);
        $return_data['code']=200;
        $return_data['data']=$data;
        return $return_data;

    }


    //新增合同列表
    public static function actionContract_add_list($start,$end){
        $data=JdCustomer::contract_add_list($start,$end);
        $return_data['code']=200;
        $return_data['data']=$data;
        return $return_data;

    }

    //新增合同列表详情
    public function  actionContart_add_markey_list($id,$start,$end){
        $data=JdCustomer::contract_market_add_list($id,$start,$end);
        $return_data['code']=200;
        $return_data['data']=$data;
        return $return_data;
    }


    //销售毛利润排名
    public function actionMarket_maoli_rank($marketid,$start,$end){
        if($start!='default' && $end!='default') {
            $start = strtotime($start);
            $end = strtotime($end ." +1 day");
        }
        $data=JdCustomer::markey_maoli_rank($marketid,$start,$end);

        $return_data['code']=200;
        $return_data['data']=$data;
        return $return_data;
    }
    //销售毛利润列表
    public function actionMarket_maoli_list($id,$start,$end){
        if($start!='default' && $end!='default') {
            $start = strtotime($start);
            $end = strtotime($end." +1 day");
        }
        $data=JdCustomer::markey_maoli_list($id,$start,$end);

        $return_data['code']=200;
        $return_data['data']=$data;
        return $return_data;
    }


    //优化师消耗排名
  /*
   *   public function actionSemrank($start,$end){
        //Init curl
        $curl = new curl\Curl();

        //get http://example.com/
        $response = $curl->get('sem.yushanapp.com/sem/zuanduserjd?userid=3132');
        $json=json_decode($response,true);
        $sem_group=$json[data]['zu'];
        foreach ($sem_group as $key =>$val)
        {
            foreach ($val as $k=>$v)
            {
                $sem_group[$key][$k]['xiaohao']=AccountConsumption::semname_xiaohao($v['name'],$start,$end);
            }
        }

        $arr=array();
        foreach($sem_group as $key=> $val)
        {

            $a=$sem_group[$key];


            usort($a,function($a,$b){
                if($a['xiaohao']==$b['xiaohao'])
                {
                    return 0;
                }
                return $a['xiaohao']>$b['xiaohao']?-1:1;
            });

            $arr[$key]=$a;

        }

        $return_data['code']=200;
        $return_data['data']=$arr;
        return $return_data;
       // throw new NotFoundHttpException('hello word');
    }
   * */

    //sem 小组消耗
    public function actionSem_group_cost($start,$end,$group_id)
    {  
        $lisy=self::actionSemrank($start,$end);
        $group_one=$lisy[data][$group_id];
        $money=0;
        foreach ($group_one as $value)
        {
            $money+=$value['xiaohao'];
        }
        $return_data['code']=200;
        $return_data['data']=$money;
        return $return_data;

    }

    //sem 优化师 各账户消耗
    public function actionSem_cost($name,$start,$end){
        $data=AccountConsumption::semname_xiaohao_($name,$start,$end);
        $return_data['code']=200;
        $return_data['data']=$data;
        return $return_data;
    }
    //公司列表
    public function actionCustomerlist(){
        $data['data']=JdCustomer::find()->select(['id','advertiser'])->where(['customer_type'=>[1,2]])->all();
        $data['code']=200;
        return $data;
     //   $list=
    }

    //获取公司的产品线
    public function actionCustomer_prlin($id){
        $data['data']=JdCustomer::customer_prlin($id);
        $data['code']=200;
        return $data;
    }
    //获取产品线
    public function actionProduct_linlist(){
        $data['data']=ProductLine::find()->all();
        $data['code']=200;
        return $data;
    }

}