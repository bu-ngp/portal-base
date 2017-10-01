<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 01.10.2017
 * Time: 9:13
 */

namespace domain\services\base\dto;

class PersonData
{
    public $person_fullname;
    public $person_username;
    public $person_password;
    public $person_password_repeat;
    public $person_email;
    public $person_fired;

    public function __construct(
        $person_fullname,
        $person_username,
        $person_password,
        $person_password_repeat,
        $person_email,
        $person_fired
    )
    {
        $this->person_fullname = $person_fullname;
        $this->person_username = $person_username;
        $this->person_password = $person_password;
        $this->person_password_repeat = $person_password_repeat;
        $this->person_email = $person_email;
        $this->person_fired = $person_fired;
    }
}