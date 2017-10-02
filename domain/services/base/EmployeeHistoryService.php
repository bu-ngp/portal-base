<?php

namespace domain\services\base;

use domain\models\base\EmployeeHistory;
use domain\repositories\base\EmployeeHistoryRepository;
use domain\services\BaseService;

class EmployeeHistoryService extends BaseService
{
    private $employeeHistoryRepository;

    public function __construct(
        EmployeeHistoryRepository $employeeHistoryRepository
    )
    {
        $this->employeeHistoryRepository = $employeeHistoryRepository;

        parent::__construct();
    }

    public function create($person_id, $dolzh_id, $podraz_id, $build_id, $employee_history_begin, $created_at, $updated_at, $created_by, $updated_by)
    {
        $employeeHistory = EmployeeHistory::create($person_id, $dolzh_id, $podraz_id, $build_id, $employee_history_begin, $created_at, $updated_at, $created_by, $updated_by);
        $this->employeeHistoryRepository->add($employeeHistory);

        return true;
    }

    public function update($id, $person_id, $dolzh_id, $podraz_id, $build_id, $employee_history_begin, $created_at, $updated_at, $created_by, $updated_by)
    {
        $employeeHistory = $this->employeeHistoryRepository->find($id);

        $employeeHistory->editData($person_id, $dolzh_id, $podraz_id, $build_id, $employee_history_begin, $created_at, $updated_at, $created_by, $updated_by);
        $this->employeeHistoryRepository->save($employeeHistory);

        return true;
    }

    public function delete($id)
    {
        $employeeHistory = $this->employeeHistoryRepository->find($id);
        $this->employeeHistoryRepository->delete($employeeHistory);
    }
}