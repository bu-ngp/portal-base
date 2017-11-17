<?php

use yii\db\Migration;

/**
 * Class m171104_052922_doh
 */
class m171104_052922_doh extends Migration
{
    public function up()
    {
        $this->createTable('{{%doh_files}}', [
            'doh_files_id' => $this->primaryKey(),
            'file_type' => $this->string()->notNull(),
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
            'handler_done_time' => $this->float(),
            'handler_used_memory' => $this->integer(),
            'handler_short_report' => $this->string(400),
            'handler_files' => $this->integer()->unsigned(),
        ]);

        $this->createTable('{{%handler_files}}', [
            'doh_files_id' => $this->integer()->notNull(),
            'handler_id' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey('handler_files_handler', '{{%handler_files}}', 'handler_id', '{{%handler}}', 'handler_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('handler_files_doh_files', '{{%handler_files}}', 'doh_files_id', '{{%doh_files}}', 'doh_files_id', 'CASCADE', 'CASCADE');
        $this->createIndex('idx_handler_files', '{{%handler_files}}', ['doh_files_id', 'handler_id'], true);
    }

    public function down()
    {
        $this->dropTable('{{%handler_files}}');
        $this->dropTable('{{%handler}}');
        $this->dropTable('{{%doh_files}}');
    }
}
