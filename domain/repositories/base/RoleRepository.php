<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 13.05.2017
 * Time: 18:21
 */

namespace domain\repositories\base;

use common\models\base\Person;
use domain\exceptions\ServiceErrorsException;
use domain\models\base\AuthItem;
use domain\repositories\RepositoryInterface;
use RuntimeException;
use Yii;
use yii\rbac\Role;

class RoleRepository implements RepositoryInterface
{

    /**
     * @param $id
     * @return AuthItem
     */
    public function find($id)
    {
        if (!$authitem = AuthItem::findOne($id)) {
            throw new RuntimeException('Model not found.');
        }
        return $authitem;
    }

    /**
     * @param AuthItem $authitem
     */
    public function add($authitem)
    {
        if ($role = Yii::$app->authManager->getRole($authitem->name)) {
            throw new RuntimeException('Adding existing model.');
        }

        $role = Yii::$app->authManager->createRole($authitem->name);
        $role->description = $authitem->description;

        if (!Yii::$app->authManager->add($role)) {
            throw new RuntimeException('Saving error.');
        }
    }

    public function save($authitem)
    {
        if (!($role = Yii::$app->authManager->getRole($authitem->name))) {
            throw new RuntimeException('Authitem not exists.');
        }

        $role->description = $authitem->description;

        if (!Yii::$app->authManager->update($authitem->primaryKey, $role)) {
            throw new RuntimeException('Saving error.');
        }
    }

    public function delete($authitem)
    {
//        if (!$person->delete()) {
//            throw new \RuntimeException(Yii::t('domain/base', 'Deleting error.'));
//        }
    }
}