<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "qudao_demand".
 *
 * @property integer $id
 * @property integer $account
 * @property string $link
 * @property integer $domain
 * @property integer $status
 * @property string $zip_link
 * @property integer $submitusers
 * @property integer $ctime
 * @property string $last_time
 * @property string $note
 */
class QudaoDemand extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'qudao_demand';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['account', 'domain', 'status', 'submitusers', 'ctime'], 'integer'],
            [['link', 'note'], 'string'],
            [['last_time'], 'safe'],
            [['zip_link','qiniu_key'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'account' => 'Account',
            'link' => 'Link',
            'domain' => 'Domain',
            'status' => 'Status',
            'zip_link' => 'Zip Link',
            'submitusers' => 'Submitusers',
            'ctime' => 'Ctime',
            'last_time' => 'Last Time',
            'note' => 'Note',
        ];
    }
}
