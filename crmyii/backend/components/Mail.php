<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/12/21
 * Time: 18:55
 */

namespace backend\components;


class Mail
{
    public static function sendMail($event){
        $mail=\Yii::$app->mailer->compose();
        $mail->setTo($event->email);
        $mail->setSubject($event->subject);
        $mail->setTextBody($event->content);
        return $mail->send();
    }

}