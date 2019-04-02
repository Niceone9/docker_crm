<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "auth_list_crm".
 *
 * @property string $route
 * @property string $role
 * @property integer $type
 * @property integer $id
 */
class AuthListCrm extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auth_list_crm';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'integer'],
            [['route', 'role'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'route' => '列表路由',
            'role' => '角色',
            'type' => '方式',
        ];
    }

    public function getType0(){
        if($this->type==1)
        {
            return '只看自己';
        }elseif($this->type==2)
        {
            return '看自己组';
        }elseif($this->type==3)
        {
            return '看全部';
        }
    }
}
