<?php

/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 13.05.2017
 * Time: 18:27
 */

namespace domain\services\base;

use domain\repositories\base\PersonRepository;
use domain\services\base\dto\PersonData;
use domain\services\base\dto\ProfileData;
use domain\services\BaseService;

class PersonService extends BaseService
{
    private $personRepository;

    public function __construct(
        PersonRepository $personRepository
    )
    {
        $this->personRepository = $personRepository;

        parent::__construct();
    }

    public function create(PersonData $personData, ProfileData $profileData, $assignEmployees, $assignRoles)
    {

    }
}