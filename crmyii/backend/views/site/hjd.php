<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/9/29
 * Time: 20:19
 */

use yii\jui\DatePicker;

echo DatePicker::widget([
    'language' => 'zh-CN',
    'name'  => 'country',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
    ],
]);