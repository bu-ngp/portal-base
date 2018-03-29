<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 04.06.2017
 * Time: 10:43
 */

namespace common\widgets\ReportLoader;

use Knp\Snappy\Pdf;
use PHPExcel;
use PHPExcel_Worksheet_PageMargins;
use PHPExcel_Worksheet_PageSetup;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Класс для обработки отчета по модели [\yii\db\ActiveRecord](https://www.yiiframework.com/doc/api/2.0/yii-db-activerecord).
 *
 * Пример использования:
 *
 * ```php
 * protected function letsExport($format = 'pdf')
 * {
 *     $report = new ReportByModel($this->gridView->dataProvider, $format);
 *     $report->setFilterString('foo > 12; foo < 20;');
 *     $report->reportDisplayName = 'Файл отчета';
 *     $report->reportid = 'FooReport';
 *     return $report->report();
 * }
 * ```
 */
class ReportByModel
{
    /** Тип отчета Excel */
    const EXCEL = 'xls';
    /** Тип отчета PDF */
    const PDF = 'pdf';

    /**
     * @var string Уникальное имя определенного вида отчетов
     */
    public $reportid;
    /**
     * @var string Имя файла отчета
     */
    public $reportDisplayName;

    /** @var ActiveDataProvider */
    private $dataProvider;
    /** @var  ActiveRecord */
    private $activeRecord;
    private $type;
    /** @var ReportProcess */
    private $loader;
    /** @var PHPExcel */
    private $objPHPExcel;
    private $filterString;
    private $columnsFromGrid;
    private $highestColumn = 0;
    private $row           = 3;

    /** @var array Границы таблицы */
    private $borders = [
        'borders' => [
            'allborders' => [
                'style' => \PHPExcel_Style_Border::BORDER_THIN,
            ],
        ],
    ];
    /** @var array Жирный шрифт для шапки таблицы */
    private $fontCaption = [
        'font' => [
            'bold' => true,
        ],
        'alignment' => [
            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        ],
    ];
    /** @var array Шрифт заголовка отчета */
    private $title = [
        'font' => [
            'bold' => true,
            'size' => 14,
        ],
    ];
    /** @var array Шрифт вспомогательного заголовка отчета (Дата, фильтр) */
    private $subTitle = [
        'font' => [
            'italic' => true,
        ],
    ];
    /** @var array Шрифт данных таблицы */
    private $dataFont = [
        'font' => [
            'size' => 8,
        ],
    ];

    /**
     * Создать экземляр текущего класса
     *
     * @param ActiveDataProvider $dataProvider Провайдер данных, по которому будет формироваться отчет.
     * @param string $type Тип отчета `PDF` или `EXCEL`.
     * @return ReportByModel
     */
    public static function execute(ActiveDataProvider $dataProvider, $type = ReportByModel::EXCEL)
    {
        return new self($dataProvider, $type);
    }

    /**
     * Конструктор класса обработчика отчета по модели.
     *
     * @param ActiveDataProvider $dataProvider Провайдер данных, по которому будет формироваться отчет.
     * @param string $type Тип отчета `PDF` или `EXCEL`.
     * @param array $columns Набор имен колонок, которые будут отражены в отчете. Если пусто, будут выведены все колонки.
     */
    public function __construct(ActiveDataProvider $dataProvider, $type = ReportByModel::EXCEL, $columns = [])
    {
        $this->prepare($dataProvider, $this->convertType($type), $columns);
        return $this;
    }

    private function setColumns()
    {
        $modelClass = $this->dataProvider->query->modelClass;
        $this->activeRecord = new $modelClass;

        $this->columnsFromGrid = empty($this->columnsFromGrid) ? array_map(function ($column) {
            return ['attribute' => $column];
        }, array_keys($this->activeRecord->getAttributes())) : array_values($this->columnsFromGrid);

        $this->highestColumn = count($this->columnsFromGrid);
    }

    private function prepare(ActiveDataProvider $dataProvider, $type, $columns)
    {
        ini_set('max_execution_time', 7200);  // 1000 seconds
        ini_set('memory_limit', 3000000000); // 1Gbyte Max Memory

        $this->dataProvider = $dataProvider;
        $this->dataProvider->pagination = false;
        $this->columnsFromGrid = $columns;
        $this->setColumns();
        $this->type = $type;
        $this->reportid = 'test'; //** formName() */
        $this->reportDisplayName = 'Report_' . date('Y-m-d');
    }

    private function convertType($type)
    {
        switch ($type) {
            case ReportByModel::EXCEL:
                return 'Excel2007';
            case ReportByModel::PDF:
                return 'PDF';
        }

        throw new \Exception('convertType("' . $type . '") not access');
    }

    /**
     * Начать процесс обработки отчета.
     *
     * @return string ссылка на скачивание отчета.
     */
    public function report()
    {
        $this->loader = ReportProcess::start($this->reportid, $this->reportDisplayName, $this->type);
        $this->objPHPExcel = new PHPExcel();
        $this->make();
        $this->createFile();
        return 'report-loader/report/download?id=' . $this->loader->getId();
    }

    private function make()
    {
        $this->makeTitle();
        $this->makeDate();
        $this->makeFilter();

        $rowGridBegin = $this->row;

        $this->pageSetup();
        $this->makeCaption();

        $models = $this->dataProvider->getModels();

        if (count($models) > 0) {
            /** @var ActiveRecord $ar */
            for ($i = 1; $i <= 1; $i++) {
                foreach ($models as $record => $ar) {
                    if (!$this->setLoaderPercent($record, count($models))) {
                        return false;
                    }

                    $this->objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $this->row, $this->row - $rowGridBegin);

                    foreach ($this->columnsFromGrid as $index => $column) {
                        $value = $this->itemsValueExists($ar, $column->attribute);
                        $value = $this->filterDateTimeValue($value);
                        $this->objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($index + 1, $this->row, $value);
                    }

                    $this->row++;
                }
            }
            $this->row--;
        }

        $this->objPHPExcel->getActiveSheet()->getColumnDimension()->setWidth(6);
        for ($i = 1; $i <= $this->highestColumn; $i++) {
            $this->objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);
        }

        $this->objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0, $rowGridBegin, $this->highestColumn, $this->row)->applyFromArray($this->borders);
        $this->objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0, 2, $this->highestColumn, $this->row)->applyFromArray($this->dataFont);

        $this->widthSetup();
    }

    private function wkhtmltopdfBinary()
    {
        switch (DIRECTORY_SEPARATOR) {
            case '/':
                return '/usr/local/bin/wkhtmltopdf-amd64'/*Yii::getAlias('@vendor') . '/bin/wkhtmltopdf-amd64'*/
                    ;
            case '\\':
                return Yii::getAlias('@vendor') . '/bin/wkhtmltopdf.exe.bat';
        }

        return '';
    }

    private function createFile()
    {
        if (!$this->loader->isActive()) {
            return false;
        }

        if (!file_exists($binaryPath = $this->wkhtmltopdfBinary())) {
            throw new \Exception('Need setup "wkhtmltopdf"');
        }

        if ($this->type === 'PDF') {
            /** @var \PHPExcel_Writer_HTML $objWriter */
            $objWriter = \PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'HTML');
            ob_start();
            $objWriter->save('php://output');
            $output = ob_get_clean();

            $output = str_replace('page-break-after:always', 'page-break-after:auto', $output);
            $snappy = new Pdf($binaryPath);
            $snappy->generateFromHtml($output, $this->loader->getFileName(), ['footer-right' => '[page] - [toPage]']);
        } else {
            /** @var \PHPExcel_Writer_Excel2007 $objWriter */
            $objWriter = \PHPExcel_IOFactory::createWriter($this->objPHPExcel, $this->type);
            $objWriter->save($this->loader->getFileName());
        }

        if ($this->loader->isActive()) {
            $this->loader->end();
        } else {
            unlink($this->loader->getFileName());
            return false;
        }
    }

    /**
     * Устанавливает строку с условиями дополнительного фильтра под заголовком отчета.
     *
     * @param null|string $filterString
     */
    public function setFilterString($filterString = null)
    {
        if (is_string($filterString)) {
            $this->filterString = $filterString;
        }
    }

    private function makeTitle()
    {
        $this->objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, $this->reportDisplayName);
        $this->objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0, 1)->applyFromArray($this->title);
        $this->objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow(0, 1, $this->highestColumn, 1);
        // Устанавливаем имя листа и книги
        $this->objPHPExcel->getActiveSheet()->setTitle(mb_substr($this->reportDisplayName, 0, 32, 'UTF-8'));
        $this->objPHPExcel->getProperties()->setTitle($this->reportDisplayName);
    }

    private function makeDate()
    {
        $this->objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 2, 'Дата: ' . date('d.m.Y'));
        $this->objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0, 2)->applyFromArray($this->subTitle);
        $this->objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow(0, 2, $this->highestColumn, 2);
    }

    private function makeFilter()
    {
        if (!empty($this->filterString)) {
            $this->objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $this->row, $this->filterString);
            $this->objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0, $this->row)->applyFromArray($this->subTitle);
            $this->objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow(0, $this->row, $this->highestColumn, $this->row);
            $this->row++;
        }
    }

    private function pageSetup()
    {
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
        $this->objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($this->row, $this->row);
    }

    private function makeCaption()
    {
        $this->objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $this->row, '№');

        foreach ($this->columnsFromGrid as $index => $column) {
            $this->objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($index + 1, $this->row, $this->activeRecord->getAttributeLabel($column->attribute));
        }

        $this->objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0, $this->row, $this->highestColumn, $this->row)->applyFromArray($this->fontCaption);
        $this->row++;
    }

    private function setLoaderPercent($current, $count)
    {
        if ($current % 100 === 0 && !$this->loader->isActive()) {
            return false;
        }

        if ($current % 100 === 0) {
            $this->loader->set(round(95 * $current / $count));
        }

        return true;
    }

    private function itemsValueExists(ActiveRecord $model, $attribute)
    {
        $modelWithValue = $model;
        if (preg_match('/\./', $attribute)) {
            $modelWithValue = ArrayHelper::getValue($model, preg_replace('/(.*)\.(\w+)/', '$1', $attribute));
            $attribute = preg_replace('/(.*)\.(\w+)/', '$2', $attribute);
        }

        if (method_exists($modelWithValue, 'itemsValues') && $items = $modelWithValue::itemsValues($attribute)) {
            return $items[ArrayHelper::getValue($modelWithValue, $attribute)];
        }

        return $modelWithValue->$attribute;
    }

    private function widthSetup()
    {
        $this->objPHPExcel->getActiveSheet()->calculateColumnWidths();
        $widthPage = 0;
        for ($i = 0; $i <= $this->highestColumn; $i++) {
            $currentWidth = $this->objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->getWidth();
            $widthPage += $currentWidth;
            $this->objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(false);
            if ($currentWidth * 1.1 > 70) {
                $this->objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setWidth(70);
                $this->objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($i, 1, $i, $this->row)->getAlignment()->setWrapText(true);
            } else {
                $this->objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setWidth($currentWidth * 1.1);
            }
        }

        if ($widthPage > 116) {
            $this->objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        }
    }

    protected function filterDateTimeValue($value)
    {
        $value = preg_replace('/(\d{4})-(\d{2})-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/', '$3.$2.$1 $5:$6:$7', $value);
        return preg_replace('/(\d{4})-(\d{2})-(\d{2})/', '$3.$2.$1', $value);
    }
}