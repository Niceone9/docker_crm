<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "jd_contact_list".
 *
 * @property integer $id
 * @property string $name
 * @property string $qq
 * @property string $weixin
 * @property string $email
 * @property string $position
 * @property string $tel
 * @property integer $customer_id
 */
class JdContactList extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jd_contact_list';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //[['name', 'qq', 'weixin', 'email', 'position', 'tel', 'customer_id'], 'required'],
            [['customer_id'], 'integer'],
            [['name', 'weixin'], 'string', 'max' => 50],
            [['qq', 'position'], 'string', 'max' => 20],
            [['email'], 'string', 'max' => 60],
            [['tel'], 'string', 'max' => 200],
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
            'qq' => 'Qq',
            'weixin' => 'Weixin',
            'email' => 'Email',
            'position' => 'Position',
            'tel' => 'Tel',
            'customer_id' => 'Customer ID',
        ];
    }

    //根据客户返回客户所有联系人
    public static function custmober_contact($id){
        return self::find()->where(['customer_id'=>$id])->asArray()->all();
    }
}
