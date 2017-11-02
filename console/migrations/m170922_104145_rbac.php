<?php

use domain\models\base\Person;
use console\helpers\RbacHelper;
use console\helpers\RbacMethodsHelper;
use domain\models\base\AuthItem;
use yii\db\Migration;

class m170922_104145_rbac extends Migration
{
    public function safeUp()
    {
        $authorized = RbacMethodsHelper::createRole(RbacHelper::BASE_AUTHORIZED, 'Авторизованный пользователь',
            RbacMethodsHelper::createPermission(RbacHelper::AUTHORIZED, 'Права авторизованного пользователя'));

        $userOperator = RbacMethodsHelper::createRole(RbacHelper::USER_OPERATOR, 'Оператор менеджера пользователей',
            RbacMethodsHelper::createPermission(RbacHelper::USER_EDIT, 'Редактирование пользователей'));

        $roleOperator = RbacMethodsHelper::createRole(RbacHelper::ROLE_OPERATOR, 'Оператор менеджера ролей',
            RbacMethodsHelper::createPermission(RbacHelper::ROLE_EDIT, 'Редактирование ролей пользователя'));

        $basePodrazEdit = RbacMethodsHelper::createRole(RbacHelper::BASE_PODRAZ_EDIT, 'Оператор справочника "Подразделения"',
            RbacMethodsHelper::createPermission(RbacHelper::PODRAZ_EDIT, 'Редактирование справочника "Подразделения"'));

        $baseDolzhEdit = RbacMethodsHelper::createRole(RbacHelper::BASE_DOLZH_EDIT, 'Оператор справочника "Должности"',
            RbacMethodsHelper::createPermission(RbacHelper::DOLZH_EDIT, 'Редактирование справочника "Должности"'));

        $baseBuildEdit = RbacMethodsHelper::createRole(RbacHelper::BASE_BUILD_EDIT, 'Оператор справочника "Здания"',
            RbacMethodsHelper::createPermission(RbacHelper::BUILD_EDIT, 'Редактирование справочника "Здания"'));

        $baseAdministrator = RbacMethodsHelper::createRole(RbacHelper::BASE_ADMINISTRATOR, 'Администратор базовой конфигурации',
            [
                $authorized,
                $userOperator,
                $roleOperator,
                $basePodrazEdit,
                $baseDolzhEdit,
                $baseBuildEdit,
            ]);

        $administrator = RbacMethodsHelper::createRole(RbacHelper::ADMINISTRATOR, 'Администратор системы', $baseAdministrator);

        $person = Person::find()->where(['person_username' => 'admin'])->one();

        if ($person) {
            $auth = Yii::$app->authManager;

            if (!$auth->getAssignment($administrator->name, $person->primaryKey)) {
                $auth->assign($administrator, $person->primaryKey);
            }
        } else {
            throw new \Exception('user admin not exist');
        }

        AuthItem::updateAll(['view' => 1], ['not', ['name' => 'Administrator']]);
    }

    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll();
    }
}
