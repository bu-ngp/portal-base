<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 13.05.2017
 * Time: 18:21
 */

namespace domain\repositories\base;

use domain\models\base\ConfigLdap;
use domain\repositories\RepositoryInterface;
use RuntimeException;
use Yii;

class ConfigLdapRepository implements RepositoryInterface
{

    /**
     * @param int $id
     * @return ConfigLdap|null
     */
    public function find($id = 0)
    {
        if (!$configLdap = ConfigLdap::findOne(1)) {
            throw new RuntimeException('Model not found.');
        }

        return $configLdap;
    }

    public function add($model)
    {
        throw new RuntimeException('unsupported');
    }

    /**
     * @param ConfigLdap $configLdap
     * @return bool
     */
    public function save($configLdap)
    {
        if ($configLdap->getIsNewRecord()) {
            throw new \RuntimeException(Yii::t('domain/base', 'unsupported'));
        }
        if ($configLdap->update(false) === false) {
            throw new \RuntimeException(Yii::t('domain/base', 'Saving error.'));
        }

        return true;
    }

    public function delete($model)
    {
        throw new RuntimeException('unsupported');
    }
}