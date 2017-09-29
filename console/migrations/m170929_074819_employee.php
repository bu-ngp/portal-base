<?php

class m170929_074819_employee extends yii\db\Migration
{
    public function safeUp()
    {
        $this->createTable('{{%dolzh}}', [
            'dolzh_id' => $this->binary(16)->notNull(),
            'dolzh_name' => $this->string()->notNull(),
        ]);

        $this->addPrimaryKey('dolzh_id', '{{%dolzh}}', 'dolzh_id');

        $this->createTable('{{%podraz}}', [
            'podraz_id' => $this->binary(16)->notNull(),
            'podraz_name' => $this->string()->notNull(),
        ]);

        $this->addPrimaryKey('podraz_id', '{{%podraz}}', 'podraz_id');

        $this->createTable('{{%build}}', [
            'build_id' => $this->binary(16)->notNull(),
            'build_name' => $this->string()->notNull(),
        ]);

        $this->addPrimaryKey('build_id', '{{%build}}', 'build_id');

        $this->createTable('{{%employee}}', [
            'employee_id' => $this->primaryKey(),
            'person_id' => $this->binary(16)->notNull(),
            'dolzh_id' => $this->binary(16)->notNull(),
            'podraz_id' => $this->binary(16)->notNull(),
            'build_id' => $this->binary(16)->null(),
            'employee_begin' => $this->date()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->string()->notNull(),
            'updated_by' => $this->string()->notNull(),
        ]);

        $this->addForeignKey('person_id_employee', '{{%employee}}', 'person_id', '{{%person}}', 'person_id');
        $this->addForeignKey('dolzh_id_employee', '{{%employee}}', 'dolzh_id', '{{%dolzh}}', 'dolzh_id');
        $this->addForeignKey('podraz_id_employee', '{{%employee}}', 'podraz_id', '{{%podraz}}', 'podraz_id');
        $this->addForeignKey('build_id_employee', '{{%employee}}', 'build_id', '{{%build}}', 'build_id');

        $this->createTable('{{%employee_history}}', [
            'employee_history_id' => $this->primaryKey(),
            'person_id' => $this->binary(16)->notNull(),
            'dolzh_id' => $this->binary(16)->notNull(),
            'podraz_id' => $this->binary(16)->notNull(),
            'build_id' => $this->binary(16)->null(),
            'employee_history_begin' => $this->date()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->string()->notNull(),
            'updated_by' => $this->string()->notNull(),
        ]);

        $this->addForeignKey('person_id_employee_history', '{{%employee_history}}', 'person_id', '{{%person}}', 'person_id');
        $this->addForeignKey('dolzh_id_employee_history', '{{%employee_history}}', 'dolzh_id', '{{%dolzh}}', 'dolzh_id');
        $this->addForeignKey('podraz_id_employee_history', '{{%employee_history}}', 'podraz_id', '{{%podraz}}', 'podraz_id');
        $this->addForeignKey('build_id_employee_history', '{{%employee_history}}', 'build_id', '{{%build}}', 'build_id');

        $this->createTable('{{%parttime}}', [
            'parttime_id' => $this->primaryKey(),
            'person_id' => $this->binary(16)->notNull(),
            'dolzh_id' => $this->binary(16)->notNull(),
            'podraz_id' => $this->binary(16)->notNull(),
            'build_id' => $this->binary(16)->null(),
            'parttime_begin' => $this->date()->notNull(),
            'parttime_end' => $this->date(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->string()->notNull(),
            'updated_by' => $this->string()->notNull(),
        ]);

        $this->addForeignKey('person_id_parttime', '{{%parttime}}', 'person_id', '{{%person}}', 'person_id');
        $this->addForeignKey('dolzh_id_parttime', '{{%parttime}}', 'dolzh_id', '{{%dolzh}}', 'dolzh_id');
        $this->addForeignKey('podraz_id_parttime', '{{%parttime}}', 'podraz_id', '{{%podraz}}', 'podraz_id');
        $this->addForeignKey('build_id_parttime', '{{%parttime}}', 'build_id', '{{%build}}', 'build_id');
    }

    public function safeDown()
    {
        $this->dropTable('{{%parttime}}');
        $this->dropTable('{{%employee_history}}');
        $this->dropTable('{{%employee}}');
        $this->dropTable('{{%dolzh}}');
        $this->dropTable('{{%podraz}}');
        $this->dropTable('{{%build}}');
    }
}
