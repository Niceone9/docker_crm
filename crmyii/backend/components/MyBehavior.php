<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/9/29
 * Time: 11:29
 */
namespace backend\components;

use Yii;
use yii\base\ActionFilter;
use yii\base\Behavior;

class MyBehavior extends Behavior
{

    public $prop1;
    private $_prop2;
    public function beforeAction($action)
    {

       // echo $action->getUniqueId();
        var_dump(12321321);
        $this->prop1='hhh';
        exit;
        return true;
    }

    public function isGuest(){
        return 2;
    }

    public function setProp($value)
    {
        $this->_prop2=$value;
    }
    public function getProp(){
        return $this->_prop2;
    }
}