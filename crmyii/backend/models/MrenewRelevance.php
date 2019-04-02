<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "jd_mrenew_relevance".
 *
 * @property integer $id
 * @property integer $dk_id
 * @property integer $xf_id
 */
class MrenewRelevance extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jd_mrenew_relevance';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dk_id', 'xf_id'], 'integer'],
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
            'xf_id' => 'Xf ID',
        ];
    }
}
