<?php

namespace backend\models;

use Yii;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "contract_advertiser".
 *
 * @property integer $id
 * @property string $advertiser
 * @property integer $customer_id
 * @property string $advertiser_for_short
 * @property integer $ctime
 * @property string $linkman
 * @property string $tel
 * @property string $url
 */
class CustomerAdvertiser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer_advertiser';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_id', 'ctime'], 'integer'],
            [['advertiser', 'advertiser_for_short', 'linkman', 'tel', 'url','tax_identification','ticket_address','open_account','account','kp_tel'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'advertiser' => 'Advertiser',
            'customer_id' => 'Customer ID',

            'advertiser_for_short' => 'Advertiser For Short',
            'linkman' => 'Linkman',
            'tel' => 'Tel',
            'url' => 'Url',
            'ctime' => 'Ctime',
        ];
    }

    public function getProduct0(){
        return $this->hasMany(CustomerAdvertiserReleveance::className(),['advertiser'=>'id']);
    }

    public static function ad_list($customer_id){

        $list=self::find()->where(['customer_id'=>$customer_id])->with('product0')->asArray()->all();
        return $list;
    }

    public static function addru(){

        $model=new CustomerAdvertiser();
        $postdata = \Yii::$app->request->post();
        $model->load(Yii::$app->getRequest()->getBodyParams(),'');

        $model->ctime=time();
        //添加产品
        if($model->save())
        {
            /*
            //添加产品
                $product_advertiser=new CustomerAdvertiserReleveance();
                $field=['advertiser','product','product_short_name'];
                foreach($postdata['product'] as $key=>$val)
                {
                    $data[$key]=[$model->id,$postdata['product'][$key],$postdata['product_short_name'][$key]];
                }
                $tabname=$product_advertiser::tableName();
                Yii::$app->db->createCommand()->batchInsert($tabname,$field,$data)->execute();
            */

            return $model;

        }else
        {
            return array("code"=>'500',"msg"=>"提交失败","error"=>$model->errors);

        }

    }

    public static function upadvertiser($id){

        $model= CustomerAdvertiser::findOne($id);
        $postdata = \Yii::$app->request->post();
        $model->load(Yii::$app->getRequest()->getBodyParams(),'');


        //修改产品
        if($model->save())
        {

            return $model;

        }else
        {
            var_dump($model->errors);exit;
            throw new NotFoundHttpException('修改失败');
        }

    }




}
