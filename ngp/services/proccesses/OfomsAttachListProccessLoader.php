<?php

namespace ngp\services\proccesses;

use doh\services\classes\ProcessLoader;
use ngp\services\classes\ChunkReadFilter;
use ngp\services\forms\OfomsAttachListForm;
use ngp\services\forms\OfomsAttachRESTForm;
use ngp\services\repositories\OfomsRepository;
use PHPExcel;
use PHPExcel_IOFactory;
use Yii;
use yii\helpers\Html;

class OfomsAttachListProccessLoader extends ProcessLoader
{
    const CHUNK_SIZE = 1000;
    const START_ROW = 2;

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
       // throw new \Exception('error');

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
                    $this->addPercentComplete(round($i * 100 / $this->highestRow));
                }

                $form = new OfomsAttachRESTForm([
                    'doctor' => $row[0],
                    'policy' => $row[1],
                    'fam' => $row[2],
                    'im' => $row[3],
                    'ot' => $row[4],
                    'dr' => $row[5],
                ]);

                if ($form->validate()) {
                    $repository = new OfomsRepository();
                    $result = $repository->attach($form->ffio, $form->policy, $form->doctor);

                    if ($result['status'] < 1) {
                        $this->addReportRow($i, $form, $result['message']);
                      //  $this->addReportRow($i, $form, $this->sheetReport['message']);
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

        /** @var \PHPExcel_Writer_CSV $objWriter */
        $objWriter = \PHPExcel_IOFactory::createWriter($this->objPHPExcelReport, 'CSV');
        $objWriter->setDelimiter(';');
        $reportPath = Yii::getAlias('@ngp/reports_attach-list/' . date('Y-m-d') . time() . '.csv');
        $objWriter->save($reportPath);

        file_put_contents($reportPath, mb_convert_encoding(file_get_contents($reportPath), 'windows-1251', 'UTF-8'));

        $this->addFile($reportPath, 'Результат прикрепления.csv');
        $this->addShortReport("Итоги обработки:\n- Всего записей: {$this->rows};\n- Успешно: {$this->success};\n- Ошибок: {$this->error};");
//            file_put_contents('test.txt', 'goood', FILE_APPEND);

    }

    protected function addReportHeader()
    {
        $this->sheetReport->setCellValueByColumnAndRow(0, $this->reportRow, 'Номер');
        $this->sheetReport->setCellValueByColumnAndRow(1, $this->reportRow, 'Полис');
        $this->sheetReport->setCellValueByColumnAndRow(2, $this->reportRow, 'ФИО');
        $this->sheetReport->setCellValueByColumnAndRow(3, $this->reportRow, 'Дата рождения');
        $this->sheetReport->setCellValueByColumnAndRow(4, $this->reportRow, 'ИНН врача');
        $this->sheetReport->setCellValueByColumnAndRow(5, $this->reportRow, 'Текст ошибки');
        $this->reportRow++;
    }

    protected function addReportRow($i, OfomsAttachRESTForm $form, $message)
    {
        $this->sheetReport->setCellValueByColumnAndRow(0, $this->reportRow, $i - 1);
        $this->sheetReport->setCellValueByColumnAndRow(1, $this->reportRow, "'" . $form->policy);
        $this->sheetReport->setCellValueByColumnAndRow(2, $this->reportRow, $form->fam . ' ' . $form->im . ' ' . $form->ot);
        $this->sheetReport->setCellValueByColumnAndRow(3, $this->reportRow, $form->dr);
        $this->sheetReport->setCellValueByColumnAndRow(4, $this->reportRow, "'" . $form->doctor);
        $this->sheetReport->setCellValueByColumnAndRow(5, $this->reportRow, $message);
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
}