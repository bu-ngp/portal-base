<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 04.11.2017
 * Time: 13:53
 */

namespace doh\services;


use doh\services\classes\ProcessLoader;

class TestWithFiles extends ProcessLoader
{
    public $description = 'Тестовый процесс с файлами';

    public function body()
    {
        for ($i = 0; $i < 10; $i++) {
            $this->addPercentComplete(10);
           // sleep(2);
        }

        file_put_contents(sys_get_temp_dir() . '/report.txt', 'This is report');
        $this->addFile(sys_get_temp_dir() . '/report.txt', 'Текстовый отчет.txt');

        file_put_contents(sys_get_temp_dir() . '/report2.txt', 'This is report 2');
        $this->addFile(sys_get_temp_dir() . '/report2.txt', 'Текстовый отчет2.txt');

        $this->addShortReport('Report with files Success Finished');
    }
}