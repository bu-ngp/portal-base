<?php

namespace common\widgets\ReportLoader;

use wartron\yii2uuid\helpers\Uuid;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\db\BaseActiveRecord;

/**
 * Поведение определяющее идентификатор пользователя. В случае отсутствия авторизованного пользователя берется идентификатор сессии пользователя.
 *
 * Поведение используется в модели обработчика отчетов [[\common\widgets\ReportLoader\models\ReportLoader]].
 */
class BlameableBehavior extends AttributeBehavior
{

    /**
     * @var string Имя атрибута модели, получающего ИД пользователя или сессии при создании записи.
     */
    public $createdByAttribute = 'created_by';
    /**
     * @var string Имя атрибута модели, получающего ИД пользователя или сессии при обновлении записи.
     */
    public $updatedByAttribute = 'updated_by';

    /**
     * Инициализация поведения
     */
    public function init()
    {
        parent::init();

        if (empty($this->attributes)) {
            $this->attributes = [
                BaseActiveRecord::EVENT_BEFORE_INSERT => [$this->createdByAttribute, $this->updatedByAttribute],
                BaseActiveRecord::EVENT_BEFORE_UPDATE => $this->updatedByAttribute,
            ];
        }
    }

    protected function getValue($event)
    {
        if ($this->value === null) {
            $user = Yii::$app->get('user', false);
            $session = Yii::$app->get('session', false);

            if ($user && $session) {
                return $user->isGuest ? $session->id : Uuid::uuid2str($user->id);
            }

            return null;
        }

        return parent::getValue($event);
    }
}
