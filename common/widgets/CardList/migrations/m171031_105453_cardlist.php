<?php

use yii\db\Migration;

class m171031_105453_cardlist extends Migration
{
    public function up()
    {
        $this->createTable('{{%cardlist}}', [
            'cardlist_id' => $this->primaryKey(),
            'cardlist_page' => $this->string()->notNull(),
            'cardlist_title' => $this->string()->notNull(),
            'cardlist_description' => $this->string(),
            'cardlist_style' => $this->string(),
            'cardlist_link' => $this->string()->notNull(),
            'cardlist_icon' => $this->string(),
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%cardlist}}');
    }
}
