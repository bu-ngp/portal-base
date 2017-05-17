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
    'modules' => [],
    'components' => [
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'rules' => [
                //'base/<controller:\w+>/<action:\w+>' => 'base/<controller>/<action>',
                //'<controller:\w+>/<action:\w+>' => 'base/<controller>/<action>',
               // 'roles' => '<controller>/<action>',
             //    '<controller:roles>/<action:\w+>' => 'base/<controller>/<action>',
                '<controller:roles>' => 'base/<controller>',
              //  '<controller:roles>/<action:\w+>' => 'base/<controller>/<action>',
                'login' => 'site/login',
            ],
        ],
        'urlManagerFrontend' => [
            'class' => 'yii\web\urlManager',
            'baseUrl' => '../',
            'rules' => [
            ],
        ],
    ],
    'params' => $params,
];
