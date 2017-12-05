<?php

use ngp\helpers\RbacHelper;
use console\helpers\RbacHelper as BaseRbacHelper;
use yii\db\Migration;

class m171031_113027_cardlist_data extends Migration
{
    public function safeUp()
    {
        $this->batchInsert('{{%cardlist}}', [
            'cardlist_page',
            'cardlist_title',
            'cardlist_description',
            'cardlist_style',
            'cardlist_link',
            'cardlist_icon',
            'cardlist_roles',
        ], [
            [
                'wkportal-backend|site/index',
                'Плитки на главной странице',
                'Добавление/Редактирование/Удаление плиток',
                'wk-yellow-style',
                'FrontendUrlManager[tiles]',
                'fa fa-table',
                RbacHelper::TILES_EDIT,
            ],
            [
                'wkportal-backend|site/index',
                'Портал ОФОМС',
                'Проверка полисов на портале ОФОМС. Прикрепление пациентов к врачам ЛПУ',
                'wk-blue-style',
                'FrontendUrlManager[ofoms]',
                'fa fa-users',
                RbacHelper::OFOMS_VIEW,
            ],
            [
                'wkportal-backend|configuration/config/index',
                'Настройки портала ОФОМС',
                '',
                'wk-blue-style',
                'FrontendUrlManager[ofoms-config]',
                'fa fa-wrench',
                BaseRbacHelper::ADMINISTRATOR,
            ],
        ]);
    }

    public function safeDown()
    {
        $this->delete('{{%cardlist}}', ['in', 'cardlist_page', [
            'wkportal-backend|site/index',
            'wkportal-backend|config/index',
        ]]);
    }
}
