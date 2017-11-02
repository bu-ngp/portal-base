<?php

/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 12.05.2017
 * Time: 22:14
 */

namespace console\helpers;

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
}