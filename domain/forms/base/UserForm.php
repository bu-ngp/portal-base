<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 01.10.2017
 * Time: 9:06
 */

namespace domain\forms\base;

use common\classes\validators\WKDateValidator;
use common\models\base\Person;
use domain\models\base\Profile;
use Yii;
use yii\base\Model;

class UserForm extends Model
{
    public $person_fullname;
    public $person_username;
    public $person_password;
    public $person_password_repeat;
    public $person_email;
    public $person_fired;

    public $assignEmployees;
    public $assignRoles;
//
//    public function rules()
//    {
//        return array_merge((new Person())->rules(), (new Profile())->rules(), [
//            [['person_password_repeat'], 'compare', 'compareAttribute' => 'person_password']
//        ]);
//    }

    public function rules()
    {
        return [
            [['person_fullname', 'person_username'], 'required'],
            [['person_fired'], WKDateValidator::className()],
            [['person_username'], 'match', 'pattern' => '/^\D([0-9a-z_-]+)?$/i', 'message' => Yii::t('domain/user', 'Need only latin symbols or digits or "-", "_". First character can\'t digit.')],
            [['person_username', 'person_fullname'], 'string', 'min' => 3],
            [['person_fullname', 'person_username', 'person_email'], 'string', 'max' => 255],
            [['person_email'], 'email'],
        ];
    }

    public function attributeLabels()
    {
        return array_merge((new Person())->attributeLabels(), (new Profile())->attributeLabels());
    }
}