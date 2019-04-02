<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "beikuan_account_to_renew_account".
 *
 * @property integer $id
 * @property integer $bk_account_id
 * @property string $account
 */
class BeikuanAccountToRenewAccount extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'beikuan_account_to_renew_account';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['bk_account_id'], 'integer'],
            [['account'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bk_account_id' => 'Bk Account ID',
            'account' => 'Account',
        ];
    }
}
