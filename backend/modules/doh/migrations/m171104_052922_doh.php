<?php

use yii\db\Migration;

/**
 * Class m171104_052922_doh
 */
class m171104_052922_doh extends Migration
{
    public function up()
    {
        $this->createTable('{{%handler_files}}', [
            'handler_files_id' => $this->primaryKey(),
            'handler_id' => $this->integer()->notNull(),
            'file_type' => $this->boolean()->notNull(),
            'file_path' => $this->string(400)->notNull(),
            'file_description' => $this->string(400)->notNull(),
        ]);

        $this->createTable('{{%handler}}', [
            'handler_id' => $this->primaryKey(),
            'identifier' => $this->string(64)->notNull(),
            'handler_name' => $this->string()->notNull(),
            'handler_description' => $this->string(400)->notNull(),
            'handler_at' => $this->integer()->notNull(),
            'handler_percent' => $this->integer()->unsigned()->defaultValue(0)->notNull(),
            'handler_status' => $this->boolean()->defaultValue(1)->notNull(),
            'handler_done_time' => $this->integer(),
            'handler_used_memory' => $this->integer(),
            'handler_short_report' => $this->string(400),
            'handler_files' => $this->integer()->unsigned(),
        ]);

        $this->addForeignKey('handler_files', '{{%handler_files}}', 'handler_id', '{{%handler}}', 'handler_id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%handler}}');
        $this->dropTable('{{%handler_files}}');
    }
}
