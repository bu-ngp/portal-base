<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 19.10.2017
 * Time: 9:58
 */

namespace domain\forms\base;


use domain\models\base\EmployeeHistoryBuild;
use domain\rules\base\EmployeeHistoryBuildRules;
use domain\validators\Str2UUIDValidator;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class EmployeeBuildForm extends Model
{
    public $employee_history_id;
    public $build_id;
    public $employee_history_build_deactive;

    public function __construct($config = [])
    {
        $this->employee_history_id = Yii::$app->request->get('employee');
        parent::__construct($config);
    }

    public function rules()
    {
        return ArrayHelper::merge(EmployeeHistoryBuildRules::client(), [
            [['!employee_history_id'], 'required'],
            [['build_id'], Str2UUIDValidator::className()],
        ]);
    }

    public function attributeLabels()
    {
        return (new EmployeeHistoryBuild())->attributeLabels();
    }
}