<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "jd_account_users".
 *
 * @property integer $id
 * @property integer $account_id
 * @property integer $u_id
 */
class AccountUsers extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jd_account_users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['account_id', 'u_id'], 'required'],
            [['account_id', 'u_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'account_id' => 'Account ID',
            'u_id' => 'U ID',
        ];
    }
}
