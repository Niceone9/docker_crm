<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "flow_gl".
 *
 * @property integer $id
 * @property string $type
 * @property integer $fuid
 * @property integer $ziid
 * @property integer $ctime
 * @property string $last_time
 */
class FlowGl extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'flow_gl';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fuid', 'ziid', 'ctime','type'], 'integer'],
            [['last_time'], 'safe'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'fuid' => 'Fuid',
            'ziid' => 'Ziid',
            'ctime' => 'Ctime',
            'last_time' => 'Last Time',
        ];
    }
}
