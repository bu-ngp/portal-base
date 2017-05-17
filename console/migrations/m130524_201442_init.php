<?php

use wartron\yii2uuid\helpers\Uuid;
use yii\base\InvalidConfigException;
use yii\db\Migration;
use yii\rbac\DbManager;

class m130524_201442_init extends Migration
{
    /**
     * @throws yii\base\InvalidConfigException
     * @return DbManager
     */
    protected function getAuthManager()
    {
        $authManager = Yii::$app->getAuthManager();
        if (!$authManager instanceof DbManager) {
            throw new InvalidConfigException('You should configure "authManager" component to use database before executing this migration.');
        }
        return $authManager;
    }

    protected function uuidCreate($table, $fieldName)
    {
        return $this->db->createCommand("ALTER TABLE $table ADD $fieldName BINARY(16) NOT NULL FIRST")->execute();
    }

    /**
     * @return bool
     */
    protected function isMSSQL()
    {
        return $this->db->driverName === 'mssql' || $this->db->driverName === 'sqlsrv' || $this->db->driverName === 'dblib';
    }

    public function safeUp()
    {
        $authManager = $this->getAuthManager();

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%profile}}', [
            'profile_inn' => $this->char(12),
            'profile_dr' => $this->date(),
            'profile_pol' => $this->boolean(),
            'profile_snils' => $this->char(11),
            'profile_address' => $this->string(400),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->uuidCreate('{{%profile}}', 'profile_id');

        $this->addPrimaryKey('profile_id', '{{%profile}}', 'profile_id');

        $this->createTable('{{%person}}', [
            'person_code' => $this->integer()->notNull(),
            'person_fullname' => $this->string()->notNull(),
            'person_username' => $this->string()->notNull()->unique(),
            'person_auth_key' => $this->char(32)->notNull(),
            'person_password_hash' => $this->string()->notNull(),
            'person_email' => $this->string(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->uuidCreate('{{%person}}', 'person_id');

        $this->addPrimaryKey('person_id', '{{%person}}', 'person_id');
        $this->addForeignKey('person_profile', '{{%person}}', 'person_id', '{{%profile}}', 'profile_id', $this->isMSSQL() ? null : 'CASCADE', $this->isMSSQL() ? null : 'CASCADE');

        $this->createTable($authManager->ruleTable, [
            'name' => $this->string(64)->notNull(),
            'data' => $this->binary(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'PRIMARY KEY (name)',
        ], $tableOptions);

        $this->createTable($authManager->itemTable, [
            'name' => $this->string(64)->notNull(),
            'type' => $this->smallInteger()->notNull(),
            'view' => $this->boolean()->notNull()->defaultValue(0),
            'description' => $this->text(),
            'rule_name' => $this->string(64),
            'data' => $this->binary(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('name', $authManager->itemTable, 'name');
        $this->createIndex('idx-auth_item-type', $authManager->itemTable, 'type');
        $this->addForeignKey('rule_name', $authManager->itemTable, 'rule_name', $authManager->ruleTable, 'name', $this->isMSSQL() ? null : 'SET NULL', $this->isMSSQL() ? null : 'CASCADE');

        $this->createTable($authManager->itemChildTable, [
            'parent' => $this->string(64)->notNull(),
            'child' => $this->string(64)->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('parent_child', $authManager->itemChildTable, ['parent', 'child']);
        $this->addForeignKey('parent', $authManager->itemChildTable, 'parent', $authManager->itemTable, 'name', $this->isMSSQL() ? null : 'CASCADE', $this->isMSSQL() ? null : 'CASCADE');
        $this->addForeignKey('child', $authManager->itemChildTable, 'child', $authManager->itemTable, 'name', $this->isMSSQL() ? null : 'CASCADE', $this->isMSSQL() ? null : 'CASCADE');

        $this->createTable($authManager->assignmentTable, [
            'item_name' => $this->string(64)->notNull(),
            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->uuidCreate($authManager->assignmentTable, 'user_id');

        $this->addPrimaryKey('item_name_user_id', $authManager->assignmentTable, ['item_name', 'user_id']);
        $this->addForeignKey('item_name', $authManager->assignmentTable, 'item_name', $authManager->itemTable, 'name', $this->isMSSQL() ? null : 'CASCADE', $this->isMSSQL() ? null : 'CASCADE');
        $this->addForeignKey('user_id', $authManager->assignmentTable, 'user_id', '{{%person}}', 'person_id');

        $uuid1 = Uuid::uuid1();

        $this->insert('{{%profile}}', [
            'profile_id' => $uuid1,
            'profile_inn' => null,
            'profile_dr' => null,
            'profile_pol' => null,
            'profile_snils' => null,
            'profile_address' => null,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert('{{%person}}', [
            'person_id' => $uuid1,
            'person_code' => 1,
            'person_fullname' => 'Администратор',
            'person_username' => 'admin',
            'person_auth_key' => Yii::$app->security->generateRandomString(),
            'person_password_hash' => Yii::$app->security->generatePasswordHash('admin'),
            'person_email' => 'admin@mm.ru',
            'created_at' => time(),
            'updated_at' => time(),
        ]);
    }

    public function safeDown()
    {
        $authManager = $this->getAuthManager();
        $this->db = $authManager->db;

        if ($this->isMSSQL()) {
            $this->execute('DROP TRIGGER dbo.trigger_auth_item_child;');
        }

        $this->dropTable($authManager->assignmentTable);
        $this->dropTable($authManager->itemChildTable);
        $this->dropTable($authManager->itemTable);
        $this->dropTable($authManager->ruleTable);

        $this->dropTable('{{%person}}');
        $this->dropTable('{{%profile}}');
    }
}
