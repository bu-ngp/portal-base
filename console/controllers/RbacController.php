<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 12.05.2017
 * Time: 19:08
 */
namespace console\controllers;

use common\models\Person;
use console\helpers\RbacHelper;
use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;

        $userEdit = $auth->createPermission(RbacHelper::USER_EDIT);
        $userEdit->description = 'Редактирование пользователей';
        $auth->add($userEdit);

        $userOperator = $auth->createRole(RbacHelper::USER_OPERATOR);
        $userOperator->description = 'Оператор менеджера пользователей';
        $auth->add($userOperator);
        $auth->addChild($userOperator, $userEdit);

        $roleEdit = $auth->createPermission(RbacHelper::ROLE_EDIT);
        $roleEdit->description = 'Редактирование ролей пользователя';
        $auth->add($roleEdit);

        $roleOperator = $auth->createRole(RbacHelper::ROLE_OPERATOR);
        $roleOperator->description = 'Оператор менеджера ролей';
        $auth->add($roleOperator);
        $auth->addChild($roleOperator, $roleEdit);

        $podrazEdit = $auth->createPermission(RbacHelper::PODRAZ_EDIT);
        $podrazEdit->description = 'Редактирование справочника "Подразделения"';
        $auth->add($podrazEdit);

        $basePodrazEdit = $auth->createRole(RbacHelper::BASE_PODRAZ_EDIT);
        $basePodrazEdit->description = 'Оператор справочника "Подразделения"';
        $auth->add($basePodrazEdit);
        $auth->addChild($basePodrazEdit, $podrazEdit);

        $dolzhEdit = $auth->createPermission(RbacHelper::DOLZH_EDIT);
        $dolzhEdit->description = 'Редактирование справочника "Должности"';
        $auth->add($dolzhEdit);

        $baseDolzhEdit = $auth->createRole(RbacHelper::BASE_DOLZH_EDIT);
        $baseDolzhEdit->description = 'Оператор справочника "Должности"';
        $auth->add($baseDolzhEdit);
        $auth->addChild($baseDolzhEdit, $dolzhEdit);

        $baseAdministrator = $auth->createRole(RbacHelper::BASE_ADMINISTRATOR);
        $baseAdministrator->description = 'Администратор базовой конфигурации';
        $auth->add($baseAdministrator);
        $auth->addChild($baseAdministrator, $userOperator);
        $auth->addChild($baseAdministrator, $roleOperator);
        $auth->addChild($baseAdministrator, $basePodrazEdit);
        $auth->addChild($baseAdministrator, $baseDolzhEdit);

        $administrator = $auth->createRole(RbacHelper::ADMINISTRATOR);
        $administrator->description = 'Администратор системы';
        $auth->add($administrator);
        $auth->addChild($administrator, $baseAdministrator);

        $person = Person::find()->where(['person_username' => 'admin'])->one();
        if ($person) {
            $auth->assign($administrator, $person->primaryKey);
        }
    }
}