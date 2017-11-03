<?php

namespace ngp\services\repositories;

use ngp\services\models\ConfigOfoms;
use RuntimeException;
use Yii;

class ConfigOfomsRepository
{
    /**
     * @return ConfigOfoms
     */
    public function find()
    {
        if (!$configOfoms = ConfigOfoms::findOne(1)) {
            throw new RuntimeException('Model not found.');
        }

        return $configOfoms;
    }

    /**
     * @param ConfigOfoms $configOfoms
     * @throws \Exception
     */
    public function save(ConfigOfoms $configOfoms)
    {
        if ($configOfoms->getIsNewRecord()) {
            throw new \DomainException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if ($configOfoms->update(false) === false) {
            throw new \DomainException(Yii::t('domain/base', 'Saving error.'));
        }
    }
}