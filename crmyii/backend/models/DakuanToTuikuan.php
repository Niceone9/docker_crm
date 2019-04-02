<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "dakuan_to_tuikuan".
 *
 * @property integer $id
 * @property integer $dk_id
 * @property integer $tk_id
 * @property string $fk_money
 */
class DakuanToTuikuan extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dakuan_to_tuikuan';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dk_id', 'tk_id','type'], 'integer'],
            [['fk_money'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dk_id' => 'Dk ID',
            'tk_id' => 'Tk ID',
            'fk_money' => 'Fk Money',
        ];
    }
}
