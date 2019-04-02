<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "department".
 *
 * @property integer $id
 * @property string $name
 * @property integer $parent_id
 */
class ArticleClass extends \yii\db\ActiveRecord
{

    public static $tree=[];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article_class';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['parent_id'], 'integer'],
            [['name','basic_role'], 'string', 'max' => 50],
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
            'parent_id' => 'Parent ID',
        ];
    }


    public static function bumen_list(){

        $list=self::find()->orderBy('id asc')->asArray()->all();
        return $list;
        /*
       $a= self::bumen_tree(0,1);
       echo "<pre>";
        var_dump($a);exit;
        */
    }

    public static function bumenuserslist($id){

        return Article::find()->where(['fuid'=>$id,'is_delete'=>0])->asArray()->all();
    }


    //添加
    public static function add_ru(){
        $model=new ArticleClass();
        $model->load(Yii::$app->getRequest()->getBodyParams(),'');

        if($model->save())
        {
            return $model;
        }else
        {
            return false;
        }

    }

    //删除部门


}
