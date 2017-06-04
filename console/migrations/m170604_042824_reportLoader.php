<?php

use yii\db\Migration;

class m170604_042824_reportLoader extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%report_loader}}', [
            'rl_id' => $this->primaryKey()->unsigned(),
            'rl_process_id' => $this->string(64)->notNull(),
            'rl_report_id' => $this->string(64)->notNull(),
            'rl_report_filename' => $this->string()->notNull(),
            'rl_report_displayname' => $this->string()->notNull(),
            'rl_status' => $this->boolean()->unsigned()->defaultValue(1)->notNull(),
            'rl_percent' => $this->boolean()->unsigned()->defaultValue(0)->notNull(),
            'rl_start' => $this->integer()->notNull(),
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%report_loader}}');
    }
}
