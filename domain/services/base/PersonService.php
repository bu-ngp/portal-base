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

}