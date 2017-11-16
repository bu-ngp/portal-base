<?php

namespace ngp\services\proccesses;

use doh\services\classes\ProcessLoader;
use ngp\services\classes\ChunkReadFilter;
use ngp\services\forms\OfomsAttachListForm;
use ngp\services\forms\OfomsAttachRESTForm;
use ngp\services\repositories\OfomsRepository;
use PHPExcel;
use Yii;

class OfomsAttachListProccessLoader extends ProcessLoader
{
    const CHUNK_SIZE = 1000;
    const START_ROW = 1;

    public $description = 'Прикрепление пациентов на портале ОФОМС';

    /** @var OfomsAttachListForm */
    private $form;
    private $fileName;
    private $executing = true;
    /** @var PHPExcel */
    private $objPHPExcelReport;
    /** @var \PHPExcel_Worksheet */
    private $sheetReport;

    private $success = 0;
    private $error = 0;
    private $rows = 0;
    private $reportRow = 1;

    private $highestRow;

    public function __construct(OfomsAttachListForm $form, $config = [])
    {
        $this->form = $form;
        $this->fileName = $this->form->listFile->tempName;
        $this->objPHPExcelReport = new \PHPExcel();
        $this->sheetReport = $this->objPHPExcelReport->getActiveSheet();
        $this->addReportHeader();
        parent::__construct($config);
    }

    public function body()
    {
        /** @var \PHPExcel_Reader_Excel2007|\PHPExcel_Reader_Excel5 $objReader */
        $objReader = \PHPExcel_IOFactory::createReaderForFile($this->fileName);
        $this->highestRow = $this->getHighestRow();

        $chunkFilter = new ChunkReadFilter();
        $objReader->setReadFilter($chunkFilter);
        $objReader->setReadDataOnly(true);

        while ($this->executing) {
            $chunkFilter->setRows(self::START_ROW, self::CHUNK_SIZE);
            $objPHPExcel = $objReader->load($this->fileName);
            $objPHPExcel->setActiveSheetIndex(0);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            for ($i = self::START_ROW; $i < self::START_ROW + self::CHUNK_SIZE; $i++) {
                $row = $objWorksheet->rangeToArray('A' . $i . ':F' . $i, null, true, false);
                $row = $row[key($row)];

                if (empty($row[0])) {
                    $this->executing = false;
                    break;
                }

                if ($i % 50 === 0) {
                    $this->addPercentComplete(round($i * 99 / $this->highestRow));
                }

                $form = $this->getForm($row);

                if ($form->validate()) {
                    $repository = new OfomsRepository();
                    $result = $repository->attach($form->ffio, $form->policy, $form->doctor);

                    if ($result['status'] < 1) {
                        $this->addReportRow($i, $form, $result['message']);
                        $this->error++;
                    } else {
                        $this->success++;
                    }
                } else {
                    $errorsMessage = implode(',', array_map(function ($errors) {
                        return implode(',', $errors);
                    }, $form->getErrors()));
                    $this->addReportRow($i, $form, $errorsMessage);

                    $this->error++;
                };

                $this->rows++;
            }
        }

        if ($this->error) {
            /** @var \PHPExcel_Writer_CSV $objWriter */
            $objWriter = \PHPExcel_IOFactory::createWriter($this->objPHPExcelReport, 'CSV');
            $objWriter->setDelimiter(';');
            $reportPath = Yii::getAlias('@ngp/reports_attach-list/' . date('Y-m-d') . time() . '.csv');
            $objWriter->save($reportPath);
            file_put_contents($reportPath, mb_convert_encoding(file_get_contents($reportPath), 'windows-1251', 'UTF-8'));
            $this->addFile($reportPath, 'Результат прикрепления.csv');
        }

        $successPercent = round($this->success * 100 / $this->rows, 1);
        $errorPercent = round($this->error * 100 / $this->rows, 1);
        $this->addShortReport("Итоги обработки:\n- Всего записей: {$this->rows};\n- Успешно ($successPercent%): {$this->success};\n- Ошибок ($errorPercent%): {$this->error};");
//            file_put_contents('test.txt', 'goood', FILE_APPEND);
    }

    protected function addReportHeader()
    {
        $this->sheetReport->setCellValueByColumnAndRow(0, $this->reportRow, 'Номер');
        $this->sheetReport->setCellValueByColumnAndRow(1, $this->reportRow, 'Текст ошибки');
        $this->sheetReport->setCellValueByColumnAndRow(2, $this->reportRow, 'Данные');
        $this->reportRow++;
    }

    protected function addReportRow($i, OfomsAttachRESTForm $form, $message)
    {
        $this->sheetReport->setCellValueByColumnAndRow(0, $this->reportRow, $i);
        $this->sheetReport->setCellValueByColumnAndRow(1, $this->reportRow, $message);
        $this->sheetReport->setCellValueByColumnAndRow(2, $this->reportRow, implode('; ', [
            $form->doctor,
            $form->policy,
            $form->fam,
            $form->im,
            $form->ot,
            $form->dr,
        ]));
        $this->reportRow++;
    }

    protected function getHighestRow()
    {
        /** @var \PHPExcel_Reader_Excel2007|\PHPExcel_Reader_Excel5 $objReader */
        $objReader = \PHPExcel_IOFactory::createReaderForFile($this->fileName);
        $objPHPExcel = $objReader->load($this->fileName);
        $highestRow = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
        unset($objPHPExcel);
        unset($objReader);
        return $highestRow;
    }

    protected function getForm(array $row)
    {
        $doctor = $row[0];
        $policy = $row[1];
        if (preg_match('/^(\b[а-я-]+\b)\s(\b[а-я-]+\b)(\s(\b[а-я-]+\b))?(\s(\b[а-я-]+\b))?$/iu', $row[2], $matches)) {
            $fam = $matches[1];
            $im = $matches[2];
            $ot = $matches[4];
            $dr = $row[3];
        } else {
            $fam = $row[2];
            $im = $row[3];
            $ot = $row[4];
            $dr = $row[5];
        }

        return new OfomsAttachRESTForm([
            'doctor' => $doctor,
            'policy' => $policy,
            'fam' => $fam,
            'im' => $im,
            'ot' => $ot,
            'dr' => $dr,
        ]);
    }
}