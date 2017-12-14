<?php
$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '',
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=db_name',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'tablePrefix' => '',
            'schemaMap' => [
                'mysqli' => 'console\classes\mysql\Schema', // MySQL
                'mysql' => 'console\classes\mysql\Schema', // MySQL
            ],
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
    ],
];

if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['*'],
        'generators' => [ // здесь
            'model' => [ // название генератора
                'class' => 'common\gii\model\Generator', // класс генератора
                'templates' => [ // настройки сторонних шаблонов
                    'wkModel' => '@common/gii/model/default', // имя_шаблона => путь_к_шаблону
                ]
            ],
            'crud' => [ // название генератора
                'class' => 'yii\gii\generators\crud\Generator', // класс генератора
                'templates' => [ // настройки сторонних шаблонов
                    'wkCrud' => '@common/gii/crud/default', // имя_шаблона => путь_к_шаблону
                ]
            ]
        ],
    ];
}

return $config;