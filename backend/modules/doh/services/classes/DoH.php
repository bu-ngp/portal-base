<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 04.11.2017
 * Time: 14:03
 */

namespace doh\services\classes;


use console\helpers\RbacHelper;
use doh\services\models\DohFiles;
use doh\services\models\Handler;
use domain\helpers\BinaryHelper;
use wartron\yii2uuid\helpers\Uuid;
use Yii;
use yii\console\Controller;
use yii\db\Expression;
use yii\helpers\StringHelper;
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
            'identifier' => self::getIdentifier(),
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

    public static function getIdentifier()
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

    public static function getCurrentIdentifierCondition() {
       return array_filter([self::getIdentifier(), Yii::$app->user->can(RbacHelper::ADMINISTRATOR) ? self::CONSOLE : null]);
    }

    public static function listen(array $handler_ids)
    {
        if (empty($handler_ids)) {
            return [];
        }

        $handlers = Handler::find()
            ->select(['handler_id', new Expression('round(handler_percent / 100, 2) as handler_percent'), 'handler_status'])
            ->andWhere([
                'handler_id' => $handler_ids,
                'identifier' => self::getCurrentIdentifierCondition(),
            ])
            ->asArray()
            ->all();

        return $handlers ? array_map(function ($handler) {
            return array_values($handler);
        }, $handlers) : [];
    }

    public static function cancel($handler_id)
    {
        $handler = Handler::findOne([
            'handler_id' => $handler_id,
            'identifier' => self::getCurrentIdentifierCondition(),
        ]);

        if (!$handler) {
            return Yii::t('doh', 'Handler "{id}" not found', ['id' => $handler_id]);
        }

        if (!in_array($handler->handler_status, [Handler::QUEUE, Handler::DURING])) {
            return Yii::t('doh', 'Handler not in queue or is not during');
        }

        $handler->handler_status = Handler::CANCELED;

        if (!$handler->save()) {
            return Yii::t('doh', 'Cancel error');
        }

        return '';
    }

    public static function delete($handler_id)
    {
        $handler = Handler::findOne([
            'handler_id' => $handler_id,
            'identifier' => self::getCurrentIdentifierCondition(),
        ]);

        if (!$handler) {
            return Yii::t('doh', 'Handler "{id}" not found', ['id' => $handler_id]);
        }

        if (in_array($handler->handler_status, [Handler::QUEUE, Handler::DURING])) {
            return Yii::t('doh', 'Handler in queue or is during');
        }

        if ($errors = self::deleteFiles($handler_id)) {
            return $errors;
        }

        if ($handler->delete() === false) {
            return Yii::t('doh', 'Delete error');
        }

        return '';
    }

    public static function clear()
    {
        $handler_ids = Handler::find()
            ->andWhere(['identifier' => self::getCurrentIdentifierCondition()])
            ->andWhere(['not', ['handler_status' => [Handler::QUEUE, Handler::DURING]]])
            ->column();

        $errors = self::deleteFiles($handler_ids);
        Handler::deleteAll(['not',
            ['handler_status' => [Handler::QUEUE, Handler::DURING]]
        ]);

        return $errors;
    }

    protected static function deleteFiles($handler_ids)
    {
        if (!is_array($handler_ids)) {
            $handler_ids = [$handler_ids];
        }

        $errors = '';
        /** @var DohFiles[] $dohFiles */
        $dohFiles = DohFiles::find()->joinWith('handlers')->andWhere(['handlers.handler_id' => $handler_ids])->all();

        if ($dohFiles) {
            foreach ($dohFiles as $docFile) {
                $path = DIRECTORY_SEPARATOR === '/' ? $docFile->file_path : mb_convert_encoding($docFile->file_path, 'Windows-1251', 'UTF-8');

                if (file_exists($path)) {
                    unlink($path);
                } else {
                    $errors .= (empty($errors) ? '' : '<br>') . "Ошибка удаления файла: '$docFile->file_path'. Файл не существует.";
                }
            }

            DohFiles::deleteAll(['doh_files_id' => $dohFiles]);
        }

        return StringHelper::truncateWords($errors, 500, '...', true);
    }
}