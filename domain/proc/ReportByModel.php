<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 04.06.2017
 * Time: 10:43
 */

namespace domain\proc;


use PHPExcel;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;

class ReportByModel
{
    const EXCEL = 'Excel2007';
    const PDF = 'PDF';

    public $reportid;
    public $reportDisplayName;

    /** @var ActiveDataProvider */
    private $dataProvider;
    private $type;
    /** @var ReportProcess */
    private $loader;
    /** @var PHPExcel */
    private $objPHPExcel;
    /** @var array Границы таблицы */
    private $ramka = [
        'borders' => [
            'allborders' => [
                'style' => \PHPExcel_Style_Border::BORDER_THIN,
            ],
        ],
    ];
    /** @var array Жирный шрифт для шапки таблицы */
    private $font = [
        'font' => [
            'bold' => true
        ],
        'alignment' => [
            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER
        ]
    ];

    public static function execute(ActiveDataProvider $dataProvider, $type = 2)
    {
        return new self($dataProvider, $type);
    }

    public function __construct(ActiveDataProvider $dataProvider, $type = 2)
    {
        $this->prepare($dataProvider, $type);
        $this->make();
        $this->createFile();
    }

    private function prepare(ActiveDataProvider $dataProvider, $type)
    {
        ini_set('max_execution_time', 7200);  // 1000 seconds
        ini_set('memory_limit', 3000000000); // 1Gbyte Max Memory

        $this->dataProvider = $dataProvider;
        $this->dataProvider->pagination = false;
        $this->type = $type;
        $this->reportid = 'test'; //** formName() */
        $this->reportDisplayName = 'Report_' . date('Y-m-d');
        $this->loader = ReportProcess::start($this->reportid, $this->reportDisplayName);
        $this->objPHPExcel = new PHPExcel();

    }

    private function make()
    {
        $this->objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, $this->reportDisplayName);
        $this->objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0, 1)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14
            ],
        ]);

        $this->objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 2, 'Дата: ' . date('d.m.Y'));
        $this->objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0, 2)->applyFromArray([
            'font' => [
                'italic' => true
            ]
        ]);

        /* Filter */

        $models = $this->dataProvider->getModels();

        $r = 6;
        if (count($models) > 0) {
            $c = 0;
            $this->objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $r - 1, '№');
            /** @var ActiveRecord $models */
            foreach ($models[0]->attributeLabels() as $label) {
                $c++;
                $this->objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($c, $r - 1, $label);
            }

            /** @var array $models */
            /** @var ActiveRecord $ar */
            foreach ($models as $row => $ar) {
                if ($row % 50 === 0 && !$this->loader->isActive()) {
                    return false;
                }

                $c = 0;
                $this->objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($c, $r, $r - 5);
                foreach ($ar->attributes as $attr) {
                    $c++;
                    $this->objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($c, $r, $attr);
                }
            }
        }

    }

    private function createFile()
    {
        if (!$this->loader->isActive()) {
            return false;
        }

        // присваиваем имя файла от имени модели
        $FileName = $this->reportDisplayName;

        // Устанавливаем имя листа
        // $objPHPExcel->getActiveSheet()->setTitle($FileName);

        // Выбираем первый лист
        $this->objPHPExcel->setActiveSheetIndex(0);
        // Формируем файл Excel
        $objWriter = \PHPExcel_IOFactory::createWriter($this->objPHPExcel, $this->type);
        $FileName = DIRECTORY_SEPARATOR === '/' ? $FileName : mb_convert_encoding($FileName, 'Windows-1251', 'UTF-8');
        // Сохраняем файл в папку "files"
        $objWriter->save($this->loader->getFileName());
        // Возвращаем имя файла Excel
        if (DIRECTORY_SEPARATOR === '/')
            echo $FileName;
        else
            echo mb_convert_encoding($FileName, 'UTF-8', 'Windows-1251');

        $this->loader->end();
    }
}