<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "flow".
 *
 * @property integer $id
 * @property string $fu_flow
 * @property string $zi_flow
 * @property string $fu_audit
 * @property integer $is_true
 */
class Flow extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'flow';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['is_true'], 'integer'],
            [['fu_flow', 'zi_flow', 'fu_audit'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fu_flow' => 'Fu Flow',
            'zi_flow' => 'Zi Flow',
            'fu_audit' => 'Fu Audit',
            'is_true' => 'Is True',
        ];
    }

    //关联数据
    public function getZi(){
        return $this->hasMany(FlowGl::className(),['type'=>'id']);
    }
}
