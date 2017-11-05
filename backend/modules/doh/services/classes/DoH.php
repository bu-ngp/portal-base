<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 04.11.2017
 * Time: 14:03
 */

namespace doh\services\classes;


use doh\services\models\Handler;
use domain\helpers\BinaryHelper;
use wartron\yii2uuid\helpers\Uuid;
use Yii;
use yii\console\Controller;
use yii\web\Session;
use yii\web\User;

class DoH
{
    const CONSOLE = 'CONSOLE';

    /**
     * @var ProcessLoader
     */
    private $_loader;
    private $_handler_id;

    public function __construct(ProcessLoader $loader)
    {

        $this->_loader = $loader;
        $handler = new Handler([
            'identifier' => $this->getIdentifier(),
            'handler_name' => $loader::className(),
            'handler_description' => $loader->description,
            'handler_at' => time(),
            'handler_percent' => 0,
            'handler_status' => Handler::QUEUE,
        ]);
        $handler->save(false);

        $this->_handler_id = $handler->primaryKey;
        $this->_loader->handler_id = $handler->primaryKey;
    }

    public function execute()
    {
        Yii::$app->queue->push($this->_loader);
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

    public static function cancel($handler_id)
    {
        $handler = Handler::findOne($handler_id);
        if ($handler && $handler->handler_status === Handler::DURING) {
            $handler->handler_status = Handler::CANCELED;
            return $handler->save(false);
        }

        return false;
    }
}