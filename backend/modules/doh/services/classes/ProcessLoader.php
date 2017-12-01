<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 04.11.2017
 * Time: 13:51
 */

namespace doh\services\classes;

use doh\events\ProccessErrorEvent;
use doh\services\models\DohFiles;
use doh\services\models\Handler;
use doh\services\models\HandlerFiles;
use Yii;
use yii\base\Component;
use yii\queue\JobInterface;

abstract class ProcessLoader extends Component implements JobInterface
{
    const EVENT_PROCCESS_ERROR = 'processError';

    public $description = 'Process Loader';
    public $handler_id;

    /** @var  Handler */
    private $_handler;

    abstract public function body();

    public function execute($queue)
    {
        $this->_handler = Handler::findOne($this->handler_id);
        if (!$this->_handler || $this->_handler->handler_status !== Handler::QUEUE) {
            return;
        }
        $this->begin();
        try {
            $this->body();
        } catch (\Exception $e) {
            if ($e instanceof CancelException) {
                $this->cancel();
                return;
            }
            $this->error($e->getMessage());
            $this->trigger(self::EVENT_PROCCESS_ERROR, Yii::createObject(['class' => ProccessErrorEvent::className(), 'exception' => $e]));
            return;
        }

        $this->end();
    }

    public function addPercentComplete($percent)
    {
        if ($this->isActive()) {
            if ($this->_handler->handler_percent < $percent) {
                $this->_handler->handler_percent = $percent;

                if ($this->_handler->handler_percent > 100) {
                    $this->_handler->handler_percent = 100;
                }

                $this->_handler->save(false);
            }
        } elseif ($this->isCanceled()) {
            throw new CancelException;
        }
    }

    public function addShortReport($reportString)
    {
        if ($this->isActive()) {
            $this->_handler->handler_short_report = $reportString;
            if (!$this->_handler->save()) {
                $this->_handler->handler_short_report = print_r($this->_handler->getErrors(), true);
                $this->_handler->save();
            };
        }
    }

    public function addFile($path, $description = '', $type = '')
    {
        if (file_exists($path)) {
            $this->_handler->dohFiles = array_merge(HandlerFiles::find()->select('doh_files_id')->andWhere(['handler_id' => $this->_handler->primaryKey])->column(), [
                [
                    'file_type' => $this->getFileType($path, $type),
                    'file_path' => $path,
                    'file_description' => $this->getFileDescription($path, $description),
                ]
            ]);

            if (!$this->_handler->save()) {
                $this->_handler->handler_short_report = "File '$path': " . print_r($this->_handler->getErrors(), true);
                $this->_handler->save();
            }
        } else {
            $this->_handler->handler_short_report = "File '$path' not exist";
            $this->_handler->save();
        }
    }

    protected function begin()
    {
        if ($this->_handler->handler_status === Handler::QUEUE) {
            $this->_handler->handler_status = Handler::DURING;
            $this->_handler->save(false);
        }
    }

    protected function end()
    {
        if ($this->isActive()) {
            $this->_handler->handler_status = Handler::FINISHED;
            $this->_handler->handler_percent = 100;
            $this->_handler->handler_done_time = microtime(true) - $this->_handler->handler_at;
            $this->_handler->handler_used_memory = memory_get_usage(true);
            $this->_handler->save(false);
        }
    }

    protected function isActive()
    {
        return Handler::findOne($this->_handler->primaryKey)->handler_status === Handler::DURING;
    }

    protected function isCanceled()
    {
        return Handler::findOne($this->_handler->primaryKey)->handler_status === Handler::CANCELED;
    }

    protected function cancel()
    {
        $this->_handler->handler_done_time = microtime(true) - $this->_handler->handler_at;
        $this->_handler->handler_used_memory = memory_get_usage(true);
        $this->_handler->save(false);
    }

    protected function error($message)
    {
        if (!mb_check_encoding($message, 'UTF-8')) {
            $message = mb_convert_encoding($message, 'UTF-8', 'Windows-1251');
        }

        $this->_handler->handler_status = Handler::ERROR;
        $this->_handler->handler_short_report = $message;
        $this->_handler->handler_done_time = microtime(true) - $this->_handler->handler_at;
        $this->_handler->handler_used_memory = memory_get_usage(true);
        $this->_handler->save();
    }

    protected function getFileDescription($path, $description)
    {
        if (is_string($description) && !empty($description)) {
            return $description;
        }

        if (preg_match('/[\\\\\/]?((?:.(?![\\\\/]))+$)/', $path, $matches)) {
            return $matches[1] ?: 'file';
        }

        return 'file';
    }

    protected function getFileType($path, $type)
    {
        if (in_array($type, [DohFiles::FILE_PDF, DohFiles::FILE_EXCEL, DohFiles::FILE_DOC, DohFiles::FILE_CSV, DohFiles::FILE_TXT])) {
            return $type;
        }

        if (preg_match('/\.?((?:.(?!\.))+$)/', $path, $matches)) {
            $extension = $matches[1];

            switch ($extension) {
                case 'pdf':
                    return DohFiles::FILE_PDF;
                    break;
                case 'xls':
                case 'xlsx':
                    return DohFiles::FILE_EXCEL;
                    break;
                case 'doc':
                case 'docx':
                    return DohFiles::FILE_DOC;
                    break;
                case 'csv':
                    return DohFiles::FILE_CSV;
                    break;
                case 'txt':
                    return DohFiles::FILE_TXT;
                    break;
                case 'zip':
                    return DohFiles::FILE_ZIP;
                    break;
                case 'xml':
                    return DohFiles::FILE_XML;
                    break;
                default:
                    return DohFiles::FILE_UNKNOWN;
            }
        }

        return DohFiles::FILE_UNKNOWN;
    }
}