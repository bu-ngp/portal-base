<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 04.06.2017
 * Time: 10:07
 */

namespace domain\proc;


use domain\proc\models\ReportLoader;

class ReportProcess
{
    private $loader;

    public static function start($reportId, $reportDisplayName)
    {
        return new self($reportId, $reportDisplayName);
    }

    public function __construct($reportId, $reportDisplayName)
    {
        $this->loader = new ReportLoader([
            'rl_report_id' => $reportId,
            'rl_report_displayname' => $reportDisplayName,
        ]);

        $this->loader->save(false);
    }

    public function set($percent)
    {
        if (is_int($percent) && $percent > 0) {
            $this->loader->load(['ReportLoader' => [
                'rl_percent' => $percent,
            ]]);
            $this->loader->save(false);
        }
    }

    public static function cancel($processId, $reportId)
    {
        $model = ReportLoader::find()->where([
            'rl_processId' => $processId,
            'rl_reportId' => $reportId,
        ])->one();

        if ($model) {
            $model->load(['ReportLoader' => [
                'rl_status' => 3,
                'rl_percent' => 0,
            ]]);
            $model->save(false);
        }
    }

    public function isActive()
    {
        return $this->loader->rl_status == 2;
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