<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 01.10.2017
 * Time: 9:06
 */

namespace domain\forms\base;

use domain\models\base\Person;
use domain\models\base\Profile;
use domain\rules\base\UserRules;
use domain\validators\FIOValidator;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

class UserForm extends Model
{
    public $person_fullname;
    public $person_username;
    public $person_password;
    public $person_password_repeat;
    public $person_email;

    public $assignRoles;

    public function rules()
    {
        return ArrayHelper::merge(UserRules::client(), [
            [['person_fullname'], FIOValidator::className()],
            [['person_password', 'person_password_repeat'], 'required'],
            [['person_password'], 'string', 'min' => 6],
            [['person_password_repeat'], 'compare', 'compareAttribute' => 'person_password'],
            [['assignRoles'], 'safe'],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge((new Person())->attributeLabels(), (new Profile())->attributeLabels(), [
            'person_password' => Yii::t('domain/person', 'Person Password'),
            'person_password_repeat' => Yii::t('domain/person', 'Person Password Repeat'),
        ]);
    }

    public static function generateUserName($fullname)
    {
        preg_match('/^(\b[а-яё-]+\b)\s([а-яё])(.*\s([а-яё]))?/ui', $fullname, $matches);

        if ($matches[1] && $matches[2]) {
            $i = 2;
            $login = Inflector::transliterate($matches[1] . $matches[2] . $matches[4]);

            while (Person::findOne(['person_username' => $login])) {
                $login = Inflector::transliterate($matches[1] . $matches[2] . $matches[4] . $i);
                $i++;
            }

            return $login;
        }

        return 'user' . rand(100000, 999999);
    }
}