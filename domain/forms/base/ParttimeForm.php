<?php

namespace domain\forms\base;

use domain\models\base\Parttime;
use domain\rules\base\ParttimeRules;
use domain\validators\Str2UUIDValidator;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\web\Request;

class ParttimeForm extends Model
{
    public $person_id;
    public $dolzh_id;
    public $podraz_id;
    public $parttime_begin;
    public $parttime_end;

    public $assignBuilds;

    public function __construct($config = [])
    {
        if (Yii::$app->request instanceof Request) {
            $this->person_id = Yii::$app->request->get('person');
        }
        $this->parttime_begin = date('Y-m-d');

        parent::__construct($config);
    }

    public function rules()
    {
        return ArrayHelper::merge(ParttimeRules::client(), [
            [['!person_id'], 'required'],
            [['!person_id', 'dolzh_id', 'podraz_id'], Str2UUIDValidator::className()],
            [['assignBuilds'], 'safe'],
        ]);
    }

    public function attributeLabels()
    {
        return (new Parttime())->attributeLabels();
    }
}