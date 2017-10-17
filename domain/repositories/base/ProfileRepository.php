<?php

namespace domain\repositories\base;

use domain\models\base\Profile;
use domain\repositories\RepositoryInterface;
use RuntimeException;
use Yii;

class ProfileRepository implements RepositoryInterface
{
    /**
     * @param $id
     * @return Profile
     */
    public function find($id)
    {
        if (!$profile = Profile::findOne($id)) {
            throw new RuntimeException('Model not found.');
        }

        return $profile;
    }

    public function has($id)
    {
        return !!Profile::findOne($id);
    }

    /**
     * @param Profile $profile
     */
    public function add($profile)
    {
        if (!$profile->getIsNewRecord()) {
            throw new \RuntimeException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if (!$profile->insert(false)) {
            throw new \RuntimeException(Yii::t('domain/base', 'Saving error.'));
        }
    }

    /**
     * @param Profile $profile
     */
    public function save($profile)
    {
        if ($profile->getIsNewRecord()) {
            throw new \RuntimeException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if ($profile->update(false) === false) {
            throw new \RuntimeException(Yii::t('domain/base', 'Saving error.'));
        }
    }

    /**
     * @param Profile $profile
     */
    public function delete($profile)
    {
        if (!$profile->delete()) {
            throw new \RuntimeException(Yii::t('domain/base', 'Deleting error.'));
        }
    }
}