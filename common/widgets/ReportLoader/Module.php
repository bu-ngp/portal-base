<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 05.06.2017
 * Time: 9:25
 */

namespace common\widgets\ReportLoader;


class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->id = 'reportLoader';
        parent::init();
    }
}