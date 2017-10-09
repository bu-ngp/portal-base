<?php

/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 13.05.2017
 * Time: 18:27
 */

namespace domain\services\base;

use common\models\base\Person;
use common\widgets\NotifyShower\NotifyShower;
use domain\forms\base\ProfileForm;
use domain\forms\base\UserForm;
use domain\models\base\AuthAssignment;
use domain\models\base\Profile;
use domain\repositories\base\AuthAssignmentRepository;
use domain\repositories\base\PersonRepository;
use domain\repositories\base\ProfileRepository;
use domain\services\base\dto\PersonData;
use domain\services\base\dto\ProfileData;
use domain\services\BaseService;
use domain\services\TransactionManager;
use domain\services\WKService;
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

        if (NotifyShower::hasErrors() || !$personValidate || !$this->validateModels($profile, $profileForm)) {
            return false;
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
        });
    }

    public function guardPasswordLength(UserForm $userForm)
    {
        if (mb_strlen($userForm->person_password, 'UTF-8') < 6) {
            NotifyShower::message(Yii::t('domain/person', 'Password very short. Need minimum 6 characters.'));
        }
    }

    private function guardAssignRoles($form)
    {
        if (!is_string($form->assignRoles) || ($assignedKeys = json_decode($form->assignRoles)) === null) {
            NotifyShower::message(\Yii::t('domain/person', 'Error when recognizing selected items'));

            return false;
        }

        return $assignedKeys;
    }
}