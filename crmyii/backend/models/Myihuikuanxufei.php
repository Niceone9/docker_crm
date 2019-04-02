<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "jd_myihuikuanxufei".
 *
 * @property integer $time
 * @property integer $xf_id
 * @property integer $hk_id
 * @property double $money
 * @property integer $mt_fandian
 * @property integer $dl_fandian
 * @property integer $xf_fandian
 * @property integer $gr_fandian
 * @property integer $xs_fandian
 * @property double $shifu_money
 * @property double $market_tc
 * @property integer $id
 * @property integer $avid
 * @property integer $xsid
 * @property integer $mht_id
 */
class Myihuikuanxufei extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jd_myihuikuanxufei';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['time', 'xf_id', 'hk_id', 'mt_fandian', 'dl_fandian', 'xf_fandian', 'gr_fandian', 'xs_fandian', 'avid', 'xsid', 'mht_id'], 'integer'],
            [['money', 'shifu_money', 'market_tc'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'time' => 'Time',
            'xf_id' => 'Xf ID',
            'hk_id' => 'Hk ID',
            'money' => 'Money',
            'mt_fandian' => 'Mt Fandian',
            'dl_fandian' => 'Dl Fandian',
            'xf_fandian' => 'Xf Fandian',
            'gr_fandian' => 'Gr Fandian',
            'xs_fandian' => 'Xs Fandian',
            'shifu_money' => 'Shifu Money',
            'market_tc' => 'Market Tc',
            'id' => 'ID',
            'avid' => 'Avid',
            'xsid' => 'Xsid',
            'mht_id' => 'Mht ID',
        ];
    }
}
