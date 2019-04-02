<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "contract_relevance_new".
 *
 * @property integer $id
 * @property integer $advertiser
 * @property integer $contract_id
 * @property integer $product_line
 * @property string $fandian
 */
class ContractRelevanceNew extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'contract_relevance_new';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['advertiser', 'contract_id', 'product_line'], 'integer'],
            [['fandian'], 'number'],
            [['contract_id'], 'unique'],
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
            'contract_id' => 'Contract ID',
            'product_line' => 'Product Line',
            'fandian' => 'Fandian',
        ];
    }

    public function getProductline(){
        return $this->hasOne(ProductLine::className(),['id'=>'product_line']);
    }


}
