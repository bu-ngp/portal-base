<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 16.06.2017
 * Time: 18:53
 */

namespace common\widgets\GridView\services;


use common\widgets\GridView\GridView;

class GWExportGridConfig
{
    public $enable = true;
    public $format = GridView::EXCEL;
    public $idReportLoader;

    public static function set()
    {
        return new self();
    }

    public function enable($enabled)
    {
        $this->enable = $enabled;
        return $this;
    }

    public function format($format)
    {
        $this->format = $format;
        return $this;
    }

    public function idReportLoader($idReportLoader)
    {
        $this->idReportLoader = $idReportLoader;
        return $this;
    }

    public function build()
    {
        if (!is_bool($this->enable)) {
            throw new \Exception('enable() must be Boolean');
        }

        if (!(in_array($this->format, [GridView::EXCEL, GridView::PDF]))) {
            throw new \Exception('format() must be only "xls" or "pdf"');
        }

        if ($this->enable && (!is_string($this->idReportLoader) || empty($this->idReportLoader))) {
            throw new \Exception('idReportLoader() must be string with id ReportLoader HTML element');
        }

        return $this;
    }
}