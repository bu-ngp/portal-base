<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 13.05.2017
 * Time: 18:21
 */

namespace domain\repositories\base;

use domain\models\base\ConfigCommon;
use Yii;

class ConfigCommonRepository
{
    /**
     * @return ConfigCommon
     */
    public function find()
    {
        if (!$configCommon = ConfigCommon::findOne(1)) {
            throw new \RuntimeException('Model not found.');
        }

        return $configCommon;
    }

    /**
     * @param ConfigCommon $configCommon
     * @throws \Exception
     */
    public function save(ConfigCommon $configCommon)
    {
        if ($configCommon->getIsNewRecord()) {
            throw new \DomainException(Yii::t('domain/base', 'unsupported'));
        }
        if ($configCommon->update(false) === false) {
            throw new \DomainException(Yii::t('domain/base', 'Saving error.'));
        }
    }
}