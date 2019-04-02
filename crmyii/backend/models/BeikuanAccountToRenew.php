<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "beikuan_account_to_renew".
 *
 * @property integer $id
 * @property integer $bk_account_id
 * @property integer $xf_id
 */
class BeikuanAccountToRenew extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'beikuan_account_to_renew';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['bk_account_id', 'xf_id'], 'integer'],
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
            'xf_id' => 'Xf ID',
        ];
    }
}
