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
use domain\models\base\Profile;
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

    public function __construct(
        TransactionManager $transactionManager,
        PersonRepository $personRepository,
        ProfileRepository $profileRepository
    )
    {
        $this->transactionManager = $transactionManager;
        $this->personRepository = $personRepository;
        $this->profileRepository = $profileRepository;
    }

    public function create(UserForm $userForm, ProfileForm $profileForm, $assignEmployees, $assignRoles)
    {
        $this->guardPasswordLength($userForm);
        $person = Person::create($userForm);
        $personValidate = $this->validateModels($person, $userForm);

        $profileForm->setAttributes(array_map(function ($value) {
            return empty($value) ? null : $value;
        }, $profileForm->getAttributes()));
        $profile = Profile::create($person->person_id, $profileForm);

        if (NotifyShower::hasErrors() || !$personValidate || !$this->validateModels($profile, $profileForm)) {
            return false;
        }

        return $this->transactionManager->execute(function () use ($person, $profile) {
            $this->personRepository->add($person);
            if ($profile->isNotEmpty()) {
                $this->profileRepository->add($profile);
            }
        });
    }

    public function guardPasswordLength(UserForm $userForm)
    {
        if (mb_strlen($userForm->person_password, 'UTF-8') < 6) {
            NotifyShower::message(Yii::t('domain\person', 'Password very short. Need minimum 6 characters.'));
        }
    }
}