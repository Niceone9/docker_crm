<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "jd_product_line".
 *
 * @property integer $id
 * @property string $name
 * @property integer $audit_1
 * @property integer $audit_2
 * @property string $title
 * @property integer $parent_id
 * @property integer $yushan_type
 * @property integer $fk_type
 */
class ProductLine extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jd_product_line';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'title'], 'required'],
            [['audit_1', 'audit_2', 'parent_id', 'yushan_type', 'fk_type'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['title'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '产品线名称',
            'audit_1' => 'Audit 1',
            'audit_2' => 'Audit 2',
            'title' => '简称',
            'parent_id' => '父类',
            'yushan_type' => '羽扇 产品线id',
            'fk_type' => '付款TYPE',
        ];
    }

    public function getFu(){
        return $this->hasOne(ProductLine::tableName(),['id'=>'parent_id'])->select('id,name');
    }
    //查询公司下所属合同的所有产品线
    public static function customer_prlin($id){
        return self::find()->join("a left join","jd_contract_relevance b","a.id=b.product_line")->where(['b.advertiser'=>$id])->all();
    }

    //列表
    public static function pr_list(){
        $list=self::find()->select('id,name,title,yushan_type,fk_type')->where(['parent_id'=>0])->asArray()->orderBy('id asc')->all();
        foreach ($list as $key=>$val)
        {
            $list[$key]['zi']=self::find()->select('id,name,title,yushan_type,fk_type')->where(['parent_id'=>$val['id']])->all();
        }
        return $list;

      //  return self::find()->With("fu")->asArray()->all();
    }
    //添加
    public static function add_prlin(){
        $model=new ProductLine();
        $model->load(Yii::$app->getRequest()->getBodyParams(),'');
        $model->yushan_type=110901;

        if($model->save())
        {
            return $model;
        }else
        {
            return array('code'=>500,'msg'=>'添加失败','data'=>$model->errors);
        }

    }


    public static function pr_info($id){
        //具体数据
        $data1=self::find()->where(['id'=>$id])->With(['fu'])->asArray()->one();
        $data['code']='200';
        $data['data']=$data1;
        return $data;
    }
}
