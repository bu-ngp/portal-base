<?php

namespace domain\repositories\base;

use domain\models\base\Employee;
use RuntimeException;
use Yii;
use yii\db\ActiveRecord;

class EmployeeRepository
{
    /**
     * @param $id
     * @return Employee
     */
    public function find($id)
    {
        if (!$employee = Employee::findOne($id)) {
            throw new RuntimeException('Model not found.');
        }

        return $employee;
    }

    /**
     * @param $personUUID
     * @return null|ActiveRecord|Employee
     */
    public function findByPerson($personUUID)
    {
        return Employee::find()->andWhere(['person_id' => $personUUID])->one();
    }

    public function has($id)
    {
        return !!Employee::findOne($id);
    }

    /**
     * @param Employee $employee
     */
    public function add(Employee $employee)
    {
        if (!$employee->getIsNewRecord()) {
            throw new \DomainException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if (!$employee->insert(false)) {
            throw new \DomainException(Yii::t('domain/base', 'Saving error.'));
        }
    }

    /**
     * @param Employee $employee
     */
    public function save(Employee $employee)
    {
        if ($employee->getIsNewRecord()) {
            throw new \DomainException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if ($employee->update(false) === false) {
            throw new \DomainException(Yii::t('domain/base', 'Saving error.'));
        }
    }

    /**
     * @param Employee $employee
     */
    public function delete(Employee $employee)
    {
        if (!$employee->delete()) {
            throw new \DomainException(Yii::t('domain/base', 'Deleting error.'));
        }
    }
}