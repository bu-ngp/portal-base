<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 20.06.2017
 * Time: 8:49
 */

namespace common\widgets\ReportLoader;


use PHPExcel;
use PHPExcel_Settings;
use ReflectionClass;
use Yii;

abstract class ReportByTemplate
{
    const EXCEL = 'xls';
    const PDF = 'pdf';

    public $title = 'Report';
    private $template;
    private $type = 'Excel2007';
    /** @var  PHPExcel */
    protected $PHPExcel;
    /** @var ReportProcess */
    protected $loader;
    protected $params;

    public static function lets()
    {
        return new static();
    }

    public function __construct()
    {

    }

    abstract public function body();

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

    public function type($type)
    {
        if (in_array($type, [ReportByTemplate::EXCEL, ReportByTemplate::PDF])) {
            $this->type = $this->convertType($type);
        }

        return $this;
    }

    public function params(array $params)
    {
        $this->params = $params;

        return $this;
    }

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

    private function saveFile()
    {
        if (!$this->loader->isActive()) {
            return false;
        }

        if ($this->type === 'PDF' && !PHPExcel_Settings::setPdfRenderer(PHPExcel_Settings::PDF_RENDERER_MPDF, Yii::getAlias('@vendor') . '/mpdf/mpdf')) {
            throw new \Exception('NOTICE PHPExcel: Please set the $rendererName and $rendererLibraryPath values');
        }

        $this->PHPExcel->getProperties()->setTitle($this->loader->getDisplayName());

        /** @var \PHPExcel_Writer_PDF_mPDF $objWriter */
        $objWriter = \PHPExcel_IOFactory::createWriter($this->PHPExcel, $this->type);
        $objWriter->save($this->loader->getFileName());

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