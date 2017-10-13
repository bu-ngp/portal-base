<?php

namespace domain\services\base;

use domain\models\base\Employee;
use domain\repositories\base\EmployeeRepository;
use domain\services\WKService;

class EmployeeService extends WKService
{
    private $employeeRepository;

    public function __construct(
        EmployeeRepository $employeeRepository
    )
    {
        $this->employeeRepository = $employeeRepository;
    }

    public function create($form)
    {
        $employee = Employee::create(
            $form->person_id,
            $form->dolzh_id,
            $form->podraz_id,
            $form->build_id,
            $form->employee_begin
        );
        $this->employeeRepository->add($employee);

        return true;
    }

    public function update($id, $person_id, $dolzh_id, $podraz_id, $build_id, $employee_begin, $created_at, $updated_at, $created_by, $updated_by)
    {
        $employee = $this->employeeRepository->find($id);

        $employee->editData($person_id, $dolzh_id, $podraz_id, $build_id, $employee_begin, $created_at, $updated_at, $created_by, $updated_by);
        $this->employeeRepository->save($employee);

        return true;
    }

    public function delete($id)
    {
        $employee = $this->employeeRepository->find($id);
        $this->employeeRepository->delete($employee);
    }
}