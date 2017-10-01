<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 01.10.2017
 * Time: 9:23
 */

namespace domain\services\base\dto;

class ProfileData
{
    public $profile_inn;
    public $profile_dr;
    public $profile_pol;
    public $profile_snils;
    public $profile_address;

    public function __construct(
        $profile_inn,
        $profile_dr,
        $profile_pol,
        $profile_snils,
        $profile_address
    )
    {
        $this->profile_inn = $profile_inn;
        $this->profile_dr = $profile_dr;
        $this->profile_pol = $profile_pol;
        $this->profile_snils = $profile_snils;
        $this->profile_address = $profile_address;
    }
}