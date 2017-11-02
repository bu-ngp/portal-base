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
class Breadcrumbs implements BootstrapInterface
{
    /**
     * @param \yii\base\Application $app
     */
    public function bootstrap($app)
    {
        $container = Yii::$container;

        $container->set('common\widgets\Breadcrumbs\Breadcrumbs', [
            'id' => 'wkbc_breadcrumb',
        ]);
    }
}