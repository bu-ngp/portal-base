<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 04.06.2017
 * Time: 10:07
 */

namespace common\widgets\ReportLoader;


use common\widgets\ReportLoader\models\ReportLoader;
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
            'rl_status' => 1,
        ]);

        if (!$this->loader->save()) {
            throw new HttpException(500, print_r($this->loader->getErrors(), true));
        }
    }

    public function set($percent)
    {
        if (filter_var($percent, FILTER_VALIDATE_INT) && $percent > 0) {
            $model = ReportLoader::findOne($this->loader->primaryKey);

            if ($model) {
                $model->load(['ReportLoader' => [
                    'rl_percent' => $percent,
                ]]);

                return $model->save(false);
            }

            return false;
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
        $model = ReportLoader::findOne($this->loader->primaryKey);

        if ($model) {
            return $model->rl_status == 1;
        }

        return false;
    }

    public function getFileName()
    {
        return $this->loader->rl_report_filename;
    }

    public function getId()
    {
        return $this->loader->primaryKey;
    }

    public function end()
    {
        $model = ReportLoader::find()
            ->andWhere([
                'rl_id' => $this->loader->primaryKey,
            ])
            ->andWhere(['not', ['rl_status' => 3]])
            ->one();

        if ($model) {
            $model->load(['ReportLoader' => [
                'rl_status' => 2,
                'rl_percent' => 100,
            ]]);

            return $model->save(false);
        }

        return false;
    }
}