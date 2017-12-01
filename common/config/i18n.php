<?php
/**
 * Configuration file for 'yii message/extract' command.
 *
 * This file is automatically generated by 'yii message/config' command.
 * It contains parameters for source code messages extraction.
 * You may modify this file to suit your needs.
 *
 * You can use 'yii message/config-template' command to create
 * template configuration file with detailed description for each parameter.
 */
return [
    'sourcePath' => '@common/..',
    'messagePath' => 'common/messages',
    'languages' => ['ru-RU'],
    'except' => [
        '/*/',
        '!backend/',
        'backend/modules/doh',
        '!common/',
        '!domain/',
        '.svn',
        '.git',
        '.gitignore',
        '.gitkeep',
        '.hgignore',
        '.hgkeep',
        '/vendor',
        'tests/',
        'web/',
        '/environments',
        '/requirements.php',
        'assets/',
        'migrations/',
        'messages/',
        'widgets/',
    ],
];
