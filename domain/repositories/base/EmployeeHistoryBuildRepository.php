<?php

namespace domain\repositories\base;

use domain\models\base\EmployeeHistoryBuild;
use Yii;

class EmployeeHistoryBuildRepository
{
    /**
     * @param $id
     * @return EmployeeHistoryBuild
     */
    public function find($id)
    {
        if (!$employeeHistoryBuild = EmployeeHistoryBuild::findOne($id)) {
            throw new \RuntimeException('Model not found.');
        }

        return $employeeHistoryBuild;
    }

    /**
     * @param EmployeeHistoryBuild $employeeHistoryBuild
     */
    public function add($employeeHistoryBuild)
    {
        if (!$employeeHistoryBuild->getIsNewRecord()) {
            throw new \DomainException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if (!$employeeHistoryBuild->insert(false)) {
            throw new \DomainException(Yii::t('domain/base', 'Saving error.'));
        }
    }

    /**
     * @param EmployeeHistoryBuild $employeeHistoryBuild
     */
    public function save($employeeHistoryBuild)
    {
        if ($employeeHistoryBuild->getIsNewRecord()) {
            throw new \DomainException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if ($employeeHistoryBuild->update(false) === false) {
            throw new \DomainException(Yii::t('domain/base', 'Saving error.'));
        }
    }

    /**
     * @param EmployeeHistoryBuild $employeeHistoryBuild
     */
    public function delete($employeeHistoryBuild)
    {
        if (!$employeeHistoryBuild->delete()) {
            throw new \DomainException(Yii::t('domain/base', 'Deleting error.'));
        }
    }
}