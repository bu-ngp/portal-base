<?php

use frontend\helpers\RbacHelper;
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
                'FrontendUrlManager[tiles/index]',
                'list-alt',
                RbacHelper::TILES_EDIT,
            ],
            [
                'wkportal-backend|site/index',
                'Портал ОФОМС',
                'Проверка полисов на портале ОФОМС. Прикрепление пациентов к врачам ЛПУ',
                'wk-blue-style',
                'FrontendUrlManager[ofoms/index]',
                'list-alt',
                RbacHelper::OFOMS_VIEW,
            ]
        ]);
    }

    public function safeDown()
    {
        $this->delete('{{%cardlist}}', ['cardlist_page' => 'wkportal-backend|site/index']);
    }
}
