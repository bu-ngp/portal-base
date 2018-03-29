<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 04.06.2017
 * Time: 10:07
 */

namespace common\widgets\ReportLoader;

use common\widgets\ReportLoader\models\ReportLoader;
use yii\web\HttpException;

/**
 * Класс процесса обработки отчетов.
 *
 * Используется в классах обработчиков отчетов:
 * * Обработчик отчета по модели [\yii\db\ActiveRecord](https://www.yiiframework.com/doc/api/2.0/yii-db-activerecord) [[ReportByModel]]
 * * Обработчик отчета по классу шаблону унаследованному от [[ReportByTemplate]]
 */
class ReportProcess
{
    /** @var ReportLoader Класс обработчика отчетов */
    private $loader;

    /**
     * Начать процесс обработки отчета.
     *
     * @param $reportId string Уникальное имя определенного вида отчетов
     * @param $reportDisplayName string Имя файла отчета
     * @param string $reportType тип отчета `xls` или `pdf`
     * @return $this
     */
    public static function start($reportId, $reportDisplayName, $reportType = '')
    {
        return new self($reportId, $reportDisplayName, $reportType);
    }

    /**
     * Констуктор класса процесса обработки отчетов.
     *
     * @param $reportId string Уникальное имя определенного вида отчетов
     * @param $reportDisplayName string Имя файла отчета
     * @param string $reportType тип отчета `xls` или `pdf`
     * @throws HttpException Исключение в случае ошибки сохранения процесса обработки в БД.
     */
    public function __construct($reportId, $reportDisplayName, $reportType = '')
    {
        $this->loader = new ReportLoader([
            'rl_report_id' => $reportId,
            'rl_report_displayname' => $reportDisplayName,
            'rl_report_type' => $reportType,
            'rl_status' => ReportLoader::PROGRESS,
        ]);

        if (!$this->loader->save()) {
            throw new HttpException(500, print_r($this->loader->getErrors(), true));
        }
    }

    /**
     * Устанавливаем количество процентов выполнения отчета.
     *
     * @param $percent int Количество процентов выполнения отчета от 1 до 100.
     * @return bool Результат сохранения в БД.
     */
    public function set($percent)
    {
        if (filter_var($percent, FILTER_VALIDATE_INT) && $percent > 0) {
            $model = ReportLoader::findOne($this->getId());

            if ($model) {
                $model->load(['ReportLoader' => [
                    'rl_percent' => $percent,
                ]]);

                return $model->save(false);
            }
        }

        return false;
    }

    /**
     * Отменить выполнение отчета.
     *
     * @param $rl_id string Инкрементный идентификатор процесса обработки отчета, который необходимо отменить.
     * @return bool Результат сохранения в БД.
     */
    public static function cancel($rl_id)
    {
        $model = ReportLoader::findOne($rl_id);

        if ($model) {
            $model->load(['ReportLoader' => [
                'rl_status' => ReportLoader::CANCEL,
                'rl_percent' => 0,
            ]]);

            return $model->save(false);
        }

        return false;
    }

    /**
     * Проверяет текущую обработку процесса отчета на активность. Статус процесса должен быть [[\common\widgets\ReportLoader\models\ReportLoader::PROGRESS]]
     *
     * @return bool
     */
    public function isActive()
    {
        $model = ReportLoader::findOne($this->getId());

        if ($model) {
            return $model->rl_status == ReportLoader::PROGRESS;
        }

        return false;
    }

    /**
     * Возвращает имя файла отчета в файловой системе.
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->loader->rl_report_filename;
    }

    /**
     * Возвращает имя отчета. Также это имя используется при скачивании файла отчета, как имя файла.
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->loader->rl_report_displayname;
    }

    /**
     * Возвращает инкрементный идентификатор процесса обработки отчета.
     *
     * @return int
     */
    public function getId()
    {
        return $this->loader->primaryKey;
    }

    /**
     * Остановить процесс обработки отчета, и пометить отчет как выполненный.
     *
     * @return bool
     */
    public function end()
    {
        $model = ReportLoader::find()
            ->andWhere([
                'rl_id' => $this->getId(),
            ])
            ->andWhere(['not', ['rl_status' => ReportLoader::CANCEL]])
            ->one();

        if ($model) {
            $model->load(['ReportLoader' => [
                'rl_status' => ReportLoader::COMPLETE,
                'rl_percent' => 100,
            ]]);

            return $model->save(false);
        }

        return false;
    }
}