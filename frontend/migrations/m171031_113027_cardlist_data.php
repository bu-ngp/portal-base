<?php

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
        ], [[
            'wkportal-backend|site/index',
            'Плитки на главной странице',
            'Добавление/Редактирование/Удаление плиток',
            'wk-yellow-style',
            '[configuration/tiles]',
            'list-alt',
        ]]);
    }

    public function safeDown()
    {
        $this->delete('{{%cardlist}}', ['cardlist_page' => 'wkportal-backend|site/index']);
    }
}
