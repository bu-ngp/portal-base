<?php

use console\helpers\RbacMethodsHelper;
use ngp\helpers\RbacHelper;
use console\helpers\RbacHelper as BaseRbacHelper;
use yii\db\Migration;

class m171102_042755_rbac extends Migration
{
    public function safeUp()
    {
        RbacMethodsHelper::createRole(RbacHelper::TILES_OPERATOR, 'Оператор плиток на главной странице',
            RbacMethodsHelper::createPermission(RbacHelper::TILES_EDIT, 'Редактирование плиток на главной странице'));

        RbacMethodsHelper::createRole(RbacHelper::NGP_OFOMS_VIEW, 'Проверка полисов на портале ОФОМС',
            RbacMethodsHelper::createPermission(RbacHelper::OFOMS_VIEW, 'Разрешение проверки полисов на портале ОФОМС'));

        RbacMethodsHelper::createRole(RbacHelper::NGP_OFOMS_PRIK, 'Прикрепление пациентов к врачам ЛПУ на портале ОФОМС',
            RbacMethodsHelper::createPermission(RbacHelper::OFOMS_PRIK, 'Разрешение прикрепления пациентов к врачам ЛПУ на портале ОФОМС'));

        RbacMethodsHelper::createRole(RbacHelper::NGP_OFOMS_PRIK_LIST, 'Пакетное прикрепление списком пациентов к врачам ЛПУ на портале ОФОМС',
            RbacMethodsHelper::createPermission(RbacHelper::OFOMS_PRIK_LIST, 'Разрешение прикрепления списком пациентов к врачам ЛПУ на портале ОФОМС'));

        RbacMethodsHelper::assignRole(BaseRbacHelper::ADMINISTRATOR, [
            RbacHelper::TILES_OPERATOR,
            RbacHelper::NGP_OFOMS_VIEW,
            RbacHelper::NGP_OFOMS_PRIK,
            RbacHelper::NGP_OFOMS_PRIK_LIST,
        ]);
    }

    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll();
    }
}
