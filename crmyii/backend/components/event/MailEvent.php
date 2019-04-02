<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/12/22
 * Time: 9:15
 */
namespace backend\components\event;
use yii\base\Event;

class MailEvent extends Event
{

    public $email;
    public $subject;
    public $content;
}