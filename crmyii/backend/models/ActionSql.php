<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "action_sql".
 *
 * @property string $action
 * @property string $sql
 * @property string $note
 */
class ActionSql extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'action_sql';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['action'], 'required'],
            [['sql'], 'string'],
            [['action', 'note'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'action' => 'Action',
            'sql' => 'Sql',
            'note' => 'Note',
        ];
    }
}
