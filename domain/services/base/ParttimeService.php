<?php

namespace domain\services\base;

use domain\forms\base\ParttimeUpdateForm;
use domain\helpers\BinaryHelper;
use domain\forms\base\ParttimeForm;
use domain\models\base\Build;
use domain\models\base\Parttime;
use domain\repositories\base\EmployeeRepository;
use domain\repositories\base\ParttimeRepository;
use domain\services\TransactionManager;
use domain\services\Service;
use wartron\yii2uuid\helpers\Uuid;
use Yii;

class ParttimeService extends Service
{
    private $transactionManager;
    private $parttimes;
    private $employeeRepository;

    public function __construct(
        TransactionManager $transactionManager,
        ParttimeRepository $parttimes,
        EmployeeRepository $employeeRepository
    )
    {
        $this->transactionManager = $transactionManager;
        $this->parttimes = $parttimes;
        $this->employeeRepository = $employeeRepository;
    }

    public function get($id)
    {
        return $this->parttimes->find($id);
    }

    public function getParttimeByDate($person_id, $date)
    {
        $result = $this->parttimes->findByDate($person_id, $date);
        if ($result === null) {
            return false;
        }

        return $result;
    }

    public function create(ParttimeForm $form)
    {
        $this->guardAssignBuilds($form);
        $parttime = Parttime::create($form);

        if (!$this->validateModels($parttime, $form)) {
            throw new \DomainException();
        }

        $this->guardHasEmployee($form);

        $this->transactionManager->execute(function () use ($parttime) {
            $this->parttimes->add($parttime);
        });
    }

    public function update($id, ParttimeUpdateForm $form)
    {
        $parttime = $this->parttimes->find($id);
        $parttime->edit($form);

        if (!$this->validateModels($parttime, $form)) {
            throw new \DomainException();
        }

        $this->parttimes->save($parttime);
    }

    public function delete($id)
    {
        $parttime = $this->parttimes->find($id);
        $this->parttimes->delete($parttime);
    }

    protected function guardAssignBuilds(ParttimeForm $form)
    {
        if (!is_string($form->assignBuilds) || ($form->assignBuilds = json_decode($form->assignBuilds)) === null) {
            throw new \DomainException(Yii::t('domain/base', 'Error when recognizing selected items'));
        }

        $form->assignBuilds = array_filter(array_map(function ($build_id) {
            $build_id = BinaryHelper::isBinaryValidString($build_id) ? Uuid::str2uuid($build_id) : $build_id;
            if (Build::findOne($build_id)) {
                return $build_id;
            }
            return false;
        }, $form->assignBuilds));
    }

    protected function guardHasEmployee(ParttimeForm $form)
    {
        if (!$this->employeeRepository->hasByPerson($form->person_id)) {
            throw new \DomainException(Yii::t('domain/parttime', 'Person don\'t have speciality'));
        }
    }
}