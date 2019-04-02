<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "audit_alias".
 *
 * @property integer $id
 * @property string $action_name
 * @property integer $audit_type
 * @property string $title
 */
class AuditAlias extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'audit_alias';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['audit_type'], 'integer'],
            [['action_name', 'title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'table' => 'Table',
            'audit_type' => 'Audit Type',
            'title' => 'Title',
        ];
    }
}
