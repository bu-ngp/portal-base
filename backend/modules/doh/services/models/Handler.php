<?php

namespace doh\services\models;

use common\widgets\GridView\services\GWItemsTrait;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use Yii;

/**
 * This is the model class for table "{{%handler}}".
 *
 * @property integer $handler_id
 * @property string $identifier
 * @property string $handler_name
 * @property string $handler_description
 * @property integer $handler_at
 * @property string $handler_percent
 * @property integer $handler_status
 * @property integer $handler_done_time
 * @property string $handler_used_memory
 * @property string $handler_short_report
 * @property string $handler_files
 *
 * @property HandlerFiles[] $handlerFiles
 * @property DohFiles[] $dohFiles
 */
class Handler extends \yii\db\ActiveRecord
{
    use GWItemsTrait;

    const QUEUE = 1;
    const DURING = 2;
    const FINISHED = 3;
    const CANCELED = 4;
    const ERROR = 5;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%handler}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['identifier', 'handler_name', 'handler_description', 'handler_at'], 'required'],
            [['handler_at', 'handler_percent', 'handler_status', 'handler_files', 'handler_used_memory'], 'integer'],
            [['identifier'], 'filter', 'filter' => function ($value) {
                return mb_substr($value, 0, 64, 'UTF-8');
            }],
            [['handler_name'], 'filter', 'filter' => function ($value) {
                return mb_substr($value, 0, 255, 'UTF-8');
            }],
            [['handler_description', 'handler_short_report'], 'filter', 'filter' => function ($value) {
                return mb_substr($value, 0, 400, 'UTF-8');
            }],
            [['identifier'], 'string', 'max' => 64],
            [['handler_name'], 'string', 'max' => 255],
            [['handler_description', 'handler_short_report'], 'string', 'max' => 400],
            [['handler_done_time'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'handler_id' => Yii::t('doh', 'Handler ID'),
            'identifier' => Yii::t('doh', 'Identifier'),
            'handler_name' => Yii::t('doh', 'Handler Name'),
            'handler_description' => Yii::t('doh', 'Handler Description'),
            'handler_at' => Yii::t('doh', 'Handler At'),
            'handler_percent' => Yii::t('doh', 'Handler Percent'),
            'handler_status' => Yii::t('doh', 'Handler Status'),
            'handler_done_time' => Yii::t('doh', 'Handler Done Time'),
            'handler_used_memory' => Yii::t('doh', 'Handler Used Memory'),
            'handler_short_report' => Yii::t('doh', 'Handler Short Report'),
            'handler_files' => Yii::t('doh', 'Handler Files'),
            'dohFilesList' => Yii::t('doh', 'Handler Files'),
        ];
    }

    public function behaviors()
    {
        return [
            'saveRelations' => [
                'class' => SaveRelationsBehavior::className(),
                'relations' => ['dohFiles'],
            ],
        ];
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHandlerFiles()
    {
        return $this->hasMany(HandlerFiles::className(), ['handler_id' => 'handler_id'])->from(['handlerFiles' => HandlerFiles::tableName()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDohFiles()
    {
        return $this->hasMany(DohFiles::className(), ['doh_files_id' => 'doh_files_id'])->from(['dohFiles' => DohFiles::tableName()])->viaTable('{{%handler_files}}', ['handler_id' => 'handler_id']);
    }

    public function getDohFilesList()
    {
        return $this->dohFiles ? '<ul>' . implode("", array_map(function ($dohFiles) {
                return $this->getLink($dohFiles);
            }, $this->dohFiles)) . '</ul>' : '';
    }

    public function labelStatus($handler_status)
    {
        switch ($handler_status) {
            case self::QUEUE:
                return 'info';
                break;
            case self::DURING:
                return 'primary';
                break;
            case self::FINISHED:
                return 'success';
                break;
            case self::CANCELED:
                return 'warning';
                break;
            case self::ERROR:
                return 'danger';
                break;
            default:
                return 'default';
        }
    }

    protected function getLink(DohFiles $dohFiles)
    {
        $path = DIRECTORY_SEPARATOR === '/' ? $dohFiles->file_path : mb_convert_encoding($dohFiles->file_path, 'Windows-1251', 'UTF-8');

        if (file_exists($path)) {
            return '<li><i class="fa fa-' . DohFiles::faFileType($dohFiles->file_type) . '"></i><a data-pjax="0" href="' . Yii::$app->get('urlManagerAdmin')->createUrl(['doh/download', 'id' => $dohFiles->primaryKey]) . '">&nbsp' . $dohFiles->file_description . '</a></li>';
        } else {
            return '<li class="wk-doh-file-missed"><i class="fa fa-' . DohFiles::faFileType($dohFiles->file_type) . '"></i>' . $dohFiles->file_description . '</li>';
        }
    }

    public static function items()
    {
        return [
            'handler_status' => [
                self::QUEUE => 'В очереди',
                self::DURING => 'В процессе',
                self::FINISHED => 'Закончен',
                self::CANCELED => 'Отменен',
                self::ERROR => 'Ошибка',
            ],
        ];
    }
}
