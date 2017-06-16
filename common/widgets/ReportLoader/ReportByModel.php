<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 04.06.2017
 * Time: 10:43
 */

namespace common\widgets\ReportLoader;


use PHPExcel;
use PHPExcel_Cell;
use PHPExcel_Style_Alignment;
use PHPExcel_Worksheet_PageMargins;
use PHPExcel_Worksheet_PageSetup;
use Yii;
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
    private $additionalFilter;
    private $filterString;
    private $columnsFromGrid;

    public static function execute(ActiveDataProvider $dataProvider, $type = 'Excel2007')
    {
        return new self($dataProvider, $type);
    }

    public function __construct(ActiveDataProvider $dataProvider, $type = 'Excel2007')
    {
        $this->prepare($dataProvider, $type);
        return $this;
    }

    public function report()
    {
        $this->loader = ReportProcess::start($this->reportid, $this->reportDisplayName, $this->type);
        $this->objPHPExcel = new PHPExcel();
        $this->make();
        $this->createFile();
        return 'report-loader/report/download?id=' . $this->loader->getId();
    }

    public function setAdditionalFilterString($additionalFilterString = null)
    {
        if (is_string($additionalFilterString)) {
            $this->additionalFilter = $additionalFilterString;
        }
    }

    public function setFilterString($filterString = null)
    {
        if (is_string($filterString)) {
            $this->filterString = $filterString;
        }
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

        $r = 5;
        if (!empty($this->filterString . $this->additionalFilter)) {
            $filter = Yii::t('wk-widget-gridview', 'Add. filter: ') . $this->filterString . $this->additionalFilter;
            $this->objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 3, $filter);
            $this->objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0, 3)->applyFromArray([
                'font' => [
                    'italic' => true
                ]
            ]);
            $r++;
        }

        $rowGridBegin = $r - 1;

        $models = $this->dataProvider->getModels();

        if (count($models) > 0) {
            $this->columnsFromGrid = empty($this->columnsFromGrid) ? array_walk(array_keys($models[0]->getAttributes()), function (&$column) {
                return ['attribute' => $column];
            }) : $this->columnsFromGrid;

            $c = 0;
            $this->objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $r - 1, '№');

            foreach ($this->columnsFromGrid as $column) {
                $c++;
                $this->objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($c, $r - 1, $models[0]->getAttributeLabel($column['attribute']));
            }

            $highestColumn = $this->objPHPExcel->getActiveSheet()->getHighestColumn();
            $this->objPHPExcel->getActiveSheet()->getStyle('A' . $rowGridBegin . ':' . $highestColumn . $rowGridBegin)->applyFromArray($this->font);


            /** @var array $models */
            /** @var ActiveRecord $ar */
            for ($i = 1; $i <= 1; $i++) {
                foreach ($models as $row => $ar) {
                    if ($row % 100 === 0 && !$this->loader->isActive()) {
                        return false;
                    }

                    if ($row % 100 === 0) {
                        $this->loader->set(round(95 * $i / 300));
                    }

                    $c = 0;
                    $this->objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($c, $r, $r - $rowGridBegin);

                    foreach ($this->columnsFromGrid as $column) {
                        $c++;

                        $value = $this->itemsValueExists($ar, $column['attribute']);
                        $this->objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($c, $r, $value);
                    }

                    $r++;
                }
            }
        }

        $highestRow = $this->objPHPExcel->getActiveSheet()->getHighestRow();

        $this->objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow(0, 1, PHPExcel_Cell::columnIndexFromString($highestColumn) - 1, 1);
        $this->objPHPExcel->getActiveSheet()->getStyle("A1:B1")->applyFromArray(['alignment' => [
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        ]]);
        foreach (range('A', $highestColumn) as $columnID) {
            $this->objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }

        $this->objPHPExcel->getActiveSheet()->getStyle('A' . $rowGridBegin . ':' . $highestColumn . $highestRow)->applyFromArray($this->ramka);
        $this->objPHPExcel->getActiveSheet()->getStyle('A2:' . $highestColumn . $highestRow)->applyFromArray(['font' => [
            'size' => 8
        ],]);

        $pageMargins = new PHPExcel_Worksheet_PageMargins;
        $pageMargins->setBottom(0.393700787401575);
        $pageMargins->setFooter(0.196850393700787);
        $pageMargins->setHeader(0.196850393700787);
        $pageMargins->setLeft(0.196850393700787);
        $pageMargins->setRight(0.196850393700787);
        $pageMargins->setTop(0.196850393700787);
        $this->objPHPExcel->getActiveSheet()->setPageMargins($pageMargins);
        $this->objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddFooter('&P');
        $this->objPHPExcel->getActiveSheet()->getHeaderFooter()->setEvenFooter('&P');
        $this->objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($rowGridBegin, $rowGridBegin);

        $this->objPHPExcel->getActiveSheet()->getColumnDimension()->setAutoSize(false);
        $this->objPHPExcel->getActiveSheet()->getColumnDimension()->setWidth(6);
        $this->objPHPExcel->getActiveSheet()->calculateColumnWidths();
        $widthPage = 0;
        for ($i = 0; $i < PHPExcel_Cell::columnIndexFromString($highestColumn); $i++) {
            $ColumnAddress = PHPExcel_Cell::stringFromColumnIndex($i);
            $widthPage += $this->objPHPExcel->getActiveSheet()->getColumnDimension($ColumnAddress)->getWidth();
            if ($this->objPHPExcel->getActiveSheet()->getColumnDimension($ColumnAddress)->getWidth() > 70) {
                $this->objPHPExcel->getActiveSheet()->getColumnDimension($ColumnAddress)->setAutoSize(false);
                $this->objPHPExcel->getActiveSheet()->getColumnDimension($ColumnAddress)->setWidth(70);
                $this->objPHPExcel->getActiveSheet()->getStyle($ColumnAddress . '1' . ':' . $ColumnAddress . $highestRow)->getAlignment()->setWrapText(true);
            }
        }

        if ($widthPage > 116) {
            $this->objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
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
        $this->objPHPExcel->getActiveSheet()->setTitle(mb_substr($this->reportDisplayName, 0, 32, 'UTF-8'));

        // Выбираем первый лист
        $this->objPHPExcel->setActiveSheetIndex(0);
        // Формируем файл Excel
        $objWriter = \PHPExcel_IOFactory::createWriter($this->objPHPExcel, $this->type);
        $FileName = DIRECTORY_SEPARATOR === '/' ? $FileName : mb_convert_encoding($FileName, 'Windows-1251', 'UTF-8');
        // Сохраняем файл в папку "files"
        $objWriter->save($this->loader->getFileName());

        if ($this->loader->isActive()) {
            $this->loader->end();
        } else {
            unlink($this->loader->getFileName());
            return false;
        }

        // Возвращаем имя файла Excel
        /*   if (DIRECTORY_SEPARATOR === '/')
               echo $FileName;
           else
               echo mb_convert_encoding($FileName, 'UTF-8', 'Windows-1251');*/
    }

    public function columnsFromGrid(array $columns)
    {
        $this->columnsFromGrid = $columns;
    }

    protected function itemsValueExists(ActiveRecord $model, $attribute)
    {
        if (method_exists($model, 'itemsValues') && $items = $model::itemsValues($attribute)) {
            return $items[$model[$attribute]];
        }

        return $model[$attribute];
    }
}