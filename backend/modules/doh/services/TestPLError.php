<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 04.11.2017
 * Time: 13:53
 */

namespace doh\services;


use doh\services\classes\ProcessLoader;

class TestPLError extends ProcessLoader
{
    public $description = 'Тестовый процесс';

    public function body()
    {
        $stepError = rand(3, 9);
        for ($i = 0; $i < 10; $i++) {
            if ($i === $stepError) {
                throw new \Exception('Произошла ошибка на шаге ' . $i);
            }
            $this->addPercentComplete(10);
            sleep(2);
        }
    }
}