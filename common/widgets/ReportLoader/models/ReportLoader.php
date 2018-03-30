<?php

namespace common\widgets\ReportLoader\models;

use common\widgets\ReportLoader\BlameableBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * Класс модели для таблицы БД `{{%report_loader}}`.
 *
 * @property string $rl_id
 * @property string $rl_process_id
 * @property string $rl_report_id
 * @property string $rl_report_filename
 * @property string $rl_report_displayname
 * @property string $rl_report_type
 * @property integer $rl_status
 * @property integer $rl_percent
 * @property integer $rl_start
 * @property string $extension
 */
class ReportLoader extends \yii\db\ActiveRecord
{
    /** Обработка отчета в процессе */
    const PROGRESS = 1;
    /** Обработка отчета выполнена */
    const COMPLETE = 2;
    /** Обработка отчета отменена пользователем */
    const CANCEL = 3;

    /**
     * Возвращает имя таблицы в БД
     *
     * @return string
     */
    public static function tableName()
    {
        return '{{%report_loader}}';
    }

    /**
     * Возвращает правила фильтрации и валидации атрибутов модели.
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['rl_report_filename'], 'default', 'value' => Yii::getAlias('@common') . '/tmpfiles/report' . time() . '_' . rand(1000, 9999) . $this->getExtension()],
            [['rl_report_id', 'rl_report_filename', 'rl_report_displayname', 'rl_report_type'], 'required'],
            [['rl_status', 'rl_percent', 'rl_start'], 'integer'],
            [['rl_report_id'], 'unique', 'targetAttribute' => ['rl_process_id', 'rl_report_id', 'rl_status'], 'message' => Yii::t('wk-widget-report-loader', 'Report with id = "{value}" is formed')],
            [['rl_process_id', 'rl_report_id'], 'string', 'max' => 64],
            [['rl_report_filename', 'rl_report_displayname'], 'string', 'max' => 255],
            [['rl_report_type'], 'string', 'max' => 10],
        ];
    }

    /**
     * Возвращает набор поведений подключенных к модели.
     *
     * ```php
     *     return [
     *             [
     *                 'class' => TimestampBehavior::className(),
     *                 'createdAtAttribute' => 'rl_start',
     *                 'updatedAtAttribute' => false,
     *                 'value' => new Expression('NOW()'),
     *             ],
     *             [
     *                 'class' => BlameableBehavior::className(),
     *                 'createdByAttribute' => 'rl_process_id',
     *                 'updatedByAttribute' => false,
     *                 'attributes' => [
     *                     ActiveRecord::EVENT_BEFORE_VALIDATE => ['rl_process_id'],
     *                 ],
     *             ],
     *         ];
     * ```
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'rl_start',
                'updatedAtAttribute' => false,
                'value' => new Expression('NOW()'),
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'rl_process_id',
                'updatedByAttribute' => false,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_VALIDATE => ['rl_process_id'],
                ],
            ],
        ];
    }

    /**
     * Возвращает расширение файла в зависимости от типа отчета.
     *
     * @return bool|string
     */
    public function getExtension()
    {
        switch ($this->rl_report_type) {
            case 'Excel2007':
                return '.xlsx';
            case 'PDF':
                return '.pdf';
        }

        return false;
    }

}
