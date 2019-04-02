<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "jd_piaotype".
 *
 * @property integer $id
 * @property string $name
 * @property integer $advertiser
 */
class Piaotype extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jd_piaotype';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'advertiser'], 'required'],
            [['advertiser'], 'integer'],
            [['name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'advertiser' => 'Advertiser',
        ];
    }

    public function getDaili(){
        return $this->hasOne(AgentCompany::className(),['id'=>'advertiser'])->select('id,companyname');
    }
    //列表
    public static function dl_list(){
        $list=self::find()->orderBy('id asc')->joinWith('daili daili')->asArray()->all();

        return $list;

        //  return self::find()->With("fu")->asArray()->all();
    }
    //添加
    public static function add_dl(){
        $model=new Piaotype();
        $model->load(Yii::$app->getRequest()->getBodyParams(),'');


        if($model->save())
        {
            return $model;
        }else
        {
            return array('code'=>500,'msg'=>'添加失败','data'=>$model->errors);
        }

    }


    public static function dl_info($id){
        //具体数据
        $data1=self::find()->where(['id'=>$id])->asArray()->one();

        return $data1;
    }

}
