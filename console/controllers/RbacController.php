<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 12.05.2017
 * Time: 19:08
 */
namespace console\controllers;

use common\models\base\Person;
use console\helpers\RbacHelper;
use domain\models\base\AuthItem;
use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
    public function actionInit()
    {
        $userOperator = RbacHelper::createRole(RbacHelper::USER_OPERATOR, 'Оператор менеджера пользователей',
            RbacHelper::createPermission(RbacHelper::USER_EDIT, 'Редактирование пользователей'));

        $roleOperator = RbacHelper::createRole(RbacHelper::ROLE_OPERATOR, 'Оператор менеджера ролей',
            RbacHelper::createPermission(RbacHelper::ROLE_EDIT, 'Редактирование ролей пользователя'));

        $basePodrazEdit = RbacHelper::createRole(RbacHelper::BASE_PODRAZ_EDIT, 'Оператор справочника "Подразделения"',
            RbacHelper::createPermission(RbacHelper::PODRAZ_EDIT, 'Редактирование справочника "Подразделения"'));

        $baseDolzhEdit = RbacHelper::createRole(RbacHelper::BASE_DOLZH_EDIT, 'Оператор справочника "Должности"',
            RbacHelper::createPermission(RbacHelper::DOLZH_EDIT, 'Редактирование справочника "Должности"'));

        $baseAdministrator = RbacHelper::createRole(RbacHelper::BASE_ADMINISTRATOR, 'Администратор базовой конфигурации',
            [
                $userOperator,
                $roleOperator,
                $basePodrazEdit,
                $baseDolzhEdit,
            ]);

        $administrator = RbacHelper::createRole(RbacHelper::ADMINISTRATOR, 'Администратор системы', $baseAdministrator);

        $person = Person::find()->where(['person_username' => 'admin'])->one();

        if ($person) {
            $auth = Yii::$app->authManager;

            if (!$auth->getAssignment($administrator->name, $person->primaryKey)) {
                $auth->assign($administrator, $person->primaryKey);
            }
        } else {
            throw new \Exception('user admin not exist');
        }

        AuthItem::updateAll(['view' => 1], ['name' => 'Administrator']);
    }
}