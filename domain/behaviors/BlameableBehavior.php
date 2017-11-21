<?php

namespace domain\behaviors;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\console\Application;
use yii\db\BaseActiveRecord;
use yii\web\User;

class BlameableBehavior extends AttributeBehavior
{
    /**
     * @var string the attribute that will receive current user ID value
     * Set this property to false if you do not want to record the creator ID.
     */
    public $createdByAttribute = 'created_by';
    /**
     * @var string the attribute that will receive current user ID value
     * Set this property to false if you do not want to record the updater ID.
     */
    public $updatedByAttribute = 'updated_by';
    /**
     * @inheritdoc
     *
     * In case, when the property is `null`, the value of `Yii::$app->user->id` will be used as the value.
     */
    public $value;


    /**
     * @inheritdoc
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

    /**
     * @inheritdoc
     *
     * In case, when the [[value]] property is `null`, the value of `Yii::$app->user->id` will be used as the value.
     */
    protected function getValue($event)
    {
        if ($this->value === null) {
            if (Yii::$app instanceof Application) {
                return 'CONSOLE';
            }

            /** @var User $user */
            $user = Yii::$app->get('user', false);

            if ($user) {
                if ($user->isGuest) {
                    return Yii::t('classes/behaviors', 'guest');
                }

                return ($user->identity->isLocal() ? 'local/' : 'ldap/') . $user->identity->person_username;
            }

            return null;
        }

        return parent::getValue($event);
    }
}
