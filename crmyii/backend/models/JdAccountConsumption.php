<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "jd_account_consumption".
 *
 * @property string $appid
 * @property string $starttime
 * @property string $endtime
 * @property string $baidu_cost_total
 * @property integer $id
 * @property integer $semid
 * @property integer $xsid
 * @property string $date
 * @property integer $htid
 * @property double $zhanxian
 * @property double $dianji
 * @property integer $avid
 * @property string $xf_fandian
 * @property string $mt_fandian
 * @property integer $account_id
 * @property string $no_back_cost
 */
class JdAccountConsumption extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jd_account_consumption';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['baidu_cost_total', 'zhanxian', 'dianji', 'xf_fandian', 'mt_fandian', 'no_back_cost'], 'number'],
            [['semid', 'xsid', 'htid', 'avid', 'account_id'], 'integer'],
            [['appid'], 'string', 'max' => 50],
            [['starttime', 'endtime'], 'string', 'max' => 11],
            [['date'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'appid' => 'Appid',
            'starttime' => 'Starttime',
            'endtime' => 'Endtime',
            'baidu_cost_total' => 'Baidu Cost Total',
            'id' => 'ID',
            'semid' => 'Semid',
            'xsid' => 'Xsid',
            'date' => 'Date',
            'htid' => 'Htid',
            'zhanxian' => 'Zhanxian',
            'dianji' => 'Dianji',
            'avid' => 'Avid',
            'xf_fandian' => 'Xf Fandian',
            'mt_fandian' => 'Mt Fandian',
            'account_id' => 'Account ID',
            'no_back_cost' => 'No Back Cost',
        ];
    }
}
