<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'helper'=>[
            'class'=>'common\components\Helper',
            'property'=>'123',
            'hjd'=>'侯建东',
        ],
        "authManager" => [
            "class" => 'yii\rbac\DbManager',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@runtime/cache2',
        ],
        'hjd'=>[
            'class'=>'common\components\Helper',
        ]
    ],

    // 配置语言
    'language'=>'zh-CN',
    // 配置时区
    'timeZone'=>'Asia/Chongqing',

];
