<?php

namespace domain\forms\base;

use domain\models\base\Parttime;
use domain\rules\base\ParttimeRules;
use domain\validators\Str2UUIDValidator;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class ParttimeUpdateForm extends Model
{
    private $person_id;
    public $dolzh_id;
    public $podraz_id;
    public $parttime_begin;
    public $parttime_end;

    public function __construct(Parttime $parttime, $config = [])
    {
        $this->person_id = $parttime->person_id;
        $this->dolzh_id = $parttime->dolzh_id;
        $this->podraz_id = $parttime->podraz_id;
        $this->parttime_begin = $parttime->parttime_begin;
        $this->parttime_end = $parttime->parttime_end;

        parent::__construct($config);
    }

    public function rules()
    {
        return ArrayHelper::merge(ParttimeRules::client(), [
            [['!person_id'], 'required'],
            [['!person_id', 'dolzh_id', 'podraz_id'], Str2UUIDValidator::className()],
        ]);
    }

    public function attributeLabels()
    {
        return (new Parttime())->attributeLabels();
    }

    public function getPerson_id()
    {
        return $this->person_id;
    }
}