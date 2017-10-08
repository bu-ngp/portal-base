<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 08.10.2017
 * Time: 8:22
 */

namespace domain\services;

use yii\base\Model;
use yii\db\ActiveRecord;

abstract class WKService
{
    /**
     * @param ActiveRecord|array $models
     * @param Model|null $form
     * @return bool
     * @throws \Exception
     */
    public function validateModels($models, Model $form = null)
    {
        if (is_array($models)) {
            foreach ($models as $model) {
                if (!$this->validateModel($model, $form)) {
                    return false;
                }
            }

            return true;
        } elseif ($models instanceof ActiveRecord) {
            return $this->validateModel($models, $form);
        }

        throw new \Exception('model need ActiveRecord or array of ActiveRecords');
    }

    /**
     * @param ActiveRecord $model
     * @param Model|null $form
     * @return bool
     */
    private function validateModel(ActiveRecord $model, Model $form = null)
    {
        if (!$model->validate()) {
            if ($form) {
                $form->addErrors($model->getErrors());
            }

            return false;
        }

        return true;
    }
}