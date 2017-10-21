<?php

namespace domain\repositories\base;

use domain\models\base\Profile;
use Yii;

class ProfileRepository
{
    /**
     * @param $id
     * @return Profile
     */
    public function find($id)
    {
        if (!$profile = Profile::findOne($id)) {
            throw new \RuntimeException('Model not found.');
        }
        return $profile;
    }

    /**
     * @param $id
     * @return bool
     */
    public function has($id)
    {
        return boolval(Profile::findOne($id));
    }

    /**
     * @param Profile $profile
     */
    public function add(Profile $profile)
    {
        if (!$profile->getIsNewRecord()) {
            throw new \DomainException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if (!$profile->insert(false)) {
            throw new \DomainException(Yii::t('domain/base', 'Saving error.'));
        }
    }

    /**
     * @param Profile $profile
     */
    public function save(Profile $profile)
    {
        if ($profile->getIsNewRecord()) {
            throw new \DomainException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if ($profile->update(false) === false) {
            throw new \DomainException(Yii::t('domain/base', 'Saving error.'));
        }
    }

    /**
     * @param Profile $profile
     */
    public function delete(Profile $profile)
    {
        if (!$profile->delete()) {
            throw new \DomainException(Yii::t('domain/base', 'Deleting error.'));
        }
    }
}