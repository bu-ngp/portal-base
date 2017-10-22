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
use Yii;
use yii\base\Model;

class EmployeeBuildForm extends Model
{
    public $employee_history_id;
    public $build_id;
    public $employee_history_build_deactive;

    public function __construct(EmployeeHistoryBuild $employeeHB = null, $config = [])
    {
        if ($employeeHB) {
            $this->employee_history_id = $employeeHB->employee_history_id;
            $this->build_id = $employeeHB->build_id;
            $this->employee_history_build_deactive = $employeeHB->employee_history_build_deactive;
        } else {
            $this->employee_history_id = Yii::$app->request->get('employee');
        }

        parent::__construct($config);
    }

    public function rules()
    {
        return EmployeeHistoryBuildRules::client();
    }
}