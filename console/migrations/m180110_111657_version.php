<?php

use yii\db\Migration;

/**
 * Class m180110_111657_version
 */
class m180110_111657_version extends Migration
{
    public function up()
    {
        $this->createTable('{{%version}}', [
            'version' => $this->string()->unique(),
        ]);

        $this->insert('{{%version}}', [
            'version' => 'v1.0.0',
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%version}}');
    }
}
