<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "jd_renew_order".
 *
 * @property integer $id
 * @property integer $xf_id
 * @property string $url
 * @property string $xingzhi
 * @property string $guanggaozhu
 * @property string $murl
 */
class RenewOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jd_renew_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['xf_id'], 'integer'],
            [['url', 'xingzhi', 'guanggaozhu', 'murl'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'xf_id' => 'Xf ID',
            'url' => 'Url',
            'xingzhi' => 'Xingzhi',
            'guanggaozhu' => 'Guanggaozhu',
            'murl' => 'Murl',
        ];
    }
}
