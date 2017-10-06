<?php

/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 13.05.2017
 * Time: 18:27
 */

namespace domain\services\base;

use common\models\base\Person;
use domain\models\base\Profile;
use domain\repositories\base\PersonRepository;
use domain\repositories\base\ProfileRepository;
use domain\services\base\dto\PersonData;
use domain\services\base\dto\ProfileData;
use domain\services\BaseService;

class PersonService extends BaseService
{
    private $personRepository;
    private $profileRepository;

    public function __construct(
        PersonRepository $personRepository,
        ProfileRepository $profileRepository
    )
    {
        $this->personRepository = $personRepository;
        $this->profileRepository = $profileRepository;

        parent::__construct();
    }

    public function create(PersonData $personData, ProfileData $profileData, $assignEmployees, $assignRoles)
    {
        $person = Person::create($personData);
        $profile = Profile::create($person->primaryKey, $profileData);

        $this->personRepository->add($person);
        $this->profileRepository->add($profile);

        return true;
    }
}