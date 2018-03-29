<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\console\controllers\FixtureController',
            'namespace' => 'common\fixtures',
        ],
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'interactive' => false,
            'migrationPath' => [
                '@common/widgets/ReportLoader/migrations',
                '@console/migrations',
                '@common/widgets/CardList/migrations',
                '@backend/modules/doh/migrations',
            ],
            'migrationNamespaces' => [
                'yii\queue\db\migrations',
            ],
        ],
        'wkloader' => [
            'class' => 'common\widgets\ReportLoader\controllers\WkloaderController',
            'interactive' => false,
        ],
    ],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'errorHandler' => null,
        'user' => null,
        'session' => null,
        'request' => null,
    ],
    'params' => $params,
];
