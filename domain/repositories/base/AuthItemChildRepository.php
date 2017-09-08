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
use domain\models\base\AuthItemChild;
use domain\repositories\RepositoryInterface;
use RuntimeException;
use Yii;

class AuthItemChildRepository implements RepositoryInterface
{

    public function find($id)
    {
        if (!$authItemChild = AuthItemChild::findOne($id)) {
            throw new RuntimeException('Model not found.');
        }
        return $authItemChild;
    }

    public function add($authItemChild)
    {
        /**
         * @var AuthItemChild $item
         */
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

    public function save($authItemChild)
    {
//        if ($person->getIsNewRecord()) {
//            throw new \RuntimeException(Yii::t('domain/base', 'Adding existing model.'));
//        }
//        if ($person->update(false) === false) {
//            throw new \RuntimeException(Yii::t('domain/base', 'Saving error.'));
//        }
    }

    public function delete($authItemChild)
    {
//        if (!$person->delete()) {
//            throw new \RuntimeException(Yii::t('domain/base', 'Deleting error.'));
//        }
    }
}