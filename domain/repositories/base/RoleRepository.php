<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 13.05.2017
 * Time: 18:21
 */

namespace domain\repositories\base;

use domain\models\base\AuthItem;
use Yii;

class RoleRepository
{
    /**
     * @param $id
     * @return AuthItem
     */
    public function find($id)
    {
        if (!$authitem = AuthItem::findOne($id)) {
            throw new \RuntimeException('Model not found.');
        }

        return $authitem;
    }

    /**
     * Поиск только пользовательской роли
     *
     * @param $id
     * @return AuthItem
     */
    public function findByUser($id)
    {
        if (!$authitem = AuthItem::find()
            ->where([
                'name' => $id,
                'view' => 0
            ])
            ->andWhere(['not in', 'name', ['Administrator']])
            ->one()
        ) {
            throw new \DomainException('Model not found.');
        }

        return $authitem;
    }

    /**
     * @param AuthItem $authitem
     */
    public function add(AuthItem $authitem)
    {
        if ($role = Yii::$app->authManager->getRole($authitem->name)) {
            throw new \DomainException('Adding existing model.');
        }

        $role = Yii::$app->authManager->createRole($authitem->name);
        $role->description = $authitem->description;

        if (!Yii::$app->authManager->add($role)) {
            throw new \DomainException('Saving error.');
        }

        AuthItem::updateAll(['ldap_group' => $authitem->ldap_group], ['name' => $authitem->name]);
    }

    /**
     * @param AuthItem $authitem
     */
    public function save(AuthItem $authitem)
    {
        if (!($role = Yii::$app->authManager->getRole($authitem->name))) {
            throw new \DomainException('Authitem not exists.');
        }

        $role->description = $authitem->description;

        if (!Yii::$app->authManager->update($authitem->primaryKey, $role)) {
            throw new \DomainException('Saving error.');
        }

        AuthItem::updateAll(['ldap_group' => $authitem->ldap_group], ['name' => $authitem->name]);
    }

    /**
     * @param AuthItem $authitem
     */
    public function delete(AuthItem $authitem)
    {
        if (!($role = Yii::$app->authManager->getRole($authitem->name))) {
            throw new \DomainException('Role not exists.');
        }

        if (!Yii::$app->authManager->remove($role)) {
            throw new \DomainException(Yii::t('domain/authitem', 'Deleting error. Remove Role Fail.'));
        };
    }

    /**
     * @param AuthItem $authitem
     * @return bool
     */
    public function isEmptyChildren(AuthItem $authitem)
    {
        if (!($role = Yii::$app->authManager->getRole($authitem->name))) {
            throw new \DomainException('Role not exists.');
        }

        return count(Yii::$app->authManager->getChildren($authitem->name)) === 0;
    }
}