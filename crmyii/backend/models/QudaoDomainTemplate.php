<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "qudao_domain_template".
 *
 * @property integer $id
 * @property string $domain
 * @property string $iframeurl
 * @property integer $type
 */
class QudaoDomainTemplate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'qudao_domain_template';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'integer'],
            [['domain', 'iframeurl'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'domain' => 'Domain',
            'iframeurl' => 'Iframeurl',
            'type' => 'Type',
        ];
    }
}
