<?php

namespace domain\repositories\base;

use domain\models\base\EmployeeHistory;
use domain\repositories\RepositoryInterface;
use RuntimeException;
use Yii;

class EmployeeHistoryRepository implements RepositoryInterface
{
    /**
     * @param $id
     * @return EmployeeHistory
     */
    public function find($id)
    {
        if (!$employeeHistory = EmployeeHistory::findOne($id)) {
            throw new RuntimeException('Model not found.');
        }

        return $employeeHistory;
    }

    /**
     * @param EmployeeHistory $employeeHistory
     */
    public function add($employeeHistory)
    {
        if (!$employeeHistory->getIsNewRecord()) {
            throw new \RuntimeException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if (!$employeeHistory->insert(false)) {
            throw new \RuntimeException(Yii::t('domain/base', 'Saving error.'));
        }
    }

    /**
     * @param EmployeeHistory $employeeHistory
     */
    public function save($employeeHistory)
    {
        if ($employeeHistory->getIsNewRecord()) {
            throw new \RuntimeException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if ($employeeHistory->update(false) === false) {
            throw new \RuntimeException(Yii::t('domain/base', 'Saving error.'));
        }
    }

    /**
     * @param EmployeeHistory $employeeHistory
     */
    public function delete($employeeHistory)
    {
        if (!$employeeHistory->delete()) {
            throw new \RuntimeException(Yii::t('domain/base', 'Deleting error.'));
        }
    }
}