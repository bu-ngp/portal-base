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
use yii\base\Model;
use yii\helpers\ArrayHelper;

class EmployeeBuildUpdateForm extends Model
{
    private $employee_history_id;
    public $build_id;
    public $employee_history_build_deactive;

    public function __construct(EmployeeHistoryBuild $employeeHB, $config = [])
    {
        $this->employee_history_id = $employeeHB->employee_history_id;
        $this->build_id = $employeeHB->build_id;
        $this->employee_history_build_deactive = $employeeHB->employee_history_build_deactive;

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

    public function getEmployee_history_id()
    {
        return $this->employee_history_id;
    }
}