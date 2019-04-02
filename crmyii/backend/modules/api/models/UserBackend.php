<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/10/9
 * Time: 17:19
 */

namespace backend\modules\api\models;
use common\models\Test;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use yii\web\Link;
use yii\web\Linkable;

class UserBackend extends ActiveRecord implements Linkable
{
    public static  function tableName()
    {
        return 'user_backend';
    }


    // 过滤掉一些字段，适用于你希望继承
// 父类实现同时你想屏蔽掉一些敏感字段
    public function fields()
    {
        $fields = parent::fields();

        // 删除一些包含敏感信息的字段
        unset($fields['auth_key'], $fields['password_hash'], $fields['password_reset_token']);

        return $fields;
    }
    /*
    public function fields(){
        return [
            'id',
            'email'=>'phone',
            'namex'=>function($model)
            {
                return $model->name.$model->username;
            }


        ];
    }
    */
   public function getTest(){
       return $this->hasOne(Test::className(),['id'=>'id']);
   }

    public function extraFields()
    {
        return ['test'];
    }

    public function getLinks(){
       return[
           Link::REL_SELF=>Url::to(['user/view','id'=>$this->id],true),
           'edit' => Url::to(['user/view', 'id' => $this->id], true),
           'profile' => Url::to(['user/profile/view', 'id' => $this->id], true),
           'index' => Url::to(['user/index'], true),
       ];
    }
}