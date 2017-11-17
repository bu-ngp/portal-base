<?php

use console\classes\mysql\Migration;

class m170929_074819_employee extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%dolzh}}', [
            'dolzh_id' => $this->baseBinary()->notNull(),
            'dolzh_name' => $this->string()->notNull()->unique(),
        ]);

        $this->addPrimaryKey('dolzh_id', '{{%dolzh}}', 'dolzh_id');

        /*==============================*/

        $this->createTable('{{%podraz}}', [
            'podraz_id' => $this->baseBinary()->notNull(),
            'podraz_name' => $this->string()->notNull()->unique(),
        ]);

        $this->addPrimaryKey('podraz_id', '{{%podraz}}', 'podraz_id');

        /*==============================*/

        $this->createTable('{{%build}}', [
            'build_id' => $this->baseBinary()->notNull(),
            'build_name' => $this->string()->notNull()->unique(),
        ]);

        $this->addPrimaryKey('build_id', '{{%build}}', 'build_id');

        /*==============================*/

        $this->createTable('{{%employee}}', [
            'employee_id' => $this->primaryKey(),
            'person_id' => $this->baseBinary()->notNull()->unique(),
            'dolzh_id' => $this->baseBinary()->notNull(),
            'podraz_id' => $this->baseBinary()->notNull(),
            'employee_begin' => $this->date()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->string()->notNull(),
            'updated_by' => $this->string()->notNull(),
        ]);

        $this->addForeignKey('person_id_employee', '{{%employee}}', 'person_id', '{{%person}}', 'person_id', 'CASCADE');
        $this->addForeignKey('dolzh_id_employee', '{{%employee}}', 'dolzh_id', '{{%dolzh}}', 'dolzh_id');
        $this->addForeignKey('podraz_id_employee', '{{%employee}}', 'podraz_id', '{{%podraz}}', 'podraz_id');

        /*==============================*/

        $this->createTable('{{%employee_history}}', [
            'employee_history_id' => $this->primaryKey(),
            'person_id' => $this->baseBinary()->notNull(),
            'dolzh_id' => $this->baseBinary()->notNull(),
            'podraz_id' => $this->baseBinary()->notNull(),
            'employee_history_begin' => $this->date()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->string()->notNull(),
            'updated_by' => $this->string()->notNull(),
        ]);

        $this->createIndex('idx_employee_history', '{{%employee_history}}', ['person_id', 'employee_history_begin'], true);

        $this->addForeignKey('person_id_employee_history', '{{%employee_history}}', 'person_id', '{{%person}}', 'person_id', 'CASCADE');
        $this->addForeignKey('dolzh_id_employee_history', '{{%employee_history}}', 'dolzh_id', '{{%dolzh}}', 'dolzh_id');
        $this->addForeignKey('podraz_id_employee_history', '{{%employee_history}}', 'podraz_id', '{{%podraz}}', 'podraz_id');

        /*==============================*/

        $this->createTable('{{%employee_history_build}}', [
            'ehb_id' => $this->primaryKey(),
            'employee_history_id' => $this->integer()->notNull(),
            'build_id' => $this->baseBinary()->notNull(),
            'employee_history_build_deactive' => $this->date(),
        ]);

        $this->createIndex('idx_employee_history_build', '{{%employee_history_build}}', ['employee_history_id', 'build_id'], true);

        $this->addForeignKey('employee_history_id_employee_history_build', '{{%employee_history_build}}', 'employee_history_id', '{{%employee_history}}', 'employee_history_id', 'CASCADE');
        $this->addForeignKey('build_id_employee_history_build', '{{%employee_history_build}}', 'build_id', '{{%build}}', 'build_id');

        /*==============================*/

        $this->createTable('{{%parttime}}', [
            'parttime_id' => $this->primaryKey(),
            'person_id' => $this->baseBinary()->notNull(),
            'dolzh_id' => $this->baseBinary()->notNull(),
            'podraz_id' => $this->baseBinary()->notNull(),
            'parttime_begin' => $this->date()->notNull(),
            'parttime_end' => $this->date(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->string()->notNull(),
            'updated_by' => $this->string()->notNull(),
        ]);

        $this->addForeignKey('person_id_parttime', '{{%parttime}}', 'person_id', '{{%person}}', 'person_id', 'CASCADE');
        $this->addForeignKey('dolzh_id_parttime', '{{%parttime}}', 'dolzh_id', '{{%dolzh}}', 'dolzh_id');
        $this->addForeignKey('podraz_id_parttime', '{{%parttime}}', 'podraz_id', '{{%podraz}}', 'podraz_id');

        $this->createIndex('idx_parttime', '{{%parttime}}', ['person_id', 'dolzh_id', 'podraz_id', 'parttime_begin', 'parttime_end'], true);

        /*==============================*/

        $this->createTable('{{%parttime_build}}', [
            'pb' => $this->primaryKey(),
            'parttime_id' => $this->integer()->notNull(),
            'build_id' => $this->baseBinary()->notNull(),
            'parttime_build_deactive' => $this->date(),
        ]);

        $this->createIndex('idx_parttime_build', '{{%parttime_build}}', ['parttime_id', 'build_id'], true);

        $this->addForeignKey('parttime_id_parttime_build', '{{%parttime_build}}', 'parttime_id', '{{%parttime}}', 'parttime_id', 'CASCADE');
        $this->addForeignKey('build_id_parttime_build', '{{%parttime_build}}', 'build_id', '{{%build}}', 'build_id');
    }

    public function safeDown()
    {
        $this->dropTable('{{%parttime_build}}');
        $this->dropTable('{{%parttime}}');
        $this->dropTable('{{%employee_history_build}}');
        $this->dropTable('{{%employee_history}}');
        $this->dropTable('{{%employee}}');
        $this->dropTable('{{%dolzh}}');
        $this->dropTable('{{%podraz}}');
        $this->dropTable('{{%build}}');
    }
}
