<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 04.06.2017
 * Time: 10:07
 */

namespace domain\proc;


use domain\proc\models\ReportLoader;
use Yii;
use yii\web\HttpException;

class ReportProcess
{
    private $loader;

    public static function start($reportId, $reportDisplayName, $reportType = '')
    {
        return new self($reportId, $reportDisplayName, $reportType);
    }

    public function __construct($reportId, $reportDisplayName, $reportType = '')
    {
        $this->loader = new ReportLoader([
            'rl_report_id' => $reportId,
            'rl_report_displayname' => $reportDisplayName,
            'rl_report_type' => $reportType,
        ]);

        if (!$this->loader->save()) {
            throw new HttpException(500, print_r($this->loader->getErrors(), true));
        }
    }

    public function set($percent)
    {
        if (filter_var($percent, FILTER_VALIDATE_INT) && $percent > 0) {
            $this->loader->load(['ReportLoader' => [
                'rl_percent' => $percent,
            ]]);
            $this->loader->save(false);
        }
    }

    public static function cancel($rl_id)
    {
        $model = ReportLoader::findOne($rl_id);

        if ($model) {
            $model->load(['ReportLoader' => [
                'rl_status' => 3,
                'rl_percent' => 0,
            ]]);

            return $model->save(false);
        }

        return false;
    }

    public function isActive()
    {
        return !$this->loader->rl_status == 1;
    }

    public function getFileName()
    {
        return $this->loader->rl_report_filename;
    }

    public function end()
    {
        $this->loader->load(['ReportLoader' => [
            'rl_status' => 2,
            'rl_percent' => 100,
        ]]);
        $this->loader->save(false);
    }
}