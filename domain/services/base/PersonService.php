<?php

/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 13.05.2017
 * Time: 18:27
 */

namespace domain\services\base;

use domain\repositories\base\PersonRepository;
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

    /**
     * Logs in a user using the provided username and password.
     * @param $person_username
     * @param $person_password
     * @param bool $rememberMe
     * @return bool whether the user is logged in successfully
     */
    public function login($person_username, $person_password, $rememberMe = true)
    {
        return $this->personRepository->login($person_username, $person_password, $rememberMe);
    }

    public function logout()
    {
        return $this->personRepository->logout();
    }

}