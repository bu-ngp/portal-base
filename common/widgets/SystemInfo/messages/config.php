<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 10.01.2018
 * Time: 15:59
 */
return [
    'sourcePath' => __DIR__ . DIRECTORY_SEPARATOR . '..',
    'messagePath' => __DIR__,
    'languages' => ['ru'],
    'except' => [
        '.svn',
        '.git',
        '.gitignore',
        '.gitkeep',
        '.hgignore',
        '.hgkeep',
        '/messages',
    ],
];