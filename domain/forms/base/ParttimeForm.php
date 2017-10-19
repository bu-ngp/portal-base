<?php

namespace domain\forms\base;

use domain\models\base\Parttime;
use domain\rules\base\ParttimeRules;
use Yii;
use yii\base\Model;

class ParttimeForm extends Model
{
    public $person_id;
    public $dolzh_id;
    public $podraz_id;
    public $build_id;
    public $parttime_begin;
    public $parttime_end;
    public $created_at;
    public $updated_at;
    public $created_by;
    public $updated_by;

    public function __construct(Parttime $parttime = null, $config = [])
    {
        if ($parttime) {
            $this->load($parttime->attributes, '');
        } else {
            $this->person_id = Yii::$app->request->get('person');
            $this->parttime_begin = date('Y-m-d');
        }

        parent::__construct($config);
    }

    public function rules()
    {
        return ParttimeRules::client();
    }

    public function attributeLabels()
    {
        return (new Parttime())->attributeLabels();
    }
}