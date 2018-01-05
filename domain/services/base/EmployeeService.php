<?php

namespace domain\services\base;

use domain\forms\base\EmployeeForm;
use domain\forms\base\EmployeeHistoryForm;
use domain\models\base\Employee;
use domain\models\base\EmployeeHistory;
use domain\repositories\base\EmployeeHistoryRepository;
use domain\repositories\base\EmployeeRepository;
use domain\services\Service;
use Yii;

class EmployeeService extends Service
{
    private $employeeRepository;

    public function __construct(
        EmployeeRepository $employeeRepository
    )
    {
        $this->employeeRepository = $employeeRepository;
    }

    public function get($id)
    {
        return $this->employeeRepository->find($id);
    }

    public function create(EmployeeHistoryForm $form)
    {
        $this->guardPersonExists($form);

        $employee = EmployeeHistory::create($form);
        if (!$this->validateModels($employee, $form)) {
            throw new \DomainException();
        }

        $this->employeeRepository->add($employee);

        return $employee->primaryKey;
    }

    public function update($id, EmployeeHistoryForm $form)
    {
        $employee = $this->employeeRepository->find($id);

        $employee->edit($form);

        if (!$this->validateModels($employee, $form)) {
            throw new \DomainException();
        }

        $this->employeeRepository->save($employee);
    }

    public function delete($id)
    {
        $employee = $this->employeeRepository->find($id);
        $this->employeeRepository->delete($employee);
    }

    protected function guardPersonExists(EmployeeHistoryForm $form)
    {
        if (!$form->person_id) {
            throw new \DomainException(Yii::t('domain/employee', 'URL parameter "person" is missed.'));
        }
    }
}