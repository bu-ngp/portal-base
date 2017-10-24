<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 24.10.2017
 * Time: 15:49
 */

namespace domain\validators;


use common\models\base\Person;
use yii\validators\Validator;

class ParttimeValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        if (($person_hired = Person::findOne($model->person_id)->person_hired) > $model->$attribute) {
            $model->addError($attribute, "\"{$model->getAttributeLabel($attribute)}\" не может быть менее даты приема на работу " . \Yii::$app->formatter->asDate($person_hired));
        }

        $person_fired = Person::findOne($model->person_id)->person_fired;
        if ($person_fired && $person_fired < $model->$attribute) {
            $model->addError($attribute, "\"{$model->getAttributeLabel($attribute)}\" не может быть более даты увольнения " . \Yii::$app->formatter->asDate($person_fired));
        }
    }
}