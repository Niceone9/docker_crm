<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "qudao_to_advertiser".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $advertiser_id
 */
class QudaoToAdvertiser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'qudao_to_advertiser';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'advertiser_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'advertiser_id' => 'Advertiser ID',
        ];
    }
}
