<?php

/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 12.05.2017
 * Time: 22:14
 */

namespace console\helpers;

use Yii;

class RbacHelper
{
    /**
     * @var string Разрешение 'Авторизованный пользователь'
     */
    const AUTHORIZED = 'authorized';
    /**
     * @var string Роль 'Права авторизованного пользователя'
     */
    const BASE_AUTHORIZED = 'baseAuthorized';
    /**
     * @var string Разрешение 'Редактирование пользователей'
     */
    const USER_EDIT = 'userEdit';
    /**
     * @var string Роль 'Оператор менеджера пользователей'
     */
    const USER_OPERATOR = 'userOperator';
    /**
     * @var string Разрешение 'Редактирование ролей пользователя'
     */
    const ROLE_EDIT = 'roleEdit';
    /**
     * @var string Роль 'Оператор менеджера ролей'
     */
    const ROLE_OPERATOR = 'roleOperator';
    /**
     * @var string Разрешение 'Редактирование справочника "Подразделения"'
     */
    const PODRAZ_EDIT = 'podrazEdit';
    /**
     * @var string Роль 'Оператор справочника "Подразделения"'
     */
    const BASE_PODRAZ_EDIT = 'basePodrazEdit';
    /**
     * @var string Разрешение 'Редактирование справочника "Должности"'
     */
    const DOLZH_EDIT = 'dolzhEdit';
    /**
     * @var string Роль 'Оператор справочника "Должности"'
     */
    const BASE_DOLZH_EDIT = 'baseDolzhEdit';
    /**
     * @var string Разрешение 'Редактирование справочника "Здания"'
     */
    const BUILD_EDIT = 'buildEdit';
    /**
     * @var string Роль 'Оператор справочника "Здания"'
     */
    const BASE_BUILD_EDIT = 'baseBuildEdit';
    /**
     * @var string Роль 'Администратор базовой конфигурации'
     */
    const BASE_ADMINISTRATOR = 'baseAdministrator';
    /**
     * @var string Роль 'Администратор системы'
     */
    const ADMINISTRATOR = 'Administrator';

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
}