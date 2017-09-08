<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 16.06.2017
 * Time: 17:39
 */

namespace common\widgets\GridView\services;


use common\widgets\GridView\GridView;

class GWExportGridConfiguration
{
    protected $enable = false;
    protected $format = [GridView::EXCEL, GridView::PDF];
    protected $idReportLoader;

    public function __construct($config = [])
    {
        if ($config['enable'] !== false && empty($config['idReportLoader'])) {
            throw new \Exception('idReportLoader required');
        }

       if (is_string($config['idReportLoader'])) {
            if (!empty($config['format']) && is_array($config['format'])) {
                $this->format = $config['format'];
            }

            if (!is_string($config['idReportLoader'])) {
                throw new \Exception('idReportLoader must be string with id ReportLoader HTML element');
            }

            $this->enable = true;
            $this->idReportLoader = $config['idReportLoader'];
        }
    }

    public function isEnable()
    {
        return $this->enable;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function getIdReportLoader()
    {
        return $this->idReportLoader;
    }
}