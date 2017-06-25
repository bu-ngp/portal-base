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
    public $format = [GridView::EXCEL, GridView::PDF];
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
        if (!is_array($format)) {
            $format = [$format];
        }

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

        foreach ($this->format as $format) {
            if (!(in_array($format, [GridView::EXCEL, GridView::PDF]))) {
                throw new \Exception('format() must be only "xls" or "pdf" in array parameter');
            }
        }

        if ($this->enable && (!is_string($this->idReportLoader) || empty($this->idReportLoader))) {
            throw new \Exception('idReportLoader() must be string with id ReportLoader HTML element');
        }

        return $this;
    }
}