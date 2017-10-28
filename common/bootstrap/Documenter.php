<?php

namespace common\bootstrap;

use Yii;
use yii\base\BootstrapInterface;

/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 28.10.2017
 * Time: 8:55
 */
class Documenter implements BootstrapInterface
{
    /**
     * @param \yii\base\Application $app
     */
    public function bootstrap($app)
    {
        $container = Yii::$container;

        $container->set('common\widgets\Documenter\Documenter', [
            'directories' => [
                '@common/updates_doc',
            ],
        ]);
    }
}