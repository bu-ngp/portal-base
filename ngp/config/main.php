<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-ngp',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'ngp\controllers',
    //'layoutPath' => '@app/views/layouts',
    'components' => [
        'urlManager' => [
            'class' => 'yii\web\urlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
        'urlManagerAdmin' => [
            'baseUrl' => 'manager',
        ],
    ],
    'params' => $params,
];
