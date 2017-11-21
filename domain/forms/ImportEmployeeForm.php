<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 21.11.2017
 * Time: 9:18
 */

namespace domain\forms;


use domain\models\base\Profile;
use ngp\services\validators\DRValidator;
use yii\base\Model;

class ImportEmployeeForm extends Model
{
    const MALE = 'М';
    const FEMALE = 'Ж';

    public $period;
    public $fio;
    public $dr;
    public $pol;
    public $snils;
    public $inn;
    public $dolzh;
    public $status;
    public $podraz;
    public $dateBegin;
    public $dateEnd;
    public $address;

    public function rules()
    {
        return [
            [['dr', 'dateBegin', 'dateEnd'], DRValidator::className()],
            [['pol'], 'filter', 'filter' => function ($value) {
                switch ($value) {
                    case self::MALE:
                        return Profile::MALE;
                    case self::FEMALE:
                        return Profile::FEMALE;
                    default:
                        return $value;
                }
            }],
            [['period', 'fio', 'dr', 'pol', 'snils', 'inn', 'dolzh', 'status', 'podraz', 'dateBegin', 'dateEnd', 'address'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'period' => 'Период',
            'fio' => 'Сотрудник',
            'dr' => 'ДатаРождения',
            'pol' => 'Пол',
            'snils' => 'СНИЛС',
            'inn' => 'ИНН',
            'dolzh' => 'Должность',
            'status' => 'СтатусРаботы',
            'podraz' => 'Подразделение',
            'dateBegin' => 'ДатаПриема',
            'dateEnd' => 'ДатаУвольнения',
            'address' => 'АдресМестаЖительства',
        ];
    }
}