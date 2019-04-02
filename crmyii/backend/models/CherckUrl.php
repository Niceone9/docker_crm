<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "cherck_url".
 *
 * @property string $url
 * @property string $ip
 * @property integer $is_weigui
 * @property integer $is_check
 * @property integer $id
 * @property integer $is_open
 * @property integer $userstype
 */
class CherckUrl extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cherck_url';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['url'], 'required'],
            [['is_check', 'is_open', 'userstype'], 'integer'],
            [['url', 'ip','is_weigui'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'url' => 'Url',
            'ip' => 'Ip',
            'is_weigui' => 'Is Weigui',
            'is_check' => 'Is Check',
            'id' => 'nextval(\'blog_id_seq\'::regclass)',
            'is_open' => 'Is Open',
            'userstype' => 'Userstype',
        ];
    }
}
