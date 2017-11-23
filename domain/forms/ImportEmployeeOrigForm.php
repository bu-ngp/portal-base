<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 21.11.2017
 * Time: 9:18
 */

namespace domain\forms;

use PHPExcel_Shared_Date;
use yii\base\Model;

class ImportEmployeeOrigForm extends Model
{
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
            [['period', 'dr', 'dateBegin', 'dateEnd'], 'filter', 'filter' => function ($value) {
                return preg_match('/\d{5}(\.\d{9})?/', $value) ? date('d.m.Y', PHPExcel_Shared_Date::ExcelToPHP($value)) : $value;
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