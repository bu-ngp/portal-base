<?php

namespace domain\services\base;

use domain\models\base\Employee;
use domain\repositories\base\EmployeeRepository;
use domain\services\BaseService;

class EmployeeService extends BaseService
{
    private $employeeRepository;

    public function __construct(
        EmployeeRepository $employeeRepository
    )
    {
        $this->employeeRepository = $employeeRepository;

        parent::__construct();
    }

    public function create($person_id, $dolzh_id, $podraz_id, $build_id, $employee_begin, $created_at, $updated_at, $created_by, $updated_by)
    {
        $employee = Employee::create($person_id, $dolzh_id, $podraz_id, $build_id, $employee_begin, $created_at, $updated_at, $created_by, $updated_by);
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