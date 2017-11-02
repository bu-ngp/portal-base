<?php
use console\helpers\RbacMethodsHelper;
use frontend\helpers\RbacHelper;
use console\helpers\RbacHelper as BaseRbacHelper;
use yii\db\Migration;

class m171102_042755_rbac extends Migration
{
    public function safeUp()
    {
        RbacMethodsHelper::createRole(RbacHelper::TILES_OPERATOR, 'Оператор плиток на главной странице',
            RbacMethodsHelper::createPermission(RbacHelper::TILES_EDIT, 'Редактирование плиток на главной странице'));

        RbacMethodsHelper::assignRole(BaseRbacHelper::ADMINISTRATOR, [RbacHelper::TILES_OPERATOR]);
    }

    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        $parent = $auth->getRole(BaseRbacHelper::ADMINISTRATOR);
        $child = $auth->getRole(RbacHelper::TILES_OPERATOR);
        $auth->removeChild($parent, $child);

        $parent = $auth->getRole(RbacHelper::TILES_OPERATOR);
        $child = $auth->getRole(RbacHelper::TILES_EDIT);
        $auth->removeChild($parent, $child);

        $auth->remove($auth->getRole(RbacHelper::TILES_OPERATOR));
        $auth->remove($auth->getRole(RbacHelper::TILES_EDIT));
    }
}
