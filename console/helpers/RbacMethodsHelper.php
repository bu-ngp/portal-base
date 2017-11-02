<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 02.11.2017
 * Time: 9:28
 */

namespace console\helpers;

use Yii;

class RbacMethodsHelper
{
    /**
     * Метод создает разрешение, если оно не существует
     *
     * @param string $name string Имя разрешения
     * @param string $description string Описание разрешения
     * @return null|\yii\rbac\Permission
     */
    public static function createPermission($name, $description)
    {
        if (!$permission = Yii::$app->authManager->getPermission($name)) {
            $permission = Yii::$app->authManager->createPermission($name);
            $permission->description = $description;
            Yii::$app->authManager->add($permission);
        }
        return $permission;
    }

    /**
     * Метод создает роль, если она не существует и привязывает к ней роли и разрешения
     *
     * @param string $name Имя роли
     * @param string $description Описание роли
     * @param array|\yii\rbac\Permission|\yii\rbac\Role $children массив авторизационных единиц, или сама авторизационная единица
     * @return null|\yii\rbac\Role
     */
    public static function createRole($name, $description, $children = [])
    {
        if (!$role = Yii::$app->authManager->getRole($name)) {
            if (!is_array($children)) {
                $children = [$children];
            }

            $role = Yii::$app->authManager->createRole($name);
            $role->description = $description;
            Yii::$app->authManager->add($role);

            /**
             * @var \yii\rbac\Permission $child
             */
            foreach ($children as $child) {
                Yii::$app->authManager->addChild($role, $child);
            }
        }
        return $role;
    }

    public static function assignRole($name, $children = [])
    {
        if ($role = Yii::$app->authManager->getRole($name)) {
            if (!is_array($children)) {
                $children = [$children];
            }

            /**
             * @var \yii\rbac\Permission $child
             */
            foreach ($children as $child) {
                $child = Yii::$app->authManager->getRole($child);
                Yii::$app->authManager->addChild($role, $child);
            }

            return true;
        }
        return false;
    }
}