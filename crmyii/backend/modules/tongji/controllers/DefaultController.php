<?php

namespace api\modules\crm\controllers;

use yii\web\Controller;

/**
 * Default controller for the `crm` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        echo 111;exit;
        return $this->render('index');
    }
}
