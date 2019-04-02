<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "jd_contract_relevance".
 *
 * @property integer $id
 * @property integer $advertiser
 * @property integer $contract_id
 * @property integer $product_line
 * @property string $money
 * @property string $fandian
 * @property string $xianshijine
 */
class ContractRelevance extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jd_contract_relevance';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['advertiser', 'contract_id', 'product_line'], 'integer'],
            [['money', 'fandian','xj_fandian'], 'number'],
            [['xj_fandian'],'default','value'=>0]
           // [['xianshijine'], 'string', 'max' => 255],
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
            'money' => 'Money',
            'fandian' => 'Fandian',
            'xianshijine' => 'Xianshijine',
        ];
    }

    public function getContract(){
        return $this->hasOne(JdContract::className(),['id'=>'contract_id']);
    }
}
