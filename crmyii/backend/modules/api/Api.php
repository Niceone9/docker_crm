<?php

namespace backend\modules\api;

/**
 * api module definition class
 */
class Api extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'backend\modules\api\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {

        header("Origin:*");
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Request-Headers:*");
        header("Access-Control-Allow-Credentials:true");
        header("Access-Control-Allow-Headers: Content-Type, X-Requested-With, Cache-Control,Authorization");


        parent::init();
       // echo \Yii::$app->access->$a();exit;
        \Yii::$app->user->enableSession = false;

        // custom initialization code goes here
    }
}
