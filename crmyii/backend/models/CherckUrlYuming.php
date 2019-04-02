<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "cherck_url_yuming".
 *
 * @property string $url
 * @property integer $id
 * @property integer $account
 * @property string $account_name
 */
class CherckUrlYuming extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cherck_url_yuming';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['url'], 'required'],
            [['account'], 'integer'],
            [['url', 'account_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'url' => 'Url',
            'id' => 'nextval(\'blog_id_seq\'::regclass)',
            'account' => 'Account',
            'account_name' => 'Account Name',
        ];
    }
}
