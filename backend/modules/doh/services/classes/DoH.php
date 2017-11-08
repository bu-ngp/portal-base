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
use yii\db\Expression;
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

    public static function listen(array $handler_ids)
    {
        if (empty($handler_ids)) {
            return [];
        }

        $handlers = Handler::find()
            ->select(['handler_id', new Expression('round(handler_percent / 100, 2) as handler_percent'), 'handler_status'])
            ->andWhere(['handler_id' => $handler_ids])
            ->asArray()
            ->all();

        return $handlers ? array_map(function ($handler) {
            return array_values($handler);
        }, $handlers) : [];
    }

    public static function cancel($handler_id)
    {
        $handler = Handler::findOne($handler_id);
        if ($handler && in_array($handler->handler_status, [Handler::QUEUE, Handler::DURING])) {
            $handler->handler_status = Handler::CANCELED;
            return $handler->save(false);
        }

        return false;
    }

    public static function delete($handler_id)
    {
        $handler = Handler::findOne($handler_id);

        if (!$handler) {
            return Yii::t('doh', 'Handler "{id}" not found', ['id' => $handler_id]);
        }

        if (in_array($handler->handler_status, [Handler::QUEUE, Handler::DURING])) {
            return Yii::t('doh', 'Handler in queue or is during');
        }

        if ($handler->delete() === false) {
            /** TODO Delete Files */
            return 'Ошибка удаления';
        }

        return '';
    }

    public static function clear()
    {
        /** TODO Delete Files */
        Handler::deleteAll(['not',
            ['handler_status' => [Handler::QUEUE, Handler::DURING]]
        ]);

        return true;
    }
}