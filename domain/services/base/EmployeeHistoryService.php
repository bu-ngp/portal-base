<?php

namespace domain\services\base;

use domain\forms\base\EmployeeHistoryForm;
use domain\models\base\Employee;
use domain\models\base\EmployeeHistory;
use domain\models\base\EmployeeHistoryBuild;
use domain\repositories\base\EmployeeHistoryBuildRepository;
use domain\repositories\base\EmployeeHistoryRepository;
use domain\repositories\base\EmployeeRepository;
use domain\services\TransactionManager;
use domain\services\WKService;
use Yii;

class EmployeeHistoryService extends WKService
{
    private $transactionManager;
    private $employeeHistoryRepository;
    private $employeeRepository;
    private $employeeHistoryBuildRepository;

    public function __construct(
        TransactionManager $transactionManager,
        EmployeeHistoryRepository $employeeHistoryRepository,
        EmployeeRepository $employeeRepository,
        EmployeeHistoryBuildRepository $employeeHistoryBuildRepository
    )
    {
        $this->transactionManager = $transactionManager;
        $this->employeeHistoryRepository = $employeeHistoryRepository;
        $this->employeeRepository = $employeeRepository;
        $this->employeeHistoryBuildRepository = $employeeHistoryBuildRepository;
    }

    public function get($id)
    {
        return $this->employeeHistoryRepository->find($id);
    }

    public function create(EmployeeHistoryForm $form)
    {
        $this->guardPersonExists($form);
//        $assignedKeysBuilds = $this->guardAssignBuilds($form);

        $employeeHistory = EmployeeHistory::create($form);
        if (!$this->validateModels($employeeHistory, $form)) {
            throw new \DomainException();
        }

        /** @var Employee $employee */
        if ($employee = Employee::find()->andWhere(['person_id' => $employeeHistory->person_id])->one()) {
            $employee->edit($form);
        } else {
            $employee = Employee::create($form);
        }

//        $employeeHistoryBuild = EmployeeHistoryBuild::create($employeeHistory, $assignedKeysBuilds);

        $this->transactionManager->execute(function () use ($employeeHistory, $employee/*, $employeeHistoryBuild*/) {
            $this->employeeHistoryRepository->add($employeeHistory);

            if ($employee->isNewRecord) {
                $this->employeeRepository->add($employee);
            } else {
                $this->employeeRepository->save($employee);
            }

            return $employeeHistory->primaryKey;

//            foreach ($employeeHistoryBuild as $item) {
//                $this->employeeHistoryBuildRepository->add($item);
//            }
        });
    }

    public function update($id, EmployeeHistoryForm $form)
    {
        $employee = $this->employeeHistoryRepository->find($id);

        $employee->edit($form);

        if (!$this->validateModels($employee, $form)) {
            throw new \DomainException();
        }

        $this->employeeHistoryRepository->save($employee);
    }

    public function delete($id)
    {
        $employeeHistory = $this->employeeHistoryRepository->find($id);
        $this->employeeHistoryRepository->delete($employeeHistory);
    }

    protected function guardPersonExists(EmployeeHistoryForm $form)
    {
        if (!$form->person_id) {
            throw new \DomainException(Yii::t('domain/employee', 'URL parameter "person" is missed.'));
        }
    }

    private function guardAssignBuilds(EmployeeHistoryForm $form)
    {
        if (!is_string($form->assignBuilds) || ($assignedKeys = json_decode($form->assignBuilds)) === null) {
            throw new \DomainException(\Yii::t('domain/employee', 'Error when recognizing selected items'));
        }

        return $assignedKeys;
    }
}