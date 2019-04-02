<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "nianjia_log".
 *
 * @property integer $id
 * @property integer $uid
 * @property string $note
 */
class NianjiaLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'nianjia_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid'], 'integer'],
            [['note','time'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'note' => 'Note',
        ];
    }
}
