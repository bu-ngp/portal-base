<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 04.11.2017
 * Time: 14:03
 */

namespace doh\services\classes;


use Yii;

class DoH
{
    /**
     * @var ProcessLoader
     */
    private $_loader;

    public function __construct(ProcessLoader $loader)
    {
        $this->_loader = $loader;
    }

    public function execute()
    {
        Yii::$app->queue->push($this->_loader);
    }
}