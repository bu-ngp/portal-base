<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 20.06.2017
 * Time: 8:49
 */

namespace common\widgets\ReportLoader;


use Knp\Snappy\Pdf;
use PHPExcel;
use ReflectionClass;
use Yii;

/**
 * Абстрактный класс для формирования классов отчетов по шаблонам `Excel`.
 *
 * **Пример использования:**
 *
 * ```php
 * // Класс отчета, наследованный от абстрактного класса ReportByTemplate
 * class RolesReport extends ReportByTemplate
 * {
 *     // Имя отчета (имя файла отчета)
 *     public $title = 'Роли';
 *
 *     // Тело выполнения отчета
 *     public function body()
 *     {
 *         $PHPExcel = $this->PHPExcel;
 *         $PHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 2, 'Дата: ' . date('d.m.Y'));
 *
 *         // Использование дополнительного параметра $this->params['view']
 *         $roles = AuthItem::find()->andWhere(['view' => $this->params['view']])->all();
 *
 *         $row = 5;
 *         // @var AuthItem $ar
 *         foreach ($roles as $current => $ar) {
 *             $PHPExcel->getActiveSheet()->insertNewRowBefore($row);
 *             $PHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $ar->description);
 *             $row++;
 *             // Устанавливаем процент выполнения отчета для каждой 50-й записи
 *             if ($current % 50 === 0) {
 *                 $this->loader->set(round(95 * $current / count($roles)))
 *             }
 *         }
 *     }
 * }
 *
 * // Controller
 * public function actionReport()
 * {
 *     return RolesReport::lets()
 *         ->assignTemplate('rolesTemplate.xlsx')
 *         ->params(['view' => 1])
 *         ->type('pdf')
 *         ->save();
 * }
 * ```
 */
abstract class ReportByTemplate
{
    /** Тип отчета Excel */
    const EXCEL = 'xls';
    /** Тип отчета PDF */
    const PDF = 'pdf';

    /** @var string Имя отчета (имя файла отчета) */
    public  $title = 'Report';
    private $template;
    private $type  = 'Excel2007';
    /** @var PHPExcel объект `PHPExcel` */
    protected $PHPExcel;
    /** @var ReportProcess объект [[ReportProcess]] */
    protected $loader;
    /** @var array Дополнительные параметры отчета */
    protected $params;

    /**
     * Создать экземляр текущего класса
     *
     * @return static
     */
    public static function lets()
    {
        return new static();
    }

    /**
     * Тело процесса обработки отчета.
     *
     * @return mixed
     */
    abstract public function body();

    /**
     * Подключить файл шаблон в формате `Excel`.
     *
     * @param string $template Имя файла шаблона.
     * @return $this
     * @throws \Exception
     */
    public function assignTemplate($template)
    {
        if (!is_string($template)) {
            throw new \Exception("Passed variable must be string");
        }

        $reflectionClass = new ReflectionClass($this);
        $templatePath = dirname($reflectionClass->getFileName()) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $template;

        if (!file_exists($templatePath)) {
            throw new \Exception("File ($templatePath) not exists");
        }

        $this->template = $templatePath;
        $this->PHPExcel = \PHPExcel_IOFactory::load($this->template);

        return $this;
    }

    /**
     * Установить тип формируемого отчета. (`xls` или `pdf`).
     *
     * @param string $type Тип формируемого отчета. (`xls` или `pdf`).
     * @return $this
     */
    public function type($type)
    {
        if (in_array($type, [ReportByTemplate::EXCEL, ReportByTemplate::PDF])) {
            $this->type = $this->convertType($type);
        }

        return $this;
    }

    /**
     * Установить дополнительные параметры отчета.
     *
     * @param array $params Массив дополнительных параметров.
     * @return $this
     */
    public function params(array $params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Начать процесс обработки и формирования отчета.
     *
     * @return bool|string
     */
    public function save()
    {
        $this->checkIntegrity();

        $this->loader = ReportProcess::start((new \ReflectionClass($this))->getShortName(), $this->title, $this->type);

        $this->body();

        return $this->saveFile();
    }

    private function checkIntegrity()
    {
        if (empty($this->template)) {
            throw new \Exception('Need apply assignTemplate() method');
        }

        if (!($this->PHPExcel instanceof PHPExcel)) {
            throw new \Exception('Error created PHPExcel');
        }
    }

    private function wkhtmltopdfBinary()
    {
        switch (DIRECTORY_SEPARATOR) {
            case '/':
                return Yii::getAlias('@vendor') . '/bin/wkhtmltopdf';
            case '\\':
                return Yii::getAlias('@vendor') . '/bin/wkhtmltopdf.exe.bat';
        }

        return '';
    }

    private function saveFile()
    {
        if (!$this->loader->isActive()) {
            return false;
        }

        if (!file_exists($binaryPath = $this->wkhtmltopdfBinary())) {
            throw new \Exception('Need setup "wkhtmltopdf"');
        }

        $this->PHPExcel->getProperties()->setTitle($this->loader->getDisplayName());

        if ($this->type === 'PDF') {
            /** @var \PHPExcel_Writer_HTML $objWriter */
            $objWriter = \PHPExcel_IOFactory::createWriter($this->PHPExcel, 'HTML');
            ob_start();
            $objWriter->save('php://output');
            $output = ob_get_clean();

            $output = str_replace('page-break-after:always', 'page-break-after:auto', $output);
            $snappy = new Pdf($binaryPath);
            $snappy->generateFromHtml($output, $this->loader->getFileName(), ['footer-right' => '[page] - [toPage]']);
        } else {
            /** @var \PHPExcel_Writer_Excel2007 $objWriter */
            $objWriter = \PHPExcel_IOFactory::createWriter($this->PHPExcel, $this->type);
            $objWriter->save($this->loader->getFileName());
        }

        if ($this->loader->isActive()) {
            $this->loader->end();
        } else {
            unlink($this->loader->getFileName());
            return false;
        }

        return 'report-loader/report/download?id=' . $this->loader->getId();
    }

    protected function setLoader($current, $count)
    {
        if (!$this->loader->isActive()) {
            return false;
        }

        $this->loader->set(round(95 * $current / $count));
        return true;
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

}