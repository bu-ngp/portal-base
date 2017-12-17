<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 17.12.2017
 * Time: 12:29
 */

namespace domain\forms\base;


use domain\models\base\Person;
use Yii;
use yii\base\Model;

class ChangeUserPasswordForm extends Model
{
    public $person_username;
    public $person_fullname;

    public $person_password;
    public $person_password_repeat;

    public function __construct(Person $person, $config = [])
    {
        $this->person_username = $person->person_username;
        $this->person_fullname = $person->person_fullname;

        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['person_password', 'person_password_repeat'], 'required'],
            [['person_password'], 'string', 'min' => 6],
            [['person_password_repeat'], 'compare', 'compareAttribute' => 'person_password'],
        ];
    }

    public function attributeLabels()
    {
        return
            array_merge((new Person())->attributeLabels(), [
                'person_password' => Yii::t('domain/person', 'Person Password'),
                'person_password_repeat' => Yii::t('domain/person', 'Person Password Repeat'),
            ]);
    }
}