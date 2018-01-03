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
    protected $person_fullname;
    protected $person_username;

    public $person_password;
    public $person_password_repeat;

    public function __construct(Person $person, $config = [])
    {
        $this->person_fullname = $person->person_fullname;
        $this->person_username = $person->person_username;

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

    public function getPerson_fullname()
    {
        return $this->person_fullname;
    }

    public function getPerson_username()
    {
        return $this->person_username;
    }
}