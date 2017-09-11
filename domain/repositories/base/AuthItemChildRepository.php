<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 13.05.2017
 * Time: 18:21
 */

namespace domain\repositories\base;

use domain\models\base\AuthItem;
use domain\models\base\AuthItemChild;
use domain\repositories\RepositoryInterface;
use RuntimeException;
use Yii;

class AuthItemChildRepository implements RepositoryInterface
{
    /**
     * @param $id
     * @return AuthItemChild
     */
    public function find($id)
    {
        if (!$authItemChild = AuthItemChild::findOne($id)) {
            throw new RuntimeException('Model not found.');
        }

        return $authItemChild;
    }

    /**
     * @param AuthItemChild[] $authItemChild
     */
    public function add($authItemChild)
    {
        foreach ($authItemChild as $item) {
            if (!$parent = Yii::$app->authManager->getRole($item->parent)) {
                throw new RuntimeException("Parent {$item->parent} not exist.");
            }

            if (!$child = Yii::$app->authManager->getRole($item->child)) {
                throw new RuntimeException("Child {$item->child} not exist.");
            }

            if (Yii::$app->authManager->canAddChild($parent, $child)) {
                Yii::$app->authManager->addChild($parent, $child);
            } else {
                throw new RuntimeException("Can't assign '{$child->description}' to '{$parent->description}'");
            }
        }
    }

    /**
     * @param AuthItemChild $authItemChild
     */
    public function save($authItemChild)
    {
        throw new RuntimeException("Not exists save method for this model");
    }

    /**
     * @param AuthItemChild $authItemChild
     */
    public function delete($authItemChild)
    {
        if (!$parent = Yii::$app->authManager->getRole($authItemChild->parent)) {
            throw new \RuntimeException(Yii::t('domain/base', 'Deleting error. Parent is missed.'));
        }

        if (!$child = Yii::$app->authManager->getRole($authItemChild->child)) {
            throw new \RuntimeException(Yii::t('domain/base', 'Deleting error. Child is missed.'));
        }

        if (!Yii::$app->authManager->removeChild($parent, $child)) {
            throw new \RuntimeException(Yii::t('domain/base', 'Deleting error. Remove Child Fail.'));
        };
    }

    /**
     * @param AuthItem $authItem
     */
    public function removeChildren($authItem)
    {
        if (!$role = Yii::$app->authManager->getRole($authItem->name)) {
            throw new \RuntimeException(Yii::t('domain/base', "Deleting error. Role '{role}' not exists", ['role' => $authItem->name]));
        }

        if (Yii::$app->authManager->getChildren($role->name) && !Yii::$app->authManager->removeChildren($role)) {
            throw new \RuntimeException(Yii::t('domain/base', 'Deleting error. Remove Children Fail.'));
        }
    }
}