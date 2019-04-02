<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "customer_advertiser_releveance".
 *
 * @property integer $id
 * @property integer $advertiser
 * @property string $product
 * @property string $product_short_name
 */
class CustomerAdvertiserReleveance extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer_advertiser_releveance';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['advertiser'], 'integer'],
            [['product', 'product_short_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'advertiser' => 'Advertiser',
            'product' => 'Product',
            'product_short_name' => 'Product Short Name',
        ];
    }
}
