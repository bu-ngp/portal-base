<?php

/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 13.05.2017
 * Time: 18:27
 */

namespace domain\services\base;

use domain\helpers\BinaryHelper;
use domain\models\base\Person;
use domain\forms\base\ProfileForm;
use domain\forms\base\UserForm;
use domain\forms\base\UserFormUpdate;
use domain\models\base\AuthAssignment;
use domain\models\base\Parttime;
use domain\models\base\Profile;
use domain\repositories\base\AuthAssignmentRepository;
use domain\repositories\base\ParttimeRepository;
use domain\repositories\base\PersonRepository;
use domain\repositories\base\ProfileRepository;
use domain\services\TransactionManager;
use domain\services\Service;
use wartron\yii2uuid\helpers\Uuid;
use Yii;

class PersonService extends Service
{
    private $transactionManager;
    private $persons;
    private $profiles;
    private $authAssignments;
    private $parttimes;

    public function __construct(
        TransactionManager $transactionManager,
        PersonRepository $persons,
        ProfileRepository $profiles,
        AuthAssignmentRepository $authAssignments,
        ParttimeRepository $parttimes
    )
    {
        $this->transactionManager = $transactionManager;
        $this->persons = $persons;
        $this->profiles = $profiles;
        $this->authAssignments = $authAssignments;
        $this->parttimes = $parttimes;
    }

    public function getUser($id)
    {
        $uuid = Uuid::str2uuid($id);
        return $this->persons->find($uuid);
    }

    public function getProfile($id)
    {
        $uuid = Uuid::str2uuid($id);
        return $this->profiles->has($uuid) ? $this->profiles->find($uuid) : false;
    }


    /**
     * @param $inn
     * @return bool|null|Person
     */
    public function getUserByINN($inn)
    {
        return $this->profiles->hasByINN($inn) ? $this->persons->findByINN($inn) : false;
    }

    public function create(UserForm $userForm, ProfileForm $profileForm)
    {
        $this->guardPasswordLength($userForm);
        $assignedKeysUser = $this->guardAssignRoles($userForm);
        $person = Person::create($userForm);
        $personValidate = $this->validateModels($person, $userForm);

        $this->filterEmptyValues($profileForm);
        $profile = Profile::create($person->primaryKey, $profileForm);

        if (!($personValidate && $this->validateModels($profile, $profileForm))) {
            throw new \DomainException();
        }

        $authAssignment = AuthAssignment::create($person, $assignedKeysUser);

        return $this->transactionManager->execute(function () use ($person, $profile, $authAssignment) {
            $this->persons->add($person);
            if ($profile->isNotEmpty()) {
                $this->profiles->add($profile);
            }

            foreach ($authAssignment as $item) {
                $this->authAssignments->add($item);
            }

            return Uuid::uuid2str($person->primaryKey);
        });
    }

    public function update($uuid, UserFormUpdate $userFormUpdate, ProfileForm $profileForm)
    {
        $person = $this->persons->find($uuid);
        $person->edit($userFormUpdate);
        $personValidate = $this->validateModels($person, $userFormUpdate);

        if ($this->profiles->has($uuid)) {
            $profile = $this->profiles->find($uuid);
            $profile->edit($profileForm);
        } else {
            $this->filterEmptyValues($profileForm);
            $profile = Profile::create($uuid, $profileForm);
        }

        if (!($personValidate && $this->validateModels($profile, $profileForm))) {
            throw new \DomainException();
        }

        $parttimes = [];
        if ($person->person_fired) {
            $parttimes = $this->parttimesForClose($person->person_id, $person->person_fired);
        }

        $this->transactionManager->execute(function () use ($person, $profile, $parttimes) {
            $this->persons->save($person);

            foreach ($parttimes as $parttime) {
                $this->parttimes->save($parttime);
            }

            if ($profile->isNotEmpty()) {
                $profile->isNewRecord ? $this->profiles->add($profile) : $this->profiles->save($profile);
            }
        });
    }

    public function delete($id)
    {
        $uuid = Uuid::str2uuid($id);
        $person = $this->persons->find($uuid);
        $this->persons->delete($person);
    }

    public function guardPasswordLength(UserForm $userForm)
    {
        if (mb_strlen($userForm->person_password, 'UTF-8') < 6) {
            throw new \DomainException(Yii::t('domain/person', 'Password very short. Need minimum 6 characters.'));
        }
    }

    public function gridInjectCallBack()
    {
        return function (\yii\db\ActiveRecord $model, $mainId, $mainField, $foreignField, $foreignId) {
            $auth = Yii::$app->authManager;
            $role = $auth->getRole($foreignId);
            $userUUID = BinaryHelper::isBinaryValidString($mainId) ? Uuid::str2uuid($mainId) : false;

            if (!$role) {
                throw new \DomainException(Yii::t('domain/base', "Role '{role}' not found", ['role' => $foreignId]));
            }

            if (!($userUUID && $auth->assign($role, $userUUID))) {
                throw new \DomainException(Yii::t('domain/base', 'Saving error.'));
            }
        };
    }

    protected function guardAssignRoles($form)
    {
        if (!is_string($form->assignRoles) || ($assignedKeys = json_decode($form->assignRoles)) === null) {
            throw new \DomainException(Yii::t('domain/base', 'Error when recognizing selected items'));
        }

        return $assignedKeys;
    }

    protected function filterEmptyValues(ProfileForm $profileForm)
    {
        $profileForm->setAttributes(array_map(function ($value) {
            return $value === null || $value === '' ? null : $value;
        }, $profileForm->getAttributes()));
    }

    protected function parttimesForClose($person_id, $date_fire)
    {
        /** @var Parttime[] $parttimes */
        $parttimes = $this->parttimes->getOpenAndMoreParttimes($person_id, $date_fire);

        foreach ($parttimes as $parttime) {
            $parttime->parttime_end = $date_fire;
        }

        return $parttimes;
    }
}