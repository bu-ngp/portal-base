<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'frontend\controllers',
    //'layoutPath' => '@app/views/layouts',
    'components' => [
        'urlManager' => function() {
            return Yii::$app->get('urlManagerFrontend');
        },
        'urlManagerAdmin' => [
            'baseUrl' => 'manager',
        ],
    ],
    'params' => $params,
];
