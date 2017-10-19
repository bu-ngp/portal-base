<?php

namespace domain\services\base;

use domain\forms\base\ParttimeForm;
use domain\models\base\Employee;
use domain\models\base\Parttime;
use domain\models\base\ParttimeBuild;
use domain\repositories\base\ParttimeBuildRepository;
use domain\repositories\base\ParttimeRepository;
use domain\repositories\base\EmployeeRepository;
use domain\services\TransactionManager;
use domain\services\WKService;
use Yii;

class ParttimeService extends WKService
{
    private $transactionManager;
    private $parttimeRepository;
    private $parttimeBuildRepository;

    public function __construct(
        TransactionManager $transactionManager,
        ParttimeRepository $parttimeRepository,
        ParttimeBuildRepository $parttimeBuildRepository
    )
    {
        $this->transactionManager = $transactionManager;
        $this->parttimeRepository = $parttimeRepository;
        $this->parttimeBuildRepository = $parttimeBuildRepository;
    }

    public function get($id)
    {
        return $this->parttimeRepository->find($id);
    }

    public function create(ParttimeForm $form)
    {
        $this->guardPersonExists($form);

        $parttime = Parttime::create($form);
        if (!$this->validateModels($parttime, $form)) {
            throw new \DomainException();
        }

        $this->transactionManager->execute(function () use ($parttime) {
            $this->parttimeRepository->add($parttime);

            return $parttime->primaryKey;
        });
    }

    public function update($id, ParttimeForm $form)
    {
        $employee = $this->parttimeRepository->find($id);

        $employee->edit($form);

        if (!$this->validateModels($employee, $form)) {
            throw new \DomainException();
        }

        $this->parttimeRepository->save($employee);
    }

    public function delete($id)
    {
        $parttime = $this->parttimeRepository->find($id);
        $this->parttimeRepository->delete($parttime);
    }

    protected function guardPersonExists(ParttimeForm $form)
    {
        if (!$form->person_id) {
            throw new \DomainException(Yii::t('domain/employee', 'URL parameter "person" is missed.'));
        }
    }
}