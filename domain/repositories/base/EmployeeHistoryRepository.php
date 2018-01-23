<?php

namespace domain\repositories\base;

use domain\models\base\Employee;
use domain\models\base\EmployeeHistory;
use RuntimeException;
use wartron\yii2uuid\helpers\Uuid;
use Yii;
use yii\db\ActiveRecord;

class EmployeeHistoryRepository
{
    /**
     * @param $id
     * @return EmployeeHistory
     */
    public function find($id)
    {
        if (!$employeeHistory = EmployeeHistory::findOne($id)) {
            throw new \RuntimeException('Model not found.');
        }

        return $employeeHistory;
    }

    /**
     * @param $person_id
     * @param $date
     * @return EmployeeHistory|null|ActiveRecord
     */
    public function findByDate($person_id, $date)
    {
        return EmployeeHistory::findOne(['person_id' => $person_id, 'employee_history_begin' => $date]);
    }

    /**
     * @param EmployeeHistory $employeeHistory
     */
    public function add(EmployeeHistory $employeeHistory)
    {
        if (!$employeeHistory->getIsNewRecord()) {
            throw new \DomainException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if (!$employeeHistory->insert(false)) {
            throw new \DomainException(Yii::t('domain/base', 'Saving error.'));
        }
    }

    /**
     * @param EmployeeHistory $employeeHistory
     */
    public function save(EmployeeHistory $employeeHistory)
    {
        if ($employeeHistory->getIsNewRecord()) {
            throw new \DomainException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if ($employeeHistory->update(false) === false) {
            throw new \DomainException(Yii::t('domain/base', 'Saving error.'));
        }
    }

    /**
     * @param EmployeeHistory $employeeHistory
     */
    public function delete(EmployeeHistory $employeeHistory)
    {
        if (!$employeeHistory->delete()) {
            throw new \DomainException(Yii::t('domain/base', 'Deleting error.'));
        }
    }

    /**
     * @param $employeeId
     * @return null|ActiveRecord|EmployeeHistory
     */
    public function previousBy($employeeId, $currentPersonId)
    {
        return EmployeeHistory::find()
            ->andWhere(['not in', 'employee_history_id', $employeeId])
            ->andWhere(['person_id' => $currentPersonId])
            ->orderBy(['employee_history_begin' => SORT_DESC, 'employee_history_id' => SORT_DESC])
            ->limit(1)
            ->one();
    }

    /**
     * @param $exceptId
     * @return bool
     */
    public function exists($exceptId, $currentPersonId)
    {
        return EmployeeHistory::find()
            ->andWhere(['not in', 'employee_history_id', $exceptId])
            ->andWhere(['person_id' => $currentPersonId])
            ->exists();
    }

    /**
     * @param $person_id
     * @return bool|ActiveRecord|EmployeeHistory
     */
    public function findCurrentByPerson($person_id)
    {
        $employee = Employee::findOne(['person_id' => $person_id]);
        if ($employee) {
            $employeeHistory = EmployeeHistory::find()
                ->andWhere([
                    'person_id' => $employee->person_id,
                    'dolzh_id' => $employee->dolzh_id,
                    'podraz_id' => $employee->podraz_id,
                    'employee_history_begin' => $employee->employee_begin,
                ])
                ->one();

            return $employeeHistory ?: false;
        }

        return false;
    }
}