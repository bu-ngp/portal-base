<?php

use yii\i18n\MissingTranslationEvent;
use yii\queue\ErrorEvent;

return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'layoutPath' => dirname(dirname(__DIR__)) . '/common/views/layouts',
    'language' => 'ru-RU',
    'sourceLanguage' => 'en-US',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'bootstrap' => [
        'assetsAutoCompress',
        'log',
        'queue',
        'queueExecutor',
        'domain\bootstrap\BaseDomainBootstrap',
        'common\bootstrap\Documenter',
        'common\bootstrap\Breadcrumbs',
        'doh',
    ],
    'components' => [
        'assetManager' => [
            'appendTimestamp' => true,
            'linkAssets' => true,
        ],
        'cache' => [
            'class' => 'yii\caching\MemCache',
        ],
        'assetsAutoCompress' =>
            [
                'class' => '\skeeks\yii2\assetsAuto\AssetsAutoCompressComponent',
                'enabled' => false,

                'readFileTimeout' => 3,           //Time in seconds for reading each asset file

                'jsCompress' => true,        //Enable minification js in html code
                'jsCompressFlaggedComments' => true,        //Cut comments during processing js

                'cssCompress' => true,        //Enable minification css in html code

                'cssFileCompile' => true,        //Turning association css files
                'cssFileRemouteCompile' => false,       //Trying to get css files to which the specified path as the remote file, skchat him to her.
                'cssFileCompress' => true,        //Enable compression and processing before being stored in the css file
                'cssFileBottom' => false,       //Moving down the page css files
                'cssFileBottomLoadOnJs' => false,       //Transfer css file down the page and uploading them using js

                'jsFileCompile' => true,        //Turning association js files
                'jsFileRemouteCompile' => false,       //Trying to get a js files to which the specified path as the remote file, skchat him to her.
                'jsFileCompress' => true,        //Enable compression and processing js before saving a file
                'jsFileCompressFlaggedComments' => true,        //Cut comments during processing js

                'htmlCompress' => true,        //Enable compression html
                'htmlCompressOptions' =>              //options for compressing output result
                    [
                        'extra' => true,        //use more compact algorithm
                        'no-comments' => true   //cut all the html comments
                    ],
            ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'/*, 'trace', 'profile'*/],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManagerAdmin' => [
            'class' => 'yii\web\urlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'login' => 'site/login',
                // 'doh/listen' => 'doh/default/listen',
            ],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'request' => [
            'csrfParam' => '_csrf-wk-portal',
            //'enableCsrfValidation' => false,
        ],
        'user' => [
            'class' => 'domain\auth\WKUser',
            'identityClass' => 'domain\models\base\Person',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-wk-portal', 'httpOnly' => true],
        ],
        'session' => [
            'class' => 'yii\web\DbSession',
            // this is the name of the session cookie used for login
            'name' => 'wk-portal',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
        ],
        'i18n' => [
            'translations' => [
                'common*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                    'sourceLanguage' => 'en-US',
                ],
                'domain/*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                    'sourceLanguage' => 'en-US',
                ],
                'classes/*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                    'sourceLanguage' => 'en-US',
                ],
                'noty' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@vendor/loveorigami/yii2-notification-wrapper/src/messages',
                    'sourceLanguage' => 'en-US',
                ],
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                    'sourceLanguage' => 'en-US',
                ],
            ],
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'dateFormat' => 'dd.MM.yyyy',
            'timeFormat' => 'HH:mm:ss',
            'datetimeFormat' => 'dd.MM.yyyy HH:mm:ss',
            'nullDisplay' => '',
        ],
        'queue' => [
            'class' => \yii\queue\db\Queue::className(),
            'commandClass' => \console\classes\queue\Command::className(),
            'mutex' => \yii\mutex\MysqlMutex::className(),
        ],
        'queueExecutor' => [
            'class' => \yii\queue\db\Queue::className(),
            'mutex' => \yii\mutex\MysqlMutex::className(),
        ],
        'config' => [
            'class' => \domain\services\ConfigComponent::className(),
        ],
    ],
    'modules' => [
        'noty' => [
            'class' => 'lo\modules\noty\Module',
        ],
        'cardlist' => [
            'class' => '\common\widgets\CardList\Module',
        ],
        'gridview' => [
            'class' => '\kartik\grid\Module'
            // enter optional module parameters below - only if you need to
            // use your own export download action or custom translation
            // message source
            // 'downloadAction' => 'gridview/export/download',
            // 'i18n' => []
        ],
        'report-loader' => [
            'class' => '\common\widgets\ReportLoader\Module',

        ],
        'markdown' => [
            'class' => 'kartik\markdown\Module',
        ],
        'doh' => [
            'class' => 'doh\Module',
        ],
    ],
];
