<?php

namespace domain\repositories\base;

use domain\models\base\Employee;
use domain\repositories\RepositoryInterface;
use RuntimeException;
use Yii;

class EmployeeRepository implements RepositoryInterface
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
     * @param Employee $employee
     */
    public function add($employee)
    {
        if (!$employee->getIsNewRecord()) {
            throw new \RuntimeException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if (!$employee->insert(false)) {
            throw new \RuntimeException(Yii::t('domain/base', 'Saving error.'));
        }
    }

    /**
     * @param Employee $employee
     */
    public function save($employee)
    {
        if ($employee->getIsNewRecord()) {
            throw new \RuntimeException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if ($employee->update(false) === false) {
            throw new \RuntimeException(Yii::t('domain/base', 'Saving error.'));
        }
    }

    /**
     * @param Employee $employee
     */
    public function delete($employee)
    {
        if (!$employee->delete()) {
            throw new \RuntimeException(Yii::t('domain/base', 'Deleting error.'));
        }
    }
}