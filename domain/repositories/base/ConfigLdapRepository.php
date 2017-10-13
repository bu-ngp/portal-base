<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 13.05.2017
 * Time: 18:21
 */

namespace domain\repositories\base;

use domain\models\base\ConfigLdap;
use Yii;

class ConfigLdapRepository
{
    /**
     * @return ConfigLdap
     */
    public function find()
    {
        if (!$configLdap = ConfigLdap::findOne(1)) {
            throw new \RuntimeException('Model not found.');
        }

        return $configLdap;
    }

    /**
     * @param ConfigLdap $configLdap
     * @throws \Exception
     */
    public function save(ConfigLdap $configLdap)
    {
        if ($configLdap->getIsNewRecord()) {
            throw new \DomainException(Yii::t('domain/base', 'unsupported'));
        }
        if ($configLdap->update(false) === false) {
            throw new \DomainException(Yii::t('domain/base', 'Saving error.'));
        }
    }
}