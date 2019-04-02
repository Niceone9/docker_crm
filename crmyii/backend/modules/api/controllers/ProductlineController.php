<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/10/20
 * Time: 16:12
 */

namespace backend\modules\api\controllers;


use backend\models\ContractRelevance;
use backend\models\JdContract;
use backend\models\JdCustomer;
use backend\models\ProductLine;
use yii\rest\ActiveController;

class ProductlineController extends ActiveController
{
    public $modelClass='backend\models\ProductLine';
    public function actions()
    {
        $actions = parent::actions();

        // 禁用"delete" 和 "create" 动作
        unset($actions['delete'], $actions['create'], $actions['index']);

        // 使用"prepareDataProvider()"方法自定义数据provider
        //  $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];

        return $actions;
    }

    public function actionIndex(){
        $gsid=\Yii::$app->request->get('customerid');
        if($gsid!='') {
            //获取公司是公司还是媒体
            $khu=JdCustomer::findOne($gsid);
            if($khu->customer_type==3){

                $pridin = JdContract::find()->select('product_line')->where(['advertiser' => $gsid])->groupBy('product_line')->column();
            }else{
                $pridin = ContractRelevance::find()->select('product_line')->where(['advertiser' => $gsid])->groupBy('product_line')->column();
            }

            $listtop = ProductLine::find()->select('parent_id')->where(['id' => $pridin])->groupBy('parent_id')->column();


        }

        $list=ProductLine::find()->select('id,name,title')->where(['parent_id'=>0])->andFilterWhere(['id'=>$listtop])->asArray()->orderBy('id desc')->asArray()->all();
        foreach ($list as $key=>$val)
        {

            $list[$key]['zi']=ProductLine::find()->select('id,name,title')->where(['parent_id'=>$val['id']])->andFilterWhere(['id'=>$pridin])->asArray()->all();

        }



        return $list;
    }


    public function actionComerline($id)
    {
        return ProductLine::customer_prlin($id);
    }

    //根据产品线获取媒介合同列表
    public function actionProductmcontract($prid){
        return JdContract::find()
            ->select('id,title')
            ->where(['product_line'=>$prid,'iszuofei'=>0,'is_meijie'=>1,'is_ultimate_shenhe'=>'1'])
            ->asArray()
            ->all();

    }

}