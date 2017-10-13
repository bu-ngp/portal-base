<?php

namespace domain\services\base;

use domain\models\base\Profile;
use domain\repositories\base\ProfileRepository;
use domain\services\WKService;

class ProfileService extends WKService
{
    private $profileRepository;

    public function __construct(
        ProfileRepository $profileRepository
    )
    {
        $this->profileRepository = $profileRepository;
    }

    public function create($profile_inn, $profile_dr, $profile_pol, $profile_snils, $profile_address, $created_at, $updated_at, $created_by, $updated_by)
    {
        $profile = Profile::create($profile_inn, $profile_dr, $profile_pol, $profile_snils, $profile_address, $created_at, $updated_at, $created_by, $updated_by);
        $this->profileRepository->add($profile);

        return true;
    }

    public function update($id, $profile_inn, $profile_dr, $profile_pol, $profile_snils, $profile_address, $created_at, $updated_at, $created_by, $updated_by)
    {
        $profile = $this->profileRepository->find($id);

        $profile->editData($profile_inn, $profile_dr, $profile_pol, $profile_snils, $profile_address, $created_at, $updated_at, $created_by, $updated_by);
        $this->profileRepository->save($profile);

        return true;
    }

    public function delete($id)
    {
        $profile = $this->profileRepository->find($id);
        $this->profileRepository->delete($profile);
    }
}