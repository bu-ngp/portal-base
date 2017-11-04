<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 04.11.2017
 * Time: 13:51
 */

namespace doh\services\classes;

use doh\services\models\Handler;
use domain\helpers\BinaryHelper;
use wartron\yii2uuid\helpers\Uuid;
use Yii;
use yii\base\BaseObject;
use yii\console\Controller;
use yii\queue\Job;
use yii\web\Session;
use yii\web\User;

abstract class ProcessLoader extends BaseObject implements Job
{
    const CONSOLE = 'CONSOLE';

    public $description = 'Process Loader';

    private $_handler;

    abstract public function body();

    public function __construct($config = [])
    {
        $this->_handler = new Handler([
            'identifier' => $this->getIdentifier(),
            'handler_name' => static::className(),
            'handler_description' => $this->description,
            'handler_at' => time(),
            'handler_percent' => 0,
            'handler_status' => Handler::DURING,
        ]);

        $this->_handler->save(false);

        parent::__construct($config);
    }

    public function execute($queue)
    {
        $this->begin();
        try {
            $this->body();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return;
        }

        $this->end();
    }

    public function addPercentComplete($percent)
    {
        if ($this->isActive()) {
            $this->_handler->handler_percent += $percent;

            if ($this->_handler->handler_percent > 100) {
                $this->_handler->handler_percent = 100;
            }

            $this->_handler->save(false);
        }
    }

    public static function cancel($handler_id)
    {
        $handler = Handler::findOne($handler_id);
        if ($handler && $handler->handler_status === Handler::DURING) {
            $handler->handler_status = Handler::CANCELED;
            $handler->save(false);
        }
    }

    protected function begin()
    {

    }

    protected function end()
    {
        if ($this->isActive()) {
            $this->_handler->handler_status = Handler::FINISHED;
            $this->_handler->handler_percent = 100;
            $this->_handler->handler_done_time = microtime(true) - $this->_handler->handler_at;
            $this->_handler->handler_used_memory = Yii::$app->formatter->asShortSize(memory_get_usage(true));
            $this->_handler->save(false);
        }
    }

    protected function isActive()
    {
        return $this->_handler->handler_status === Handler::DURING;
    }

    protected function error($message)
    {
        $this->_handler->handler_status = Handler::ERROR;
        $this->_handler->handler_short_report = $message;
        $this->_handler->save();
    }

    protected function getIdentifier()
    {
        if (Yii::$app->controller instanceof Controller) {
            return self::CONSOLE;
        }

        /** @var User $user */
        if ($user = Yii::$app->get('user')) {
            /** @var Session $session */
            if ($user->isGuest) {
                if ($session = Yii::$app->get('session')) {
                    return $session->getId();
                }

                throw new \Exception('Need user and session components');
            }

            return BinaryHelper::isBinary($user->getId()) ? Uuid::uuid2str($user->getId()) : $user->getId();
        }

        throw new \Exception('Need user and session components');
    }

}