<?php

namespace ngp\services\proccesses;

use doh\services\classes\ProcessLoader;
use ngp\services\forms\OfomsAttachListForm;
use ngp\services\forms\OfomsAttachRESTForm;
use ngp\services\repositories\OfomsRepository;
use PHPExcel;
use PHPExcel_IOFactory;

class OfomsAttachListProccessLoader extends ProcessLoader
{
    public $description = 'Прикрепление пациентов на портале ОФОМС';

    /** @var OfomsAttachListForm */
    private $form;

    public function __construct(OfomsAttachListForm $form, $config = [])
    {
        $this->form = $form;
        parent::__construct($config);
    }

    public function body()
    {
        $objPHPExcel = PHPExcel_IOFactory::load($this->form->listFile->tempName);
        $objPHPExcel2 = new PHPExcel();
        $sheet = $objPHPExcel->getActiveSheet();
        $sheet2 = $objPHPExcel2->getActiveSheet();
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        for ($row = 1; $row <= $highestRow; $row++) {
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            if (empty($rowData[0][0])) {
                break;
            }

            $form = new OfomsAttachRESTForm([
                'doctor' => $rowData[0][0],
                'policy' => $rowData[0][1],
                'fam' => $rowData[0][2],
                'im' => $rowData[0][3],
                'ot' => $rowData[0][4],
                'dr' => $rowData[0][5],
            ]);
            $repository = new OfomsRepository();
            $result = $repository->attach($form->ffio, $form->policy, $form->doctor);

            if ($result['status'] < 1) {
                file_put_contents('test.txt', print_r($result['message'], true), FILE_APPEND);
            }

            file_put_contents('test.txt', 'goood', FILE_APPEND);

            //      file_put_contents('test.txt', print_r($rowData, true), FILE_APPEND);
            $a = '';

        }


        //$this->addFile(sys_get_temp_dir() . '/report2.txt', 'Текстовый отчет2.txt');
        $this->addShortReport('Report with files Success Finished');
    }
}