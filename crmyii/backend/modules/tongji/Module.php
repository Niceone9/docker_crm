<?php

namespace backend\modules\tongji;

/**
 * tongji module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'backend\modules\tongji\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        // echo \Yii::$app->access->$a();exit;
        \Yii::$app->user->enableSession = false;

        // custom initialization code goes here
    }
}
