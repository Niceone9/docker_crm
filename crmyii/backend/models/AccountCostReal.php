<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "jd_account_cost_real".
 *
 * @property integer $id
 * @property integer $account_id
 * @property string $date
 * @property string $cost
 * @property string $no_back_cost
 * @property integer $contract_id
 * @property string $a_user
 * @property string $istuikuan
 */
class AccountCostReal extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jd_account_cost_real';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['a_user'], 'required'],
            [['account_id', 'contract_id'], 'integer'],
            [['date'], 'safe'],
            [['cost', 'no_back_cost'], 'number'],
            [['a_user'], 'string', 'max' => 255],
            [['istuikuan'],'default','value'=>0],
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
            'date' => 'Date',
            'cost' => 'Cost',
            'no_back_cost' => 'No Back Cost',
            'contract_id' => 'Contract ID',
            'a_user' => 'A User',
        ];
    }

    public function getContractgl(){
        return $this->hasOne(ContractRelevance::className(),['contract_id'=>'contract_id']);
    }

    public function getContractprlin(){
        return $this->hasOne(ProductLine::className(),['id'=>'product_line'])->select('id,name')
            ->viaTable(ContractRelevance::tableName(),['contract_id'=>'contract_id']);
    }
    //关联数据
    public function getAdvertiser0(){
        return $this->hasOne(JdCustomer::className(),['id'=>'advertiser'])->select('id,advertiser')
            ->viaTable(JdContract::tableName(),['id'=>'contract_id']);
    }
    //关联数据
    public function getMarket(){
        return $this->hasOne(UserBackend::className(),['id'=>'market'])->select('id,name')
            ->viaTable(JdContract::tableName(),['id'=>'contract_id']);
    }

}
