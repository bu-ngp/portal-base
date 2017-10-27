<?php

namespace domain\services\base;

use domain\helpers\BinaryHelper;
use domain\forms\base\ParttimeForm;
use domain\models\base\Parttime;
use domain\repositories\base\ParttimeRepository;
use domain\services\TransactionManager;
use domain\services\Service;
use wartron\yii2uuid\helpers\Uuid;
use Yii;

class ParttimeService extends Service
{
    private $transactionManager;
    private $parttimes;

    public function __construct(
        TransactionManager $transactionManager,
        ParttimeRepository $parttimes
    )
    {
        $this->transactionManager = $transactionManager;
        $this->parttimes = $parttimes;
    }

    public function get($id)
    {
        return $this->parttimes->find($id);
    }

    public function create(ParttimeForm $form)
    {
        $this->guardPersonExists($form);
        $this->guardAssignBuilds($form);
        $this->filterEmployeeUUIDCreate($form);
        $parttime = Parttime::create($form);

        if (!$this->validateModels($parttime, $form)) {
            throw new \DomainException();
        }

        $this->transactionManager->execute(function () use ($parttime) {
            $this->parttimes->add($parttime);
        });
    }

    public function update($id, ParttimeForm $form)
    {
        $employee = $this->parttimes->find($id);
        $this->filterEmployeeUUIDUpdate($form);
        $employee->edit($form);

        if (!$this->validateModels($employee, $form)) {
            throw new \DomainException();
        }

        $this->parttimes->save($employee);
    }

    public function delete($id)
    {
        $parttime = $this->parttimes->find($id);
        $this->parttimes->delete($parttime);
    }

    protected function guardPersonExists(ParttimeForm $form)
    {
        if (!$form->person_id) {
            throw new \DomainException(Yii::t('domain/employee', 'URL parameter "person" is missed.'));
        }
    }

    protected function filterEmployeeUUIDCreate(ParttimeForm $form)
    {
        if (BinaryHelper::isBinaryValidString($form->person_id)
            && BinaryHelper::isBinaryValidString($form->dolzh_id)
            && BinaryHelper::isBinaryValidString($form->podraz_id)
        ) {
            $form->person_id = Uuid::str2uuid($form->person_id);
            $form->dolzh_id = Uuid::str2uuid($form->dolzh_id);
            $form->podraz_id = Uuid::str2uuid($form->podraz_id);
        } else {
            throw new \RuntimeException(Yii::t('domain/employee', 'Invalid UUID Parameters.'));
        }

        $form->assignBuilds = array_map(function ($buildId) {
            return BinaryHelper::isBinaryValidString($buildId) ? Uuid::str2uuid($buildId) : $buildId;
        }, $form->assignBuilds);
    }

    protected function filterEmployeeUUIDUpdate(ParttimeForm $form)
    {
        if (BinaryHelper::isBinaryValidString($form->dolzh_id)
            && BinaryHelper::isBinaryValidString($form->podraz_id)
        ) {
            $form->dolzh_id = Uuid::str2uuid($form->dolzh_id);
            $form->podraz_id = Uuid::str2uuid($form->podraz_id);
        } else {
            throw new \RuntimeException(Yii::t('domain/employee', 'Invalid UUID Parameters.'));
        }
    }

    protected function guardAssignBuilds(ParttimeForm $form)
    {
        if (!is_string($form->assignBuilds) || ($form->assignBuilds = json_decode($form->assignBuilds)) === null) {
            throw new \DomainException(Yii::t('domain/base', 'Error when recognizing selected items'));
        }
    }
}