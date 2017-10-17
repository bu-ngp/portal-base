<?php

/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 13.05.2017
 * Time: 18:27
 */

namespace domain\services\base;

use common\models\base\Person;
use domain\forms\base\ProfileForm;
use domain\forms\base\UserForm;
use domain\forms\base\UserFormUpdate;
use domain\models\base\AuthAssignment;
use domain\models\base\Profile;
use domain\repositories\base\AuthAssignmentRepository;
use domain\repositories\base\PersonRepository;
use domain\repositories\base\ProfileRepository;
use domain\services\TransactionManager;
use domain\services\WKService;
use wartron\yii2uuid\helpers\Uuid;
use Yii;

class PersonService extends WKService
{
    private $transactionManager;
    private $personRepository;
    private $profileRepository;
    private $authAssignmentRepository;

    public function __construct(
        TransactionManager $transactionManager,
        PersonRepository $personRepository,
        ProfileRepository $profileRepository,
        AuthAssignmentRepository $authAssignmentRepository
    )
    {
        $this->transactionManager = $transactionManager;
        $this->personRepository = $personRepository;
        $this->profileRepository = $profileRepository;
        $this->authAssignmentRepository = $authAssignmentRepository;
    }

    public function get($id)
    {
        return $this->personRepository->find(Uuid::str2uuid($id));
    }

    public function getProfile($id)
    {
        $uuid = Uuid::str2uuid($id);
        if ($this->profileRepository->has($uuid)) {
            return $this->profileRepository->find($uuid);
        }

        return false;
    }

    public function create(UserForm $userForm, ProfileForm $profileForm)
    {
        $this->guardPasswordLength($userForm);
        $assignedKeysUser = $this->guardAssignRoles($userForm);
        $person = Person::create($userForm);
        $personValidate = $this->validateModels($person, $userForm);

        $profileForm->setAttributes(array_map(function ($value) {
            return empty($value) ? null : $value;
        }, $profileForm->getAttributes()));
        $profile = Profile::create($person->person_id, $profileForm);

        if (!($personValidate && $this->validateModels($profile, $profileForm))) {
            throw new \DomainException();
        }

        $authAssignment = AuthAssignment::create($person, $assignedKeysUser);

        return $this->transactionManager->execute(function () use ($person, $profile, $authAssignment) {
            $this->personRepository->add($person);
            if ($profile->isNotEmpty()) {
                $this->profileRepository->add($profile);
            }

            foreach ($authAssignment as $item) {
                $this->authAssignmentRepository->add($item);
            }

            return $person->primaryKey;
        });
    }

    public function update($id, UserFormUpdate $userFormUpdate, ProfileForm $profileForm)
    {
        $person = $this->personRepository->find($id);
        $person->edit($userFormUpdate);
        $personValidate = $this->validateModels($person, $userFormUpdate);

        if ($this->profileRepository->has($id)) {
            $profile = $this->profileRepository->find($id);
            $profile->edit($profileForm);
        } else {
            $profileForm->setAttributes(array_map(function ($value) {
                return empty($value) ? null : $value;
            }, $profileForm->getAttributes()));
            $profile = Profile::create($id, $profileForm);
        }

        if (!($personValidate && $this->validateModels($profile, $profileForm))) {
            throw new \DomainException();
        }

        $this->transactionManager->execute(function () use ($person, $profile) {
            $this->personRepository->save($person);
            if ($profile->isNotEmpty()) {
                $profile->isNewRecord ? $this->profileRepository->add($profile) : $this->profileRepository->save($profile);
            }
        });
    }

    public function guardPasswordLength(UserForm $userForm)
    {
        if (mb_strlen($userForm->person_password, 'UTF-8') < 6) {
            Yii::$app->session->addFlash('error', Yii::t('domain/person', 'Password very short. Need minimum 6 characters.'));
        }
    }

    private function guardAssignRoles($form)
    {
        if (!is_string($form->assignRoles) || ($assignedKeys = json_decode($form->assignRoles)) === null) {
            throw new \DomainException(\Yii::t('domain/person', 'Error when recognizing selected items'));
        }

        return $assignedKeys;
    }
}