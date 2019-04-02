<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);


return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'admin' => [
            'class' => 'mdm\admin\Module',
        ],
        'api'=>[
            'class'=>'backend\modules\api\Api'
        ],
        'tongji' => [
            'class' => 'backend\modules\tongji\Module',
        ],
        'redactor' => [
            'class' => 'yii\redactor\RedactorModule',
            'uploadDir' => './Upload',  // 比如这里可以填写 ./uploads
            'uploadUrl' => './Upload',
            'imageAllowExtensions'=>['jpg','png','gif']
        ],
    ],
    'aliases' => [
        '@mdm/admin' => '@vendor/mdmsoft/yii2-admin',
    ],
    'as myBehavior2' => \backend\components\MyBehavior::className(),
    'components' => [
        'response' => [
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                /*$response->data = [
                    'code' => $response->getStatusCode(),
                    'data' => $response->data,
                    'message' => $response->statusText
                ];*/
                //$response->format = yii\web\Response::FORMAT_JSON;
            },
        ],
        'request' => [
            'csrfParam' => '_csrf-backend',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'user' => [
            'identityClass' => 'backend\models\UserBackend',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
            'enableSession' => true,
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logVars' => ['_GET','_POST'],
                ],
            ],


        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'assetManager'=>[
            'bundles'=>[
                'dmstr\web\AdminLteAsset' => [
                    'skin' => 'skin-blue',
                ],
            ]
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'defaultRoles' => ['guest'],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => false,
            'showScriptName' => false,
            'rules' => require(__DIR__.'/rules.php'),
        ],
        'hjd'=>[
            'class'=>'common\components\Helper',
        ]
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
        /*
        'view' => [
            'theme' => [
                // 'basePath' => '@app/themes/spring',
                // 'baseUrl' => '@web/themes/spring',
                'pathMap' => [
                    '@app/views' => [
                        '@app/themes/christmas',
                        '@app/themes/spring',
                    ]
                ],
            ],
        ],*/
    ],
    'as hjd'=>[
        'class'=>'backend\components\MyBehavior',
    ],
    'as access' => [
        'class' => 'mdm\admin\components\AccessControl',
        'allowActions' => [
            //这里是允许访问的action，不受权限控制
            //controller/action
            //'*',
            'site/*',
            'site/login',
            'site/logout',
            'blog/index',
            'category/*',
            'gii/*',
            'test/*',
            'api/*',
            'tongji/*'

        ]
    ],
    'as theme' => [
        'class' => 'backend\components\ThemeControl',
    ],
    'params' => $params,
];
