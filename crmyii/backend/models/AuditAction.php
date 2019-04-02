<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "audit_action".
 *
 * @property integer $id
 * @property string $action_name
 * @property integer $type
 * @property string $note
 * @property integer $audit_count
 */
class AuditAction extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'audit_action';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

            [['audit_count', 'type'], 'integer'],
            [['action_name', 'note'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'action_name' => 'Action Name',
            'type' => 'Type',
            'note' => 'Note',
            'audit_count' => 'Audit Count',
        ];
    }
}
