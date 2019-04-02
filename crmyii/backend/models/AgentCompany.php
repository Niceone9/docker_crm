<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "jd_agent_company".
 *
 * @property integer $id
 * @property string $companyname
 * @property integer $ctime
 * @property string $title
 */
class AgentCompany extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jd_agent_company';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['companyname', 'ctime', 'title'], 'required'],
            [['ctime'], 'integer'],
            [['companyname'], 'string', 'max' => 100],
            [['title'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'companyname' => 'Companyname',
            'ctime' => 'Ctime',
            'title' => 'Title',
        ];
    }


    //列表
    public static function dl_list(){
        $list=self::find()->orderBy('id asc')->all();

        return $list;

        //  return self::find()->With("fu")->asArray()->all();
    }
    //添加
    public static function add_dl(){
        $model=new AgentCompany();
        $model->load(Yii::$app->getRequest()->getBodyParams(),'');

        $model->ctime=time();

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
