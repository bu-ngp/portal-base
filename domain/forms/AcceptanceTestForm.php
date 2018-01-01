<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 01.01.2018
 * Time: 16:59
 */

namespace domain\forms;


use yii\base\Model;

class AcceptanceTestForm extends Model
{
    public $dolzh_single_id;
    public $dolzh_multiple_id;
    public $podraz_single_id;
    public $podraz_multiple_id;

    public function rules()
    {
        return [
            [[
                'dolzh_single_id',
                'dolzh_multiple_id',
                'podraz_single_id',
                'podraz_multiple_id',
            ], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'dolzh_single_id' => 'Select2 Single With Ajax',
            'dolzh_multiple_id' => 'Select2 Multiple With Ajax',
            'podraz_single_id' => 'Select2 Single Without Ajax',
            'podraz_multiple_id' => 'Select2 Multiple Without Ajax',
        ];
    }
}