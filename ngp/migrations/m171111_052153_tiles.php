<?php

use console\classes\mysql\Migration;

/**
 * Class m171111_052153_tiles
 */
class m171111_052153_tiles extends Migration
{
    public function up()
    {
        $this->createTable('{{%tiles}}', [
            'tiles_id' => $this->primaryKey(),
            'tiles_name' => $this->string()->notNull(),
            'tiles_description' => $this->string(400),
            'tiles_link' => $this->string()->notNull(),
            'tiles_thumbnail' => $this->string(),
            'tiles_icon' => $this->string(),
            'tiles_icon_color' => $this->string(),
            'tiles_keywords' => $this->string(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->string()->notNull(),
            'updated_by' => $this->string()->notNull(),
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%tiles}}');
    }
}
