<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 04.11.2017
 * Time: 13:53
 */

namespace doh\services;


use doh\services\classes\ProcessLoader;

class TestPL extends ProcessLoader
{
    public $description = 'Тестовый процесс';

    public function body()
    {
        for ($i = 0; $i < 10; $i++) {
            $this->addPercentComplete(10);
            sleep(2);
        }
    }
}